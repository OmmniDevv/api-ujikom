<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExecutiveAnalyticsController extends Controller
{
    public function index()
    {
        // 1. Metrik Ringkasan Utama
        $totalPeminjaman = Peminjaman::count();
        $totalDikembalikan = Peminjaman::where('status', 'dikembalikan')->count();
        $totalTelat = Peminjaman::where('status', 'telat')->count();
        $totalDenda = Pengembalian::sum('denda');

        $tingkatDisiplin = $totalPeminjaman > 0 
            ? round(($totalDikembalikan / max(1, ($totalDikembalikan + $totalTelat))) * 100, 1)
            : 100;

        $rataSkorReputasi = round(User::where('role', 'peminjam')->avg('skor_reputasi') ?? 100, 1);

        // 2. Top 5 Alat Paling Sering Dipinjam (Hot Assets)
        $topAlats = DetailPinjam::select('alat_id', DB::raw('SUM(jumlah) as total_dipinjam'), DB::raw('COUNT(DISTINCT peminjaman_id) as frekuensi_pinjam'))
            ->groupBy('alat_id')
            ->orderByDesc('total_dipinjam')
            ->with('alat.kategori')
            ->take(5)
            ->get();

        // 3. Top 5 Alat Mengendap (Idle Assets)
        $idleAlats = Alat::with('kategori')
            ->withCount('detailPinjam')
            ->orderBy('detail_pinjam_count', 'asc')
            ->orderBy('stok', 'desc')
            ->take(5)
            ->get();

        // 4. Kondisi Fisik Alat Lab
        $kondisiStat = [
            'baik' => Alat::where('status_kondisi', 'baik')->count(),
            'rusak' => Alat::where('status_kondisi', 'rusak')->count(),
            'perbaikan' => Alat::where('status_kondisi', 'perbaikan')->count(),
        ];

        // 5. Tren Peminjaman 6 Bulan Terakhir
        $trenBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $year = $date->year;
            $month = $date->month;
            $namaBulan = $date->translatedFormat('M Y');

            $count = Peminjaman::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $trenBulanan[] = [
                'bulan' => $namaBulan,
                'total' => $count,
            ];
        }

        $maxTren = max(array_column($trenBulanan, 'total')) ?: 1;

        // 6. Distribusi Reputasi Peminjam
        $distribusiReputasi = [
            'teladan' => User::where('role', 'peminjam')->where('skor_reputasi', '>=', 120)->count(),
            'kredibel' => User::where('role', 'peminjam')->whereBetween('skor_reputasi', [90, 119])->count(),
            'perhatian' => User::where('role', 'peminjam')->whereBetween('skor_reputasi', [60, 89])->count(),
            'rawan' => User::where('role', 'peminjam')->where('skor_reputasi', '<', 60)->count(),
        ];

        return view('admin.analitik.index', compact(
            'totalPeminjaman',
            'totalDikembalikan',
            'totalTelat',
            'totalDenda',
            'tingkatDisiplin',
            'rataSkorReputasi',
            'topAlats',
            'idleAlats',
            'kondisiStat',
            'trenBulanan',
            'maxTren',
            'distribusiReputasi'
        ));
    }
}
