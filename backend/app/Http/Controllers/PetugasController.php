<?php

namespace App\Http\Controllers;

use App\Exports\LaporanPeminjamanExport;
use App\Models\Peminjaman;
use App\Services\ActivityLogger;
use App\Services\PeminjamanService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function __construct(private PeminjamanService $service) {}

    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function setujuiPeminjaman(Peminjaman $peminjaman)
    {
        try {
            $this->service->approve($peminjaman);

            ActivityLogger::log(
                'Approve Peminjaman',
                "Peminjaman #{$peminjaman->id} disetujui | Peminjam: {$peminjaman->user->name}"
            );

            return redirect()->back()->with('success', 'Peminjaman disetujui. Stok alat sudah di-reserve saat pengajuan.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyetujui peminjaman.');
        }
    }

    public function laporan()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'dikembalikan', 'telat'])
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('peminjamans'));
    }

    public function laporanPdf()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'dikembalikan', 'telat'])
            ->latest()
            ->get();

        $pdf = Pdf::loadView('petugas.laporan.pdf', compact('peminjamans'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-peminjaman-'.now()->format('Ymd-His').'.pdf');
    }

    public function laporanExcel()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn('status', ['dipinjam', 'dikembalikan', 'telat'])
            ->latest()
            ->get();

        $export = new LaporanPeminjamanExport($peminjamans);

        return $export->download('laporan-peminjaman-'.now()->format('Ymd-His').'.xlsx');
    }

    public function tolakPeminjaman(Peminjaman $peminjaman)
    {
        try {
            $namaAlat = $this->service->tolak($peminjaman);

            ActivityLogger::log(
                'Tolak Peminjaman',
                "Peminjaman #{$peminjaman->id} ditolak | Peminjam: {$peminjaman->user->name} | Stok dikembalikan: ".implode(', ', $namaAlat)
            );

            return redirect()->back()->with('success', 'Pengajuan peminjaman ditolak dan stok dikembalikan.');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak peminjaman.');
        }
    }
}
