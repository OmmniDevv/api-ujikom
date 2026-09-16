@extends('layouts.app')
@section('title', 'Detail Pengembalian #' . $pengembalian->id)
@section('page-title', 'Detail Pengembalian')
@section('page-subtitle', 'ID #' . $pengembalian->id)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Status denda --}}
    @if($pengembalian->denda > 0)
    <div class="alert-error flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.07 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <div>
            <p class="font-semibold">Terlambat! Denda: Rp {{ number_format($pengembalian->denda) }}</p>
        </div>
    </div>
    @else
    <div class="alert-success flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="font-semibold">Pengembalian tepat waktu. Tidak ada denda.</p>
    </div>
    @endif

    {{-- Info Pengembalian --}}
    <div class="glass-card p-6">
        <h2 class="font-semibold text-white mb-4">Informasi Pengembalian</h2>
        <div class="fire-divider mb-4"></div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Peminjam</p>
                <p class="text-white font-medium">{{ $pengembalian->peminjaman?->user?->name }}</p>
                <p class="text-white/40 text-xs">{{ $pengembalian->peminjaman?->user?->email }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Tanggal Kembali</p>
                <p class="text-white font-medium">{{ $pengembalian->tgl_kembali->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Rencana Kembali</p>
                <p class="text-white font-medium">{{ $pengembalian->peminjaman?->tgl_kembali_plan->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Keterlambatan</p>
                @php
                    $terlambat = max(0, $pengembalian->tgl_kembali->diffInDays($pengembalian->peminjaman?->tgl_kembali_plan, false) * -1);
                @endphp
                <p class="font-medium {{ $terlambat > 0 ? 'text-red-400' : 'text-green-400' }}">
                    {{ $terlambat > 0 ? $terlambat . ' hari' : 'Tepat waktu' }}
                </p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Kondisi Alat</p>
                <span class="badge
                    {{ $pengembalian->kondisi_kembali === 'baik'      ? 'badge-success' : '' }}
                    {{ $pengembalian->kondisi_kembali === 'rusak'     ? 'badge-danger' : '' }}
                    {{ $pengembalian->kondisi_kembali === 'perbaikan' ? 'badge-warning' : '' }}
                ">{{ ucfirst($pengembalian->kondisi_kembali) }}</span>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Denda</p>
                <p class="text-xl font-bold {{ $pengembalian->denda > 0 ? 'text-red-400' : 'text-green-400' }}">
                    Rp {{ number_format($pengembalian->denda) }}
                </p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Diproses oleh</p>
                <p class="text-white font-medium">{{ $pengembalian->petugas?->name }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Waktu Proses</p>
                <p class="text-white font-medium">{{ $pengembalian->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Alat yang dikembalikan --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-4">Alat yang Dikembalikan</h3>
        <div class="fire-divider mb-4"></div>
        <div class="overflow-x-auto">
            <table class="w-full table-glass">
                <thead>
                    <tr>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Kondisi Sekarang</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengembalian->peminjaman?->detailPinjam ?? [] as $d)
                    <tr>
                        <td class="font-medium text-white">{{ $d->alat?->nama_alat }}</td>
                        <td class="text-white/60">{{ $d->alat?->kategori?->nama_kategori }}</td>
                        <td><span class="badge badge-fire">{{ $d->jumlah }} unit</span></td>
                        <td>
                            <span class="badge
                                {{ ($d->alat?->status_kondisi ?? '') === 'baik'      ? 'badge-success' : '' }}
                                {{ ($d->alat?->status_kondisi ?? '') === 'rusak'     ? 'badge-danger' : '' }}
                                {{ ($d->alat?->status_kondisi ?? '') === 'perbaikan' ? 'badge-warning' : '' }}
                            ">{{ ucfirst($d->alat?->status_kondisi ?? '—') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Peminjaman asal --}}
    <div class="glass-card p-5 border border-white/5">
        <p class="text-white/40 text-xs uppercase tracking-wider mb-3">Referensi Peminjaman</p>
        <div class="flex items-center justify-between">
            <div class="text-sm">
                <p class="text-white">Peminjaman #{{ $pengembalian->peminjaman?->id }}</p>
                <p class="text-white/40 text-xs">Tgl Pinjam: {{ $pengembalian->peminjaman?->tgl_pinjam->format('d M Y') }}</p>
            </div>
            <span class="badge badge-success">{{ ucfirst($pengembalian->peminjaman?->status) }}</span>
        </div>
    </div>

    <a href="{{ route('petugas.pengembalian.index') }}" class="btn-ghost inline-flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Daftar
    </a>
</div>
@endsection
