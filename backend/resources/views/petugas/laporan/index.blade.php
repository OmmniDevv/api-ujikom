@extends('layouts.app')

@section('title', 'Laporan Peminjaman Alat')
@section('page-title', 'Laporan Peminjaman Alat')
@section('page-subtitle', 'Rekapitulasi seluruh riwayat transaksi peminjaman dan pengembalian')

@section('content')
<div class="space-y-6">

    {{-- ===== STATISTIK RINGKASAN LAPORAN ===== --}}
    @php
        $totalData = $peminjamans->count();
        $totalDipinjam = $peminjamans->where('status', 'dipinjam')->count();
        $totalSelesai = $peminjamans->where('status', 'dikembalikan')->count();
        $totalTelat = $peminjamans->where('status', 'telat')->count();
        $totalDenda = $peminjamans->sum(fn($p) => $p->pengembalian?->denda ?? 0);
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 print:hidden">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/50 mb-1">Total Peminjaman</p>
                    <p class="text-2xl font-bold text-white">{{ $totalData }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-500/20 text-orange-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 w-8 h-1 rounded-full bg-orange-500"></div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/50 mb-1">Sedang Dipinjam</p>
                    <p class="text-2xl font-bold text-blue-400">{{ $totalDipinjam }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 w-8 h-1 rounded-full bg-blue-500"></div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/50 mb-1">Selesai (Tepat Waktu)</p>
                    <p class="text-2xl font-bold text-green-400">{{ $totalSelesai }}</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-green-500/20 text-green-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 w-8 h-1 rounded-full bg-green-500"></div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-white/50 mb-1">Total Denda (Telat: {{ $totalTelat }})</p>
                    <p class="text-xl font-bold {{ $totalDenda > 0 ? 'text-red-400' : 'text-green-400' }}">
                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-2 w-8 h-1 rounded-full bg-red-500"></div>
        </div>
    </div>

    {{-- ===== ACTION TOOLBAR (EXPORT BUTTONS) ===== --}}
    <div class="glass-card p-4 flex flex-col sm:flex-row items-center justify-between gap-3 print:hidden">
        <div class="text-xs text-white/60">
            <span>Menampilkan <strong class="text-white">{{ $totalData }}</strong> transaksi peminjaman</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Tombol Export PDF --}}
            <a href="{{ route('petugas.laporan.pdf') }}"
               class="btn-danger flex items-center gap-2 px-4 py-2 text-xs font-semibold shadow-lg shadow-red-900/20 transition hover:scale-105"
               title="Unduh laporan dalam format PDF siap cetak">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                Unduh PDF
            </a>

            {{-- Tombol Export Excel --}}
            <a href="{{ route('petugas.laporan.excel') }}"
               class="btn-ghost flex items-center gap-2 px-4 py-2 text-xs font-semibold bg-emerald-600/30 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/50 shadow-lg shadow-emerald-900/20 transition hover:scale-105"
               title="Unduh laporan dalam format spreadsheet Excel .xlsx">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh Excel
            </a>

            {{-- Tombol Print Browser --}}
            <button onclick="window.print()"
                    class="btn-fire flex items-center gap-2 px-4 py-2 text-xs font-semibold transition hover:scale-105">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak / Print
            </button>
        </div>
    </div>

    {{-- ===== PRINT ONLY HEADER (Hanya muncul saat dicetak ke printer / PDF browser) ===== --}}
    <div class="hidden print:block mb-6 text-center text-black">
        <h1 class="text-xl font-bold uppercase tracking-wider">Laporan Peminjaman Alat</h1>
        <p class="text-sm">SMKN 7 Baleendah — Sistem Informasi Inventaris & Peminjaman</p>
        <p class="text-xs text-gray-500 mt-1">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB | Total Data: {{ $totalData }}</p>
        <hr class="my-3 border-gray-400">
    </div>

    {{-- ===== TABEL LAPORAN LENGKAP ===== --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-glass w-full text-xs">
                <thead>
                    <tr>
                        <th class="text-center w-12">No</th>
                        <th>Peminjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Alat yang Dipinjam</th>
                        <th>Tgl Kembali Aktual</th>
                        <th class="text-center">Kondisi</th>
                        <th class="text-right">Denda</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamans as $i => $item)
                    @php
                        $tglPinjam = $item->tgl_pinjam instanceof \Carbon\Carbon
                            ? $item->tgl_pinjam->format('d/m/Y')
                            : $item->tgl_pinjam;
                        $tglPlan = $item->tgl_kembali_plan instanceof \Carbon\Carbon
                            ? $item->tgl_kembali_plan->format('d/m/Y')
                            : $item->tgl_kembali_plan;
                        $tglAktual = $item->pengembalian?->tgl_kembali instanceof \Carbon\Carbon
                            ? $item->pengembalian->tgl_kembali->format('d/m/Y')
                            : ($item->pengembalian?->tgl_kembali ?? '—');
                        $kondisi = $item->pengembalian?->kondisi_kembali ?? '—';
                        $denda = $item->pengembalian?->denda ?? 0;
                    @endphp
                    <tr class="align-top hover:bg-white/5 transition">
                        <td class="text-center text-white/40 font-mono">{{ $i + 1 }}</td>
                        <td>
                            <p class="font-medium text-white">{{ $item->user->name ?? 'User Dihapus' }}</p>
                            <p class="text-[11px] text-white/40">{{ $item->user->email ?? '—' }}</p>
                        </td>
                        <td class="text-white/70 whitespace-nowrap">{{ $tglPinjam }}</td>
                        <td class="text-white/70 whitespace-nowrap">{{ $tglPlan }}</td>
                        <td>
                            <div class="space-y-1">
                                @foreach($item->detailPinjam as $detail)
                                    <div class="flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-orange-400"></span>
                                        <span class="font-medium text-white/90">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                        <span class="badge badge-gray text-[10px] px-1.5 py-0.5">{{ $detail->jumlah }} unit</span>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                        <td class="text-white/70 whitespace-nowrap">{{ $tglAktual }}</td>
                        <td class="text-center">
                            @if($kondisi === 'baik')
                                <span class="badge badge-success text-[10px]">Baik</span>
                            @elseif($kondisi === 'rusak')
                                <span class="badge badge-danger text-[10px]">Rusak</span>
                            @elseif($kondisi === 'perbaikan')
                                <span class="badge badge-warning text-[10px]">Perbaikan</span>
                            @else
                                <span class="text-white/30">—</span>
                            @endif
                        </td>
                        <td class="text-right whitespace-nowrap font-mono {{ $denda > 0 ? 'text-red-400 font-semibold' : 'text-white/50' }}">
                            {{ $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : 'Rp 0' }}
                        </td>
                        <td class="text-center whitespace-nowrap">
                            @if($item->status === 'dipinjam')
                                <span class="badge badge-info">Dipinjam</span>
                            @elseif($item->status === 'dikembalikan')
                                <span class="badge badge-success">Dikembalikan</span>
                            @elseif($item->status === 'telat')
                                <span class="badge badge-danger">Telat</span>
                            @else
                                <span class="badge badge-warning">{{ ucfirst($item->status) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-white/40 py-12">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg class="w-8 h-8 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-sm">Belum ada data peminjaman yang dapat dilaporkan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- CSS KHUSUS PRINT BROWSER SUPAYA BERSIH SAAT DICETAK --}}
<style>
@media print {
    body {
        background: #ffffff !important;
        color: #111827 !important;
    }
    aside, header, .glass-sidebar, .glass-nav, .btn-fire, .btn-ghost, .btn-danger, .stat-card {
        display: none !important;
    }
    main {
        padding: 0 !important;
        overflow: visible !important;
    }
    .glass-card {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
        color: #111827 !important;
    }
    th, td {
        border: 1px solid #d1d5db !important;
        padding: 6px 8px !important;
        color: #111827 !important;
    }
    th {
        background-color: #f3f4f6 !important;
        font-weight: bold !important;
    }
    .badge {
        border: 1px solid #9ca3af !important;
        color: #111827 !important;
        background: transparent !important;
    }
}
</style>
@endsection
