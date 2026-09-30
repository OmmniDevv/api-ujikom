@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Peminjaman', 'value'=>$stats['total'],        'color'=>'from-orange-500 to-red-600'],
            ['label'=>'Diajukan',         'value'=>$stats['diajukan'],     'color'=>'from-yellow-500 to-orange-500'],
            ['label'=>'Sedang Dipinjam',  'value'=>$stats['dipinjam'],     'color'=>'from-blue-500 to-cyan-500'],
            ['label'=>'Selesai',          'value'=>$stats['dikembalikan'] + $stats['telat'], 'color'=>'from-green-500 to-emerald-500'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card">
            <p class="text-xs text-white/50 mb-1">{{ $c['label'] }}</p>
            <p class="text-3xl font-bold text-white font-mono" data-counter="{{ $c['value'] }}">{{ $c['value'] }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-gradient-to-r {{ $c['color'] }}"></div>
        </div>
        @endforeach
    </div>

    {{-- Reputation Card --}}
    @php $tier = auth()->user()->tier_reputasi; @endphp
    <div class="glass-card p-5 border border-orange-500/20 bg-gradient-to-r from-orange-950/30 via-slate-900/40 to-slate-900/20">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 p-0.5 shadow-lg shadow-orange-500/20 flex items-center justify-center shrink-0">
                    <div class="w-full h-full rounded-2xl bg-slate-950/80 flex items-center justify-center text-2xl">
                        {{ $tier['icon'] }}
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Status Kredibilitas Peminjam</span>
                        <span class="badge {{ $tier['badge'] }} text-xs">{{ $tier['nama'] }}</span>
                    </div>
                    <h3 class="text-base font-bold text-white mt-0.5">{{ $tier['deskripsi'] }}</h3>
                    <p class="text-xs text-white/40">Batas maksimal peminjaman aktif: <strong class="text-orange-400">{{ $tier['kuota_max'] }} alat sekaligus</strong></p>
                </div>
            </div>
            <div class="sm:text-right flex sm:flex-col items-baseline sm:items-end justify-between border-t sm:border-t-0 border-white/10 pt-3 sm:pt-0 shrink-0">
                <span class="text-3xl font-extrabold text-white font-mono" data-counter="{{ auth()->user()->skor_reputasi ?? 100 }}">
                    {{ auth()->user()->skor_reputasi ?? 100 }}
                </span>
                <span class="text-xs text-orange-400 font-mono">Skor Reputasi (Max 150)</span>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('peminjam.katalog') }}" class="btn-fire flex items-center gap-2 px-5 py-3">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"/>
            </svg> Lihat Katalog Alat
        </a>
        <a href="{{ route('peminjam.peminjaman.create') }}" class="btn-ghost flex items-center gap-2 px-5 py-3">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Ajukan Peminjaman
        </a>
    </div>

    <div class="glass-card p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">Peminjaman Terakhir</h2>
            <a href="{{ route('peminjam.peminjaman.riwayat') }}" class="text-xs text-orange-400 hover:text-orange-300">Lihat semua →</a>
        </div>
        <div class="fire-divider"></div>
        @if($peminjamanSaya->isEmpty())
            <p class="text-center text-white/40 py-8 text-sm">Belum ada peminjaman.</p>
        @else
        <div class="space-y-2 mt-3">
            @foreach($peminjamanSaya as $p)
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/3">
                <div>
                    <p class="text-sm font-medium text-white">{{ $p->detailPinjam->count() }} alat dipinjam</p>
                    <p class="text-xs text-white/40 mt-0.5">{{ $p->tgl_pinjam->format('d M Y') }} → {{ $p->tgl_kembali_plan->format('d M Y') }}</p>
                </div>
                @if($p->status === 'diajukan')     <span class="badge badge-warning">Diajukan</span>
                @elseif($p->status === 'dipinjam') <span class="badge badge-info">Dipinjam</span>
                @elseif($p->status === 'dikembalikan') <span class="badge badge-success">Selesai</span>
                @else <span class="badge badge-danger">Telat</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
