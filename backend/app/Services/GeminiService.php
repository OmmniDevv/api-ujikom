<?php

namespace App\Services;

use App\Models\Alat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private array $candidateModels;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->candidateModels = [
            env('GEMINI_MODEL', 'gemini-3.5-flash-lite'),
            'gemini-3.5-flash',
            'gemini-3.8-flash',
            'gemini-flash-latest',
        ];
    }

    /**
     * Kirim percakapan ke Gemini AI dengan batas konteks peminjaman alat yang ketat.
     */
    public function chat(string $userMessage, array $conversationHistory = []): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'reply' => 'API Key Gemini belum dikonfigurasi di server. Silakan hubungi admin.',
            ];
        }

        // Ambil data inventaris alat yang tersedia saat ini
        $alats = Alat::with('kategori:id,nama_kategori')
            ->select('id', 'nama_alat', 'kategori_id', 'stok', 'status_kondisi', 'deskripsi')
            ->where('stok', '>', 0)
            ->where('status_kondisi', 'baik')
            ->get();

        $daftarAlatText = $alats->map(function ($a) {
            $kategori = $a->kategori?->nama_kategori ?? 'Umum';
            return "- {$a->nama_alat} (Kategori: {$kategori}, Stok: {$a->stok}, Deskripsi: {$a->deskripsi})";
        })->implode("\n");

        $systemInstruction = <<<INSTRUCTION
Anda adalah "SiPinjam AI" — Asisten Virtual Cerdas Laboratorium & Peminjaman Alat Sekolah.

=== ATURAN KETAT KONTEKS (WAJIB DIPATUHI) ===
1. Anda HANYA diperbolehkan menjawab dan berdiskusi mengenai:
   - Rekomendasi alat untuk praktikum, proyek siswa, multimedia, fotografi, robotik, jaringan, dll.
   - Pengecekan ketersediaan alat dan stok di inventaris SiPinjam.
   - Panduan cara penggunaan singkat atau SOP keselamatan pemakaian alat lab.
   - Kebijakan peminjaman, batas waktu, pengembalian, dan denda di SiPinjam.
2. DILARANG KERAS menjawab topik di luar konteks peminjaman alat sekolah (misal: politik, obrolan umum, resep masakan, tugas matematika umum, coding di luar konteks lab, hiburan bebas, dll).
3. Jika pengguna bertanya topik di luar konteks, TOLAK DENGAN RAMAH & SOPAN, contoh:
   "Maaf, saya adalah Asisten Virtual SiPinjam yang khusus melayani konsultasi peminjaman alat sekolah dan laboratorium. Ada yang bisa saya bantu terkait kebutuhan alat praktikum atau kegiatan Anda?"
4. Gunakan bahasa Indonesia yang ramah, profesional, ringkas, dan jelas.
5. Bila merekomendasikan alat, cantumkan nama alat yang persis dengan daftar inventaris kami di bawah ini:

=== DAFTAR INVENTARIS ALAT TERSEDIA SAAT INI ===
{$daftarAlatText}
INSTRUCTION;

        // Susun payload contents
        $contents = [];

        // Masukkan riwayat pesan jika ada (maksimal 6 riwayat terakhir)
        if (!empty($conversationHistory)) {
            $recentHistory = array_slice($conversationHistory, -6);
            foreach ($recentHistory as $msg) {
                if (isset($msg['role']) && isset($msg['text'])) {
                    $role = $msg['role'] === 'user' ? 'user' : 'model';
                    $contents[] = [
                        'role' => $role,
                        'parts' => [['text' => $msg['text']]],
                    ];
                }
            }
        }

        // Tambahkan pesan user terkini
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        // Lakukan request dengan fallback model jika terjadi spike
        $lastError = null;
        foreach ($this->candidateModels as $model) {
            try {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

                $payload = [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 600,
                    ]
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $this->apiKey,
                ])->timeout(12)->post($endpoint, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return [
                            'success' => true,
                            'reply' => trim($reply),
                            'model_used' => $model,
                        ];
                    }
                }

                $lastError = $response->body();
                Log::warning("Gemini model {$model} returned non-200: " . $response->status(), ['body' => $lastError]);

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::error("Gemini model {$model} exception: " . $e->getMessage());
            }
        }

        return [
            'success' => false,
            'reply' => 'Maaf, asisten AI sedang sibuk memproses permintaan. Silakan coba sesaat lagi.',
            'error' => $lastError,
        ];
    }
}
