<?php

namespace App\Http\Controllers;

use App\Http\Requests\Peminjaman\StorePeminjamanRequest;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Services\ActivityLogger;
use App\Services\PeminjamanService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function __construct(private PeminjamanService $service) {}

    public function index(Request $request)
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->search($request->get('search'))
            ->byStatus($request->get('status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    public function riwayat(Request $request)
    {
        $peminjamanList = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->byStatus($request->get('status'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('peminjam.peminjaman.riwayat', compact('peminjamanList'));
    }

    public function create()
    {
        $alats = Alat::where('status_kondisi', 'baik')
            ->where('stok', '>', 0)
            ->with('kategori')
            ->get();

        return view('peminjam.peminjaman.create', compact('alats'));
    }

    public function store(StorePeminjamanRequest $request)
    {
        try {
            $result = $this->service->ajukan(
                auth()->id(),
                $request->tgl_pinjam,
                $request->tgl_kembali_plan,
                $request->detail
            );

            if (! $result['success']) {
                return back()->with('error', $result['error']);
            }

            $p = $result['peminjaman'];
            ActivityLogger::log(
                'Ajukan Peminjaman',
                "Peminjaman #{$p->id} | Alat: ".implode(', ', $result['namaAlat']).
                " | Tgl Pinjam: {$request->tgl_pinjam} | Rencana Kembali: {$request->tgl_kembali_plan}"
            );

            return redirect()->route('peminjam.peminjaman.riwayat')
                ->with('success', 'Pengajuan berhasil dikirim. Menunggu persetujuan petugas.');

        } catch (\Exception $e) {
            ActivityLogger::logError('Gagal Ajukan Peminjaman', $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'detailPinjam.alat', 'pengembalian.petugas']);

        return view('petugas.peminjaman.show', compact('peminjaman'));
    }

    public function tolak(Peminjaman $peminjaman)
    {
        try {
            $namaAlat = $this->service->tolak($peminjaman);

            ActivityLogger::log(
                'Tolak Peminjaman',
                "Peminjaman #{$peminjaman->id} ditolak | Peminjam: {$peminjaman->user->name} | Stok dikembalikan: ".implode(', ', $namaAlat)
            );

            return redirect()->route('petugas.peminjaman.index')
                ->with('success', 'Peminjaman ditolak dan stok dikembalikan.');

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menolak peminjaman.');
        }
    }
}
