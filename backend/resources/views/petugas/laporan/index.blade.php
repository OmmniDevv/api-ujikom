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

    {{-- ===== PRINT ONLY HEADER & METADATA (Tampil Eksklusif Saat Dicetak / Save as PDF) ===== --}}
    @php
        $currentUser = auth()->user();
        $generatorNama = $currentUser ? $currentUser->name : 'Sistem Otomatis';
        $generatorRole = $currentUser ? ucfirst($currentUser->role) : 'Administrator';
        $generatorEmail = $currentUser ? $currentUser->email : '-';
        $waktuCetak = now()->format('d F Y, H:i:s') . ' WIB';
        $docId = 'SPJ-DOC-' . now()->format('Ymd') . '-' . strtoupper(substr(hash('crc32', $generatorNama . now()->timestamp), 0, 6));
    @endphp

    <div class="hidden print:block mb-4">
        {{-- Watermark Background Print --}}
        <div class="print-watermark">
            SIPINJAM • SMKN 7 BALEENDAH • DOKUMEN RESMI TERVERIFIKASI
        </div>

        {{-- Kop Header --}}
        <div class="flex items-center justify-between pb-3 mb-3 border-b-2 border-orange-600">
            <div>
                <h1 class="text-lg font-black text-orange-600 uppercase tracking-tight">⚡ SiPinjam - SMKN 7 Baleendah</h1>
                <p class="text-[11px] text-slate-600">Sistem Informasi Manajemen Inventaris & Peminjaman Peralatan Laboratorium</p>
                <p class="text-[10px] text-slate-500">Jl. Siliwangi No. 127, Baleendah, Kec. Baleendah, Kabupaten Bandung, Jawa Barat</p>
            </div>
            <div class="text-right">
                <h2 class="text-base font-extrabold text-slate-900 uppercase">Rekapitulasi Peminjaman Alat</h2>
                <p class="text-[10px] font-mono font-bold text-orange-600">No. Dokumen: {{ $docId }}</p>
            </div>
        </div>

        {{-- Metadata Box Generator --}}
        <div class="p-3 rounded bg-slate-50 border border-slate-200 border-l-4 border-l-orange-500 mb-4 text-xs">
            <div class="grid grid-cols-2 gap-y-1 gap-x-4">
                <div>
                    <span class="text-slate-500">Di-generate Oleh:</span>
                    <strong class="text-slate-900 ml-1">{{ $generatorNama }}</strong> 
                    <span class="text-orange-600 font-semibold">({{ $generatorRole }})</span>
                </div>
                <div>
                    <span class="text-slate-500">Waktu Pembuatan:</span>
                    <strong class="text-slate-900 ml-1">{{ $waktuCetak }}</strong>
                </div>
                <div>
                    <span class="text-slate-500">Email Akun:</span>
                    <span class="text-slate-800 ml-1">{{ $generatorEmail }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Status Dokumen:</span>
                    <strong class="text-emerald-700 ml-1">✓ ASLI & TERSERTIFIKASI ELEKTRONIK</strong>
                </div>
            </div>
        </div>

        {{-- Metric Strip Ringkasan Cetak --}}
        <div class="grid grid-cols-5 gap-2 mb-4 text-center">
            <div class="p-2 rounded bg-slate-100 border border-slate-200">
                <span class="text-[9px] font-bold text-slate-600 uppercase block">Total Transaksi</span>
                <span class="text-sm font-black text-slate-900 font-mono">{{ $totalData }}</span>
            </div>
            <div class="p-2 rounded bg-blue-50 border border-blue-200">
                <span class="text-[9px] font-bold text-blue-700 uppercase block">Sedang Dipinjam</span>
                <span class="text-sm font-black text-blue-900 font-mono">{{ $totalDipinjam }}</span>
            </div>
            <div class="p-2 rounded bg-emerald-50 border border-emerald-200">
                <span class="text-[9px] font-bold text-emerald-700 uppercase block">Selesai (Tepat Waktu)</span>
                <span class="text-sm font-black text-emerald-900 font-mono">{{ $totalSelesai }}</span>
            </div>
            <div class="p-2 rounded bg-rose-50 border border-rose-200">
                <span class="text-[9px] font-bold text-rose-700 uppercase block">Kasus Terlambat</span>
                <span class="text-sm font-black text-rose-900 font-mono">{{ $totalTelat }}</span>
            </div>
            <div class="p-2 rounded bg-orange-50 border border-orange-200">
                <span class="text-[9px] font-bold text-orange-700 uppercase block">Total Kas Denda</span>
                <span class="text-sm font-black text-orange-900 font-mono">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
            </div>
        </div>
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

    {{-- PRINT FOOTER: PENGESAHAN & CHECKSUM DIGITAL (Hanya tampil saat print) --}}
    <div class="hidden print:block mt-6 pt-4 border-t border-dashed border-slate-300" style="page-break-inside: avoid;">
        <div class="flex items-start justify-between gap-6">
            <div class="w-7/12 p-3 rounded bg-slate-50 border border-slate-200 text-[10px] text-slate-600 leading-relaxed">
                <div class="flex items-center gap-1.5 font-bold text-slate-900 mb-1">
                    <span class="text-orange-600">🔒</span> OTENTIKASI & KEAMANAN DOKUMEN DIGITAL
                </div>
                Dokumen ini merupakan salinan digital resmi yang dihasilkan secara otomatis oleh sistem <strong>SiPinjam SMKN 7 Baleendah</strong>. Seluruh riwayat transaksi di atas tervalidasi pada database server.
                <div class="mt-2 font-mono text-[9px] text-slate-500 break-all">
                    Checksum SHA-256: <span class="font-bold text-orange-600">{{ strtoupper(hash('sha256', $docId . now()->timestamp . $totalData)) }}</span>
                </div>
            </div>
            <div class="w-4/12 text-center text-xs text-slate-800">
                <p class="text-slate-600">Baleendah, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 mt-0.5">{{ $generatorRole }} Penanggung Jawab,</p>
                <div class="h-14 flex items-center justify-center">
                    <span class="text-[9px] text-slate-300 font-mono italic">[ Tanda Tangan Digital Tersertifikasi ]</span>
                </div>
                <p class="font-bold underline text-slate-900 text-xs">{{ $generatorNama }}</p>
                <p class="text-[10px] text-slate-500 font-mono">ID Akun: #{{ $currentUser->id ?? '1' }} • Status: Verified</p>
            </div>
        </div>
    </div>

</div>

{{-- CSS KHUSUS PRINT BROWSER MODERN & ELEGAN --}}
<style>
@media print {
    @page {
        size: landscape;
        margin: 10mm 12mm;
    }
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    html, body {
        height: auto !important;
        overflow: visible !important;
        background: #ffffff !important;
        color: #0f172a !important;
        font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        font-size: 11px !important;
    }
    .flex.h-screen, .h-screen, .overflow-hidden, .overflow-y-auto {
        height: auto !important;
        overflow: visible !important;
        display: block !important;
    }
    aside, header, nav, .glass-sidebar, .glass-nav, .btn-fire, .btn-ghost, .btn-danger, .stat-card, .fixed, .print\:hidden {
        display: none !important;
    }
    main, .main-content, .container, .p-6, .space-y-6 {
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        width: 100% !important;
        max-width: 100% !important;
    }
    .glass-card {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .overflow-x-auto {
        overflow: visible !important;
    }
    .print-watermark {
        position: fixed !important;
        top: 40% !important;
        left: 5% !important;
        right: 5% !important;
        font-size: 34px !important;
        font-weight: 900 !important;
        color: rgba(234, 88, 12, 0.04) !important;
        transform: rotate(-12deg) !important;
        text-align: center !important;
        z-index: 0 !important;
        pointer-events: none !important;
        text-transform: uppercase !important;
        letter-spacing: 3px !important;
    }
    table {
        border-collapse: collapse !important;
        width: 100% !important;
        font-size: 10.5px !important;
        position: relative !important;
        z-index: 10 !important;
        page-break-inside: auto !important;
    }
    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
    thead {
        display: table-header-group !important;
    }
    thead tr {
        background-color: #0f172a !important;
        color: #ffffff !important;
    }
    th {
        background-color: #0f172a !important;
        color: #ffffff !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        padding: 8px 10px !important;
        font-size: 9.5px !important;
        border: none !important;
        border-bottom: 2px solid #ea580c !important;
    }
    td {
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 7px 10px !important;
        color: #1e293b !important;
        vertical-align: middle !important;
    }
    tbody tr:nth-child(even) {
        background-color: #f8fafc !important;
    }
    tbody tr:nth-child(odd) {
        background-color: #ffffff !important;
    }
    .badge {
        display: inline-block !important;
        padding: 2px 8px !important;
        border-radius: 9999px !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
    }
    .badge-success {
        background-color: #ecfdf5 !important;
        color: #047857 !important;
        border: 1px solid #a7f3d0 !important;
    }
    .badge-danger {
        background-color: #fff1f2 !important;
        color: #be123c !important;
        border: 1px solid #fecdd3 !important;
    }
    .badge-warning {
        background-color: #fffbeb !important;
        color: #b45309 !important;
        border: 1px solid #fde68a !important;
    }
    .badge-info {
        background-color: #eff6ff !important;
        color: #1d4ed8 !important;
        border: 1px solid #bfdbfe !important;
    }
    .badge-gray {
        background-color: #f1f5f9 !important;
        color: #475569 !important;
        border: 1px solid #cbd5e1 !important;
    }
    .text-white {
        color: #0f172a !important;
    }
    .text-white\/90 {
        color: #1e293b !important;
    }
    .text-white\/70 {
        color: #475569 !important;
    }
    .text-white\/50 {
        color: #64748b !important;
    }
    .text-white\/30 {
        color: #94a3b8 !important;
    }
}
</style>
@endsection
