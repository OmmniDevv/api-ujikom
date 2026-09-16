@extends('layouts.app')
@section('title', 'Detail Kategori — ' . $kategori->nama_kategori)
@section('page-title', 'Detail Kategori')
@section('page-subtitle', $kategori->nama_kategori)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Info Kategori --}}
    <div class="glass-card p-6">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-white">{{ $kategori->nama_kategori }}</h2>
                <p class="text-white/50 text-sm mt-1">{{ $kategori->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
            </div>
            <span class="badge badge-fire">{{ $kategori->alat->count() }} alat</span>
        </div>
        <div class="fire-divider"></div>
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Dibuat</p>
                <p class="text-white font-medium">{{ $kategori->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Diperbarui</p>
                <p class="text-white font-medium">{{ $kategori->updated_at->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Daftar Alat --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-4">Alat dalam Kategori ini</h3>
        <div class="fire-divider mb-4"></div>
        @forelse($kategori->alat as $alat)
        <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
            <div>
                <p class="text-white text-sm font-medium">{{ $alat->nama_alat }}</p>
                <p class="text-white/40 text-xs mt-0.5">Stok: {{ $alat->stok }} unit</p>
            </div>
            <span class="badge
                {{ $alat->status_kondisi === 'baik'      ? 'badge-success' : '' }}
                {{ $alat->status_kondisi === 'rusak'     ? 'badge-danger' : '' }}
                {{ $alat->status_kondisi === 'perbaikan' ? 'badge-warning' : '' }}
            ">{{ ucfirst($alat->status_kondisi) }}</span>
        </div>
        @empty
        <p class="text-white/40 text-sm text-center py-4">Belum ada alat dalam kategori ini.</p>
        @endforelse
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.kategoris.edit', $kategori) }}" class="btn-fire">Edit Kategori</a>
        <a href="{{ route('admin.kategoris.index') }}" class="btn-ghost">← Kembali</a>
    </div>
</div>
@endsection
