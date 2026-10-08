@extends('layouts.app')
@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Petugas')
@section('page-subtitle', 'Kelola peminjaman dan pengembalian')

@section('content')
<div class="space-y-6">

    {{-- Stats dengan sparkline (ref: Glassy Dashboard reel) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Menunggu Approval', 'value'=>$stats['diajukan'],     'color'=>'from-yellow-500 to-orange-500', 'spark'=>'M0,26 L12,22 L24,24 L36,16 L48,20 L60,12 L72,14 L84,6', 'sc'=>'#fbbf24'],
            ['label'=>'Sedang Dipinjam',   'value'=>$stats['dipinjam'],     'color'=>'from-blue-500 to-cyan-500',     'spark'=>'M0,22 L12,26 L24,18 L36,22 L48,14 L60,18 L72,10 L84,12', 'sc'=>'#38bdf8'],
            ['label'=>'Dikembalikan',      'value'=>$stats['dikembalikan'], 'color'=>'from-green-500 to-emerald-500', 'spark'=>'M0,24 L12,20 L24,22 L36,14 L48,16 L60,8 L72,12 L84,4',  'sc'=>'#34d399'],
            ['label'=>'Telat Kembali',     'value'=>$stats['telat'],        'color'=>'from-red-500 to-rose-500',       'spark'=>'M0,20 L12,24 L24,20 L36,26 L48,22 L60,28 L72,24 L84,26', 'sc'=>'#fb7185'],
        ];
        @endphp
        @foreach($cards as $c)
        <div class="stat-card stat-glassy">
            <p class="text-xs text-white/50 font-medium mb-1">{{ $c['label'] }}</p>
            <p class="text-3xl font-extrabold text-white tracking-tight">{{ $c['value'] }}</p>
            <div class="mt-2 w-8 h-1 rounded-full bg-gradient-to-r {{ $c['color'] }}"></div>
            <svg class="spark" width="84" height="32" viewBox="0 0 84 32" fill="none">
                <path d="{{ $c['spark'] }}" stroke="{{ $c['sc'] }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" opacity="0.85"/>
            </svg>
        </div>
        @endforeach
    </div>

    {{-- Peminjaman perlu tindakan --}}
    <div class="glass-card p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-white">Peminjaman Perlu Tindakan</h2>
            <a href="{{ route('petugas.peminjaman.index') }}" class="text-xs text-orange-400 hover:text-orange-300">Lihat semua →</a>
        </div>
        <div class="fire-divider"></div>
        @if($peminjamanTerbaru->isEmpty())
            <p class="text-center text-white/40 py-8 text-sm">Tidak ada peminjaman yang perlu ditangani.</p>
        @else
        <div class="space-y-3 mt-3">
            @foreach($peminjamanTerbaru as $p)
            <div class="glass-table-row flex items-center justify-between p-3 rounded-xl">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-orange-500/30 to-red-600/30 border border-orange-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-orange-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ $p->user?->name ?? 'User Dihapus' }}</p>
                        <p class="text-xs text-white/40 mt-0.5">{{ $p->detailPinjam->count() }} alat · {{ $p->tgl_pinjam->format('d M Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if($p->status === 'diajukan')
                        <span class="badge badge-warning">Diajukan</span>
                        <form action="{{ route('petugas.peminjaman.setujui', $p) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-fire px-3 py-1.5 text-xs">Setujui</button>
                        </form>
                    @else
                        <span class="badge badge-info">Dipinjam</span>
                        <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $p->id]) }}"
                           class="btn-ghost px-3 py-1.5 text-xs">Proses Kembali</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
