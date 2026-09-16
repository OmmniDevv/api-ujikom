@extends('layouts.app')
@section('title', 'Riwayat Peminjaman')
@section('page-title', 'Riwayat Peminjaman')
@section('page-subtitle', 'Daftar peminjaman yang pernah diajukan')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row gap-3 justify-between">
        <form method="GET" class="flex gap-2">
            <select name="status" class="input-glass w-44">
                <option value="">Semua Status</option>
                <option value="diajukan"     {{ request('status')=='diajukan'     ? 'selected':'' }}>Diajukan</option>
                <option value="dipinjam"     {{ request('status')=='dipinjam'     ? 'selected':'' }}>Dipinjam</option>
                <option value="dikembalikan" {{ request('status')=='dikembalikan' ? 'selected':'' }}>Dikembalikan</option>
                <option value="telat"        {{ request('status')=='telat'        ? 'selected':'' }}>Telat</option>
            </select>
            <button type="submit" class="btn-ghost px-4">Filter</button>
        </form>
        <a href="{{ route('peminjam.peminjaman.create') }}" class="btn-fire flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Ajukan Baru
        </a>
    </div>

    <div class="space-y-3">
        @forelse($peminjamanList as $p)
        <div class="glass-card p-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        @if($p->status === 'diajukan')     <span class="badge badge-warning">Menunggu Persetujuan</span>
                        @elseif($p->status === 'dipinjam') <span class="badge badge-info">Sedang Dipinjam</span>
                        @elseif($p->status === 'dikembalikan') <span class="badge badge-success">Sudah Dikembalikan</span>
                        @else <span class="badge badge-danger">Terlambat</span>
                        @endif
                        <span class="text-xs text-white/40">{{ $p->created_at->format('d M Y') }}</span>
                    </div>

                    <div class="flex gap-4 text-xs text-white/50 mb-3">
                        <span>Pinjam: <span class="text-white">{{ $p->tgl_pinjam->format('d M Y') }}</span></span>
                        <span>Kembali: <span class="text-white">{{ $p->tgl_kembali_plan->format('d M Y') }}</span></span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($p->detailPinjam as $d)
                        <span class="badge badge-gray">{{ $d->alat?->nama_alat }} ({{ $d->jumlah }})</span>
                        @endforeach
                    </div>
                </div>

                @if($p->pengembalian)
                <div class="text-right text-xs">
                    <p class="text-white/40">Kembali {{ $p->pengembalian->tgl_kembali->format('d M Y') }}</p>
                    @if($p->pengembalian->denda > 0)
                        <p class="text-red-400 font-semibold mt-1">Denda: Rp {{ number_format($p->pengembalian->denda) }}</p>
                    @else
                        <p class="text-green-400 mt-1">Tidak ada denda</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="glass-card p-10 text-center">
            <p class="text-white/40">Belum ada riwayat peminjaman.</p>
            <a href="{{ route('peminjam.peminjaman.create') }}" class="btn-fire mt-4 inline-flex">Ajukan Sekarang</a>
        </div>
        @endforelse
    </div>

    @if($peminjamanList->hasPages())
    <div>{{ $peminjamanList->links() }}</div>
    @endif
</div>
@endsection
