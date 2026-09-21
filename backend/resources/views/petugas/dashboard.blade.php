@extends('layouts.app')
@section('title', 'Dashboard Petugas')
@section('page-title', 'Dashboard Petugas')
@section('page-subtitle', 'Kelola peminjaman dan pengembalian')

@section('content')
<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $cards = [
            ['label'=>'Menunggu Approval', 'value'=>$stats['diajukan'],     'badge'=>'badge-warning', 'color'=>'from-yellow-500 to-orange-500'],
            ['label'=>'Sedang Dipinjam',   'value'=>$stats['dipinjam'],     'badge'=>'badge-info',    'color'=>'from-blue-500 to-cyan-500'],
            ['label'=>'Dikembalikan',      'value'=>$stats['dikembalikan'], 'badge'=>'badge-success', 'color'=>'from-green-500 to-emerald-500'],
            ['label'=>'Telat Kembali',     'value'=>$stats['telat'],        'badge'=>'badge-danger',  'color'=>'from-red-500 to-rose-500'],
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
            <div class="flex items-center justify-between p-3 rounded-lg bg-white/3 hover:bg-white/5 transition">
                <div>
                    <p class="text-sm font-medium text-white">{{ $p->user->name }}</p>
                    <p class="text-xs text-white/40 mt-0.5">{{ $p->detailPinjam->count() }} alat · {{ $p->tgl_pinjam->format('d M Y') }}</p>
                </div>
                <div class="flex items-center gap-2">
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
