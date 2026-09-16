@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, {{ auth()->user()->name }}')

@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total Peminjaman', 'value'=>$stats['total'],        'color'=>'from-orange-500 to-red-600'],
            ['label'=>'Diajukan',         'value'=>$stats['diajukan'],     'color'=>'from-yellow-500 to-orange-500'],
            ['label'=>'Sedang Dipinjam',  'value'=>$stats['dipinjam'],     'color'=>'from-blue-500 to-cyan-500'],
            ['label'=>'Selesai',          'value'=>$stats['dikembalikan'], 'color'=>'from-green-500 to-emerald-500'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card">
            <p class="text-xs text-white/50 mb-1">{{ $c['label'] }}</p>
            <p class="text-3xl font-bold text-white">{{ $c['value'] }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-gradient-to-r {{ $c['color'] }}"></div>
        </div>
        @endforeach
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
