<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Services\ActivityLogger;
use App\Services\PengembalianService;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function __construct(private PengembalianService $service) {}

    public function index(Request $request)
    {
        $pengembalianList = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Pengajuan pengembalian dari user yang menunggu diproses (ACC)
        $pengajuanList = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'pengajuan_kembali')
            ->latest()
            ->get();

        return view('petugas.pengembalian.index', compact('pengembalianList', 'pengajuanList'));
    }

    public function create(Request $request)
    {
        $peminjamanAktif = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'pengajuan_kembali'])
            ->orderByRaw("FIELD(status, 'pengajuan_kembali', 'dipinjam')")
            ->get();

        $selectedPeminjaman = null;
        if ($request->has('peminjaman_id')) {
            $selectedPeminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])
                ->findOrFail($request->peminjaman_id);
        }

        return view('petugas.pengembalian.create', compact('peminjamanAktif', 'selectedPeminjaman'));
    }

    public function store(StorePengembalianRequest $request)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($request->peminjaman_id);

        try {
            $result = $this->service->proses(
                $peminjaman,
                $request->tgl_kembali,
                $request->kondisi_kembali,
                auth()->id(),
                $request->catatan,
                $request->denda_manual
            );

            $catatanExtra = $request->catatan ? ' | Catatan: '.$request->catatan : '';
            $namaPeminjam = $peminjaman->user?->name ?? 'User Dihapus';
            ActivityLogger::log(
                'Proses Pengembalian',
                "Peminjaman #{$peminjaman->id} | Peminjam: {$namaPeminjam}".
                ' | Alat: '.implode(', ', $result['namaAlat']).
                " | Kondisi: {$request->kondisi_kembali}".
                " | Terlambat: {$result['terlambatHari']} hari".
                ' | Denda: Rp '.number_format($result['denda']).$catatanExtra
            );

            $msg = 'Pengembalian berhasil diproses.';
            if ($result['denda'] > 0) {
                $msg .= ' Denda: Rp '.number_format($result['denda']);
            }

            return redirect()->route('petugas.pengembalian.index')->with('success', $msg);

        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            ActivityLogger::logError('Gagal Proses Pengembalian', $e->getMessage());

            return back()->with('error', 'Gagal memproses pengembalian. Silakan coba lagi.');
        }
    }

    public function show(Pengembalian $pengembalian)
    {
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas']);

        return view('petugas.pengembalian.show', compact('pengembalian'));
    }
}
