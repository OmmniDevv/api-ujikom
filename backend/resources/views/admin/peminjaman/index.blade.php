@extends('layouts.app')
@section('title', 'Data Peminjaman')
@section('page-title', 'Data Peminjaman')
@section('page-subtitle', 'Seluruh riwayat peminjaman')

@section('content')
<div class="space-y-5">


    {{-- Filter & Search --}}
    <div class="glass-card p-4">
        <form method="GET" class="flex flex-wrap sm:flex-nowrap gap-3 items-end">
            <div class="flex-1 min-w-[220px]">
                <label class="text-white/50 text-xs block mb-1.5 font-medium">Cari Peminjam</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="input-glass input-has-icon w-full pr-3" placeholder="Nama peminjam...">
                </div>
            </div>
            <div class="w-44 shrink-0">
                <label class="text-white/50 text-xs block mb-1.5 font-medium">Status</label>
                <select name="status" class="input-glass w-full">
                    <option value="">Semua Status</option>
                    @foreach(['diajukan','dipinjam','dikembalikan','telat'] as $s)
                    <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-fire h-10 px-5 shrink-0">Filter</button>
            @if(request('search') || request('status'))
            <a href="{{ route('admin.peminjaman.index') }}" class="btn-ghost h-10 px-4 shrink-0 flex items-center">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="glass-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full table-glass">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Tgl Pinjam</th>
                        <th>Rencana Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjamanList as $p)
                    <tr>
                        <td class="text-white/40 text-xs">{{ $p->id }}</td>
                        <td>
                            <p class="text-white font-medium">{{ $p->user?->name }}</p>
                            <p class="text-white/40 text-xs">{{ $p->user?->email }}</p>
                        </td>
                        <td>
                            <div class="flex flex-wrap gap-1">
                                @foreach($p->detailPinjam->take(2) as $d)
                                <span class="badge badge-gray">{{ $d->alat?->nama_alat }} ({{ $d->jumlah }})</span>
                                @endforeach
                                @if($p->detailPinjam->count() > 2)
                                <span class="badge badge-gray">+{{ $p->detailPinjam->count() - 2 }} lagi</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $p->tgl_pinjam->format('d M Y') }}</td>
                        <td>{{ $p->tgl_kembali_plan->format('d M Y') }}</td>
                        <td>
                            <span class="badge
                                {{ $p->status === 'diajukan'     ? 'badge-warning' : '' }}
                                {{ $p->status === 'dipinjam'     ? 'badge-info' : '' }}
                                {{ $p->status === 'dikembalikan' ? 'badge-success' : '' }}
                                {{ $p->status === 'telat'        ? 'badge-danger' : '' }}
                            ">{{ ucfirst($p->status) }}</span>
                        </td>
                        <td>
                            <a href="{{ route('admin.peminjaman.show', $p) }}"
                               class="text-orange-400 hover:text-orange-300 text-xs font-medium">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-white/40 py-10">Tidak ada data peminjaman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($peminjamanList->hasPages())
        <div class="p-4 border-t border-white/5">
            {{ $peminjamanList->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
