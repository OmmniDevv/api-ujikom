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
        $stats = [
            'total_user'       => User::count(),
            'total_alat'       => Alat::count(),
            'total_kategori'   => Kategori::count(),
            'total_peminjaman' => Peminjaman::count(),
            'diajukan'         => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam'         => Peminjaman::where('status', 'dipinjam')->count(),
            'dikembalikan'     => Peminjaman::where('status', 'dikembalikan')->count(),
        ];

        $logTerbaru = LogAktivitas::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'logTerbaru'));
    }
    public function petugas()
    {
        $stats = [
            'diajukan'     => Peminjaman::where('status', 'diajukan')->count(),
            'dipinjam'     => Peminjaman::where('status', 'dipinjam')->count(),
            'dikembalikan' => Peminjaman::where('status', 'dikembalikan')->count(),
            'telat'        => Peminjaman::where('status', 'telat')->count(),
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

        $stats = [
            'total'        => Peminjaman::where('user_id', $user->id)->count(),
            'diajukan'     => Peminjaman::where('user_id', $user->id)->where('status', 'diajukan')->count(),
            'dipinjam'     => Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count(),
            'dikembalikan' => Peminjaman::where('user_id', $user->id)->where('status', 'dikembalikan')->count(),
        ];

        return view('peminjam.dashboard', compact('peminjamanSaya', 'stats'));
    }
}
