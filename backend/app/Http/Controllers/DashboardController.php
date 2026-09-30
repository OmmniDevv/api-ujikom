<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        $peminjamanStats = Peminjaman::selectRaw("
            COUNT(*) as total_peminjaman,
            COALESCE(SUM(status = 'diajukan'), 0) as diajukan,
            COALESCE(SUM(status = 'dipinjam'), 0) as dipinjam,
            COALESCE(SUM(status = 'dikembalikan'), 0) as dikembalikan,
            COALESCE(SUM(status = 'telat'), 0) as telat
        ")->first();

        $stats = [
            'total_user' => User::count(),
            'total_alat' => Alat::count(),
            'total_kategori' => Kategori::count(),
            'total_peminjaman' => (int) ($peminjamanStats->total_peminjaman ?? 0),
            'diajukan' => (int) ($peminjamanStats->diajukan ?? 0),
            'dipinjam' => (int) ($peminjamanStats->dipinjam ?? 0),
            'dikembalikan' => (int) ($peminjamanStats->dikembalikan ?? 0),
            'telat' => (int) ($peminjamanStats->telat ?? 0),
        ];

        $logTerbaru = LogAktivitas::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'logTerbaru'));
    }

    public function petugas()
    {
        $peminjamanStats = Peminjaman::selectRaw("
            COALESCE(SUM(status = 'diajukan'), 0) as diajukan,
            COALESCE(SUM(status = 'dipinjam'), 0) as dipinjam,
            COALESCE(SUM(status = 'dikembalikan'), 0) as dikembalikan,
            COALESCE(SUM(status = 'telat'), 0) as telat
        ")->first();

        $stats = [
            'diajukan' => (int) ($peminjamanStats->diajukan ?? 0),
            'dipinjam' => (int) ($peminjamanStats->dipinjam ?? 0),
            'dikembalikan' => (int) ($peminjamanStats->dikembalikan ?? 0),
            'telat' => (int) ($peminjamanStats->telat ?? 0),
        ];

        $peminjamanTerbaru = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['diajukan', 'dipinjam'])
            ->latest()
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact('stats', 'peminjamanTerbaru'));
    }

    public function peminjam()
    {
        $user = auth()->user();

        $peminjamanSaya = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $peminjamanStats = Peminjaman::where('user_id', $user->id)
            ->selectRaw("
                COUNT(*) as total,
                COALESCE(SUM(status = 'diajukan'), 0) as diajukan,
                COALESCE(SUM(status = 'dipinjam'), 0) as dipinjam,
                COALESCE(SUM(status = 'dikembalikan'), 0) as dikembalikan,
                COALESCE(SUM(status = 'telat'), 0) as telat
            ")->first();

        $stats = [
            'total' => (int) ($peminjamanStats->total ?? 0),
            'diajukan' => (int) ($peminjamanStats->diajukan ?? 0),
            'dipinjam' => (int) ($peminjamanStats->dipinjam ?? 0),
            'dikembalikan' => (int) ($peminjamanStats->dikembalikan ?? 0),
            'telat' => (int) ($peminjamanStats->telat ?? 0),
        ];

        return view('peminjam.dashboard', compact('peminjamanSaya', 'stats'));
    }
}
