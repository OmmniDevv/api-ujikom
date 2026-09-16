@extends('layouts.app')
@section('title', 'Detail Alat — ' . $alat->nama_alat)
@section('page-title', 'Detail Alat')
@section('page-subtitle', $alat->nama_alat)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Info Alat --}}
    <div class="glass-card p-6">
        <div class="flex gap-5">
            {{-- Gambar --}}
            <div class="w-28 h-28 rounded-xl overflow-hidden flex-shrink-0 bg-white/5 flex items-center justify-center border border-white/10">
                @if($alat->gambar)
                    <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-10 h-10 text-white/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg>
                @endif
            </div>
            {{-- Detail --}}
            <div class="flex-1">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-white">{{ $alat->nama_alat }}</h2>
                        <p class="text-orange-400/70 text-sm mt-0.5">{{ $alat->kategori?->nama_kategori }}</p>
                    </div>
                    <span class="badge
                        {{ $alat->status_kondisi === 'baik'      ? 'badge-success' : '' }}
                        {{ $alat->status_kondisi === 'rusak'     ? 'badge-danger' : '' }}
                        {{ $alat->status_kondisi === 'perbaikan' ? 'badge-warning' : '' }}
                    ">{{ ucfirst($alat->status_kondisi) }}</span>
                </div>
                <p class="text-white/50 text-sm mt-2">{{ $alat->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        <div class="fire-divider mt-5"></div>

        <div class="grid grid-cols-3 gap-4 mt-4 text-sm">
            <div class="stat-card text-center">
                <p class="text-3xl font-bold fire-text">{{ $alat->stok }}</p>
                <p class="text-white/40 text-xs mt-1">Unit Tersedia</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-blue-400">{{ $alat->detailPinjam->count() }}</p>
                <p class="text-white/40 text-xs mt-1">Total Dipinjam</p>
            </div>
            <div class="stat-card text-center">
                <p class="text-3xl font-bold text-green-400">{{ $alat->created_at->format('Y') }}</p>
                <p class="text-white/40 text-xs mt-1">Tahun Masuk</p>
            </div>
        </div>
    </div>

    {{-- Riwayat Peminjaman --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-4">Riwayat Peminjaman Alat</h3>
        <div class="fire-divider mb-4"></div>
        <div class="overflow-x-auto">
            <table class="w-full table-glass">
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alat->detailPinjam()->with('peminjaman.user')->latest()->take(10)->get() as $d)
                    <tr>
                        <td>{{ $d->peminjaman?->user?->name ?? '—' }}</td>
                        <td>{{ $d->jumlah }} unit</td>
                        <td>{{ $d->peminjaman?->tgl_pinjam->format('d M Y') ?? '—' }}</td>
                        <td>
                            <span class="badge
                                {{ ($d->peminjaman?->status ?? '') === 'diajukan'     ? 'badge-warning' : '' }}
                                {{ ($d->peminjaman?->status ?? '') === 'dipinjam'     ? 'badge-info' : '' }}
                                {{ ($d->peminjaman?->status ?? '') === 'dikembalikan' ? 'badge-success' : '' }}
                                {{ ($d->peminjaman?->status ?? '') === 'telat'        ? 'badge-danger' : '' }}
                            ">{{ ucfirst($d->peminjaman?->status ?? '—') }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-white/40 py-6">Belum pernah dipinjam.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.alats.edit', $alat) }}" class="btn-fire">Edit Alat</a>
        <a href="{{ route('admin.alats.index') }}" class="btn-ghost">← Kembali</a>
    </div>
</div>
@endsection
