@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan data sistem')

@section('content')
<div class="space-y-6">

    {{-- Stat cards dengan sparkline (ref: Glassy Dashboard reel) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Total User',       'value'=>$stats['total_user'],       'icon'=>'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0z', 'color'=>'from-orange-500 to-red-600', 'spark'=>'M0,28 L12,24 L24,26 L36,18 L48,20 L60,12 L72,14 L84,6', 'sc'=>'#f97316'],
            ['label'=>'Total Alat',        'value'=>$stats['total_alat'],        'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color'=>'from-amber-500 to-orange-600', 'spark'=>'M0,22 L12,26 L24,18 L36,22 L48,14 L60,18 L72,10 L84,12', 'sc'=>'#fbbf24'],
            ['label'=>'Total Kategori',    'value'=>$stats['total_kategori'],    'icon'=>'M7 7h.01M7 3h5l7 7-7 7-5-5V3z', 'color'=>'from-red-500 to-rose-600', 'spark'=>'M0,26 L12,22 L24,24 L36,16 L48,20 L60,14 L72,16 L84,8', 'sc'=>'#f43f5e'],
            ['label'=>'Total Peminjaman',  'value'=>$stats['total_peminjaman'],  'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color'=>'from-rose-500 to-pink-600', 'spark'=>'M0,24 L12,20 L24,22 L36,14 L48,16 L60,8 L72,12 L84,4', 'sc'=>'#fb7185'],
        ];
        @endphp

        @foreach($cards as $card)
        <div class="stat-card stat-glassy">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-white/50 font-medium mb-1">{{ $card['label'] }}</p>
                    <p class="text-3xl font-extrabold text-white tracking-tight">{{ $card['value'] }}</p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-lg opacity-90 flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <svg class="spark" width="84" height="32" viewBox="0 0 84 32" fill="none">
                <path d="{{ $card['spark'] }}" stroke="{{ $card['sc'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.85"/>
            </svg>
        </div>
        @endforeach
    </div>

    {{-- Tren 7 hari + status --}}
    <div class="grid lg:grid-cols-3 gap-4">
        {{-- Bar chart tren peminjaman --}}
        <div class="glass-card chart-card lg:col-span-2">
            <div class="flex items-center justify-between mb-1">
                <div>
                    <h2 class="font-semibold text-white">Tren Peminjaman</h2>
                    <p class="text-xs text-white/40 mt-0.5">7 hari terakhir</p>
                </div>
                <span class="badge badge-fire">{{ array_sum($trenData) }} total</span>
            </div>
            @php $max = max(1, max($trenData)); @endphp
            <div class="flex items-end justify-between gap-2 h-44 mt-4 px-1">
                @foreach($trenData as $i => $v)
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2">
                    <span class="text-[11px] font-bold text-white/80">{{ $v }}</span>
                    <div class="chart-bar w-full max-w-10 rounded-t-lg bg-gradient-to-t from-orange-600/70 to-amber-400/90 border border-orange-400/20"
                         style="height: {{ max(4, round($v / $max * 100)) }}%; opacity: {{ 0.45 + 0.55 * ($v / $max) }}"></div>
                    <span class="text-[10px] text-white/35">{{ $trenLabels[$i] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Status peminjaman --}}
        <div class="glass-card chart-card">
            <h2 class="font-semibold text-white mb-1">Status Peminjaman</h2>
            <p class="text-xs text-white/40 mb-4">Distribusi saat ini</p>
            @php
            $statusRows = [
                ['label'=>'Menunggu Persetujuan', 'value'=>$stats['diajukan'],     'color'=>'bg-yellow-400', 'bar'=>'from-yellow-500 to-orange-500'],
                ['label'=>'Sedang Dipinjam',      'value'=>$stats['dipinjam'],     'color'=>'bg-blue-400',   'bar'=>'from-blue-500 to-cyan-500'],
                ['label'=>'Sudah Dikembalikan',   'value'=>$stats['dikembalikan'], 'color'=>'bg-green-400',  'bar'=>'from-green-500 to-emerald-500'],
                ['label'=>'Terlambat',            'value'=>$stats['telat'],        'color'=>'bg-red-400',    'bar'=>'from-red-500 to-rose-500'],
            ];
            $totalSt = max(1, $stats['diajukan'] + $stats['dipinjam'] + $stats['dikembalikan'] + $stats['telat']);
            @endphp
            <div class="space-y-4">
                @foreach($statusRows as $row)
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-white/60 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $row['color'] }}"></span>{{ $row['label'] }}
                        </span>
                        <span class="text-sm font-bold text-white">{{ $row['value'] }}</span>
                    </div>
                    <div class="h-2 rounded-full bg-white/5 overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r {{ $row['bar'] }} transition-all duration-700" style="width: {{ round($row['value'] / $totalSt * 100) }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
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
                <div class="glass-table-row flex items-start gap-3 p-3 rounded-xl">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">
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
