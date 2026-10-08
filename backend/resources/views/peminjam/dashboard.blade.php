@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
<div class="space-y-6">

    {{-- Stat cards dengan sparkline (ref: Glassy Dashboard reel) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Peminjaman', 'value'=>$stats['total'],        'color'=>'from-orange-500 to-red-600',   'spark'=>'M0,28 L12,24 L24,26 L36,18 L48,20 L60,12 L72,14 L84,6',  'sc'=>'#f97316'],
            ['label'=>'Diajukan',         'value'=>$stats['diajukan'],     'color'=>'from-yellow-500 to-orange-500', 'spark'=>'M0,22 L12,26 L24,18 L36,22 L48,14 L60,18 L72,10 L84,12', 'sc'=>'#fbbf24'],
            ['label'=>'Sedang Dipinjam',  'value'=>$stats['dipinjam'],     'color'=>'from-blue-500 to-cyan-500',     'spark'=>'M0,24 L12,20 L24,22 L36,14 L48,16 L60,8 L72,12 L84,4',   'sc'=>'#38bdf8'],
            ['label'=>'Selesai',          'value'=>$stats['dikembalikan'] + $stats['telat'], 'color'=>'from-green-500 to-emerald-500', 'spark'=>'M0,26 L12,22 L24,24 L36,16 L48,20 L60,14 L72,16 L84,8', 'sc'=>'#34d399'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card stat-glassy">
            <p class="text-xs text-white/50 font-medium mb-1">{{ $c['label'] }}</p>
            <p class="text-3xl font-extrabold text-white tracking-tight font-mono" data-counter="{{ $c['value'] }}">{{ $c['value'] }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-gradient-to-r {{ $c['color'] }}"></div>
            <svg class="spark" width="84" height="32" viewBox="0 0 84 32" fill="none">
                <path d="{{ $c['spark'] }}" stroke="{{ $c['sc'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.85"/>
            </svg>
        </div>
        @endforeach
    </div>

    {{-- Hero card reputasi (ref: kartu hero dashboard reel) --}}
    @php $tier = auth()->user()->tier_reputasi; @endphp
    <div class="glass-card p-6 border border-orange-500/20 bg-gradient-to-r from-orange-950/40 via-slate-900/50 to-slate-900/20 overflow-hidden relative">
        <div class="absolute -right-16 -top-16 w-64 h-64 rounded-full bg-orange-500/10 blur-3xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-5 relative">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-orange-500 to-amber-400 p-0.5 shadow-lg shadow-orange-500/20 flex items-center justify-center shrink-0">
                    <div class="w-full h-full rounded-2xl bg-slate-950/80 flex items-center justify-center text-2xl">
                        {{ $tier['icon'] }}
                    </div>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Status Kredibilitas Peminjam</span>
                        <span class="badge {{ $tier['badge'] }} text-xs">{{ $tier['nama'] }}</span>
                    </div>
                    <h3 class="text-lg font-bold text-white mt-1">{{ $tier['deskripsi'] }}</h3>
                    <p class="text-xs text-white/40 mt-0.5">Batas maksimal peminjaman aktif: <strong class="text-orange-400">{{ $tier['kuota_max'] }} alat sekaligus</strong></p>
                </div>
            </div>
            <div class="flex sm:flex-col items-center sm:items-end gap-3 shrink-0">
                <div class="text-center sm:text-right">
                    <span class="text-3xl font-extrabold text-white font-mono" data-counter="{{ auth()->user()->skor_reputasi ?? 100 }}">{{ auth()->user()->skor_reputasi ?? 100 }}</span>
                    <span class="text-xs text-orange-400 font-mono block">Skor Reputasi (Max 150)</span>
                </div>
                <a href="{{ route('peminjam.katalog') }}" class="btn-fire flex items-center gap-2 px-5 py-2.5 text-sm whitespace-nowrap">
                    Lihat Katalog
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="flex gap-3 flex-wrap">
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
            <div class="glass-table-row flex items-center justify-between p-3 rounded-xl">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500/30 to-red-600/30 border border-orange-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white">{{ $p->detailPinjam->count() }} alat dipinjam</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ $p->tgl_pinjam->format('d M Y') }} → {{ $p->tgl_kembali_plan->format('d M Y') }}</p>
                    </div>
                </div>
                @if($p->status === 'diajukan')     <span class="badge badge-warning flex-shrink-0">Diajukan</span>
                @elseif($p->status === 'dipinjam') <span class="badge badge-info flex-shrink-0">Dipinjam</span>
                @elseif($p->status === 'dikembalikan') <span class="badge badge-success flex-shrink-0">Selesai</span>
                @else <span class="badge badge-danger flex-shrink-0">Telat</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
