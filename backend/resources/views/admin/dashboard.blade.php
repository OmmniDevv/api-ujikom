@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan data sistem')

@section('content')
<div class="space-y-6">

    {{-- Stat cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total User',       'value'=>$stats['total_user'],       'icon'=>'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'from-orange-500 to-red-600'],
            ['label'=>'Total Alat',        'value'=>$stats['total_alat'],        'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color'=>'from-amber-500 to-orange-600'],
            ['label'=>'Total Kategori',    'value'=>$stats['total_kategori'],    'icon'=>'M7 7h.01M7 3h5l7 7-7 7-5-5V3z', 'color'=>'from-red-500 to-rose-600'],
            ['label'=>'Total Peminjaman',  'value'=>$stats['total_peminjaman'],  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'from-rose-500 to-pink-600'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium mb-1">{{ $card['label'] }}</p>
                    <p class="text-3xl font-bold text-white">{{ $card['value'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-lg opacity-90">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Status peminjaman --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="glass-card p-5 text-center">
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['diajukan'] }}</p>
            <p class="text-xs text-white/50 mt-1">Menunggu Persetujuan</p>
            <span class="badge badge-warning mt-2">Diajukan</span>
        </div>
        <div class="glass-card p-5 text-center">
            <p class="text-2xl font-bold text-blue-400">{{ $stats['dipinjam'] }}</p>
            <p class="text-xs text-white/50 mt-1">Sedang Dipinjam</p>
            <span class="badge badge-info mt-2">Dipinjam</span>
        </div>
        <div class="glass-card p-5 text-center">
            <p class="text-2xl font-bold text-green-400">{{ $stats['dikembalikan'] }}</p>
            <p class="text-xs text-white/50 mt-1">Sudah Dikembalikan</p>
            <span class="badge badge-success mt-2">Selesai</span>
        </div>
    </div>

    {{-- Log aktivitas terbaru --}}
    <div class="glass-card p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">Log Aktivitas Terbaru</h2>
            <span class="badge badge-fire">{{ $logTerbaru->count() }} aktivitas</span>
        </div>
        <div class="fire-divider"></div>
        @if($logTerbaru->isEmpty())
            <p class="text-center text-white/40 py-6 text-sm">Belum ada aktivitas.</p>
        @else
            <div class="space-y-2 mt-3">
                @foreach($logTerbaru as $log)
                <div class="flex items-start gap-3 p-3 rounded-lg bg-white/3 hover:bg-white/5 transition">
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
                        {{ strtoupper(substr($log->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-white font-medium">{{ $log->aktivitas }}</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ $log->user?->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                        @if($log->keterangan)
                            <p class="text-xs text-orange-300/60 mt-0.5">{{ $log->keterangan }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
