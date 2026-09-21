@extends('layouts.app')
@section('title', 'Detail Peminjaman')
@section('page-title', 'Detail Peminjaman')
@section('page-subtitle', 'Informasi lengkap peminjaman #{{ $peminjaman->id }}')

@section('content')
<div class="max-w-3xl space-y-4">

    <div class="glass-card p-5">
        <h2 class="font-semibold text-white mb-4">Informasi Peminjaman</h2>
        <div class="fire-divider"></div>
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div><p class="text-white/40">Peminjam</p><p class="text-white font-medium mt-1">{{ $peminjaman->user->name }}</p></div>
            <div><p class="text-white/40">Status</p>
                <div class="mt-1">
                    @if($peminjaman->status === 'diajukan')     <span class="badge badge-warning">Diajukan</span>
                    @elseif($peminjaman->status === 'dipinjam') <span class="badge badge-info">Dipinjam</span>
                    @elseif($peminjaman->status === 'dikembalikan') <span class="badge badge-success">Dikembalikan</span>
                    @else <span class="badge badge-danger">Telat</span>
                    @endif
                </div>
            </div>
            <div><p class="text-white/40">Tanggal Pinjam</p><p class="text-white mt-1">{{ $peminjaman->tgl_pinjam->format('d M Y') }}</p></div>
            <div><p class="text-white/40">Rencana Kembali</p><p class="text-white mt-1">{{ $peminjaman->tgl_kembali_plan->format('d M Y') }}</p></div>
        </div>
    </div>

    <div class="glass-card p-5">
        <h2 class="font-semibold text-white mb-4">Daftar Alat yang Dipinjam</h2>
        <div class="fire-divider"></div>
        <div class="space-y-2 mt-3">
            @foreach($peminjaman->detailPinjam as $detail)
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/3">
                <div class="flex items-center gap-3">
                    @if($detail->alat?->gambar)
                        <img src="{{ asset('storage/'.$detail->alat->gambar) }}" class="w-9 h-9 rounded-lg object-cover">
                    @else
                        <div class="w-9 h-9 rounded-lg bg-orange-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <p class="text-sm font-medium text-white">{{ $detail->alat?->nama_alat }}</p>
                        <p class="text-xs text-white/40">{{ $detail->alat?->kategori?->nama_kategori }}</p>
                    </div>
                </div>
                <span class="badge badge-fire">{{ $detail->jumlah }} unit</span>
            </div>
            @endforeach
        </div>
    </div>

    @if($peminjaman->pengembalian)
    <div class="glass-card p-5">
        <h2 class="font-semibold text-white mb-4">Info Pengembalian</h2>
        <div class="fire-divider"></div>
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
            <div><p class="text-white/40">Tanggal Kembali</p><p class="text-white mt-1">{{ $peminjaman->pengembalian->tgl_kembali->format('d M Y') }}</p></div>
            <div><p class="text-white/40">Kondisi</p><p class="text-white mt-1">{{ ucfirst($peminjaman->pengembalian->kondisi_kembali) }}</p></div>
            <div><p class="text-white/40">Denda</p>
                <p class="mt-1 {{ $peminjaman->pengembalian->denda > 0 ? 'text-red-400 font-semibold' : 'text-green-400' }}">
                    Rp {{ number_format($peminjaman->pengembalian->denda) }}
                </p>
            </div>
            <div><p class="text-white/40">Petugas</p><p class="text-white mt-1">{{ $peminjaman->pengembalian->petugas?->name }}</p></div>
        </div>
    </div>
    @endif

    <div class="flex gap-3">
        @if($peminjaman->status === 'diajukan')
            <form action="{{ route('petugas.peminjaman.setujui', $peminjaman) }}" method="POST">
                @csrf <button type="submit" class="btn-fire px-5 py-2.5">Setujui Peminjaman</button>
            </form>
            <form action="{{ route('petugas.peminjaman.tolak', $peminjaman) }}" method="POST"
                  onsubmit="return confirm('Tolak peminjaman ini?')">
                @csrf <button type="submit" class="btn-danger px-5 py-2.5">Tolak</button>
            </form>
        @elseif($peminjaman->status === 'dipinjam')
            <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $peminjaman->id]) }}"
               class="btn-fire px-5 py-2.5">Proses Pengembalian</a>
        @endif
        <a href="{{ route('petugas.peminjaman.index') }}" class="btn-ghost px-5 py-2.5">← Kembali</a>
    </div>
</div>
@endsection
