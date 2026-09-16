@extends('layouts.app')
@section('title', 'Detail Peminjaman #' . $peminjaman->id)
@section('page-title', 'Detail Peminjaman')
@section('page-subtitle', 'ID #' . $peminjaman->id)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Info Peminjaman --}}
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">Informasi Peminjaman</h2>
            <span class="badge
                {{ $peminjaman->status === 'diajukan'     ? 'badge-warning' : '' }}
                {{ $peminjaman->status === 'dipinjam'     ? 'badge-info' : '' }}
                {{ $peminjaman->status === 'dikembalikan' ? 'badge-success' : '' }}
                {{ $peminjaman->status === 'telat'        ? 'badge-danger' : '' }}
            ">{{ ucfirst($peminjaman->status) }}</span>
        </div>
        <div class="fire-divider"></div>
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Peminjam</p>
                <p class="text-white font-medium">{{ $peminjaman->user?->name }}</p>
                <p class="text-white/40 text-xs">{{ $peminjaman->user?->email }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Tanggal Pinjam</p>
                <p class="text-white font-medium">{{ $peminjaman->tgl_pinjam->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Rencana Kembali</p>
                <p class="text-white font-medium">{{ $peminjaman->tgl_kembali_plan->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Dibuat</p>
                <p class="text-white font-medium">{{ $peminjaman->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Detail Alat --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-4">Alat yang Dipinjam</h3>
        <div class="fire-divider mb-4"></div>
        <div class="space-y-2">
            @foreach($peminjaman->detailPinjam as $d)
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/3 text-sm">
                <div>
                    <p class="text-white font-medium">{{ $d->alat?->nama_alat }}</p>
                    <p class="text-white/40 text-xs">{{ $d->alat?->kategori?->nama_kategori }}</p>
                </div>
                <span class="badge badge-fire">{{ $d->jumlah }} unit</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Info Pengembalian (jika sudah dikembalikan) --}}
    @if($peminjaman->pengembalian)
    <div class="glass-card p-6 border border-green-500/20">
        <h3 class="font-semibold text-green-400 mb-4">Informasi Pengembalian</h3>
        <div class="fire-divider mb-4"></div>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Tanggal Kembali</p>
                <p class="text-white font-medium">{{ $peminjaman->pengembalian->tgl_kembali->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Kondisi Alat</p>
                <p class="text-white font-medium">{{ ucfirst($peminjaman->pengembalian->kondisi_kembali) }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Denda</p>
                <p class="font-semibold {{ $peminjaman->pengembalian->denda > 0 ? 'text-red-400' : 'text-green-400' }}">
                    Rp {{ number_format($peminjaman->pengembalian->denda) }}
                </p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Diproses oleh</p>
                <p class="text-white font-medium">{{ $peminjaman->pengembalian->petugas?->name }}</p>
            </div>
        </div>
    </div>
    @endif

    <a href="{{ route('admin.peminjaman.index') }}" class="btn-ghost inline-flex">← Kembali</a>
</div>
@endsection
