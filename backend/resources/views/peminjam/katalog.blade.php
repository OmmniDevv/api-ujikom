@extends('layouts.app')
@section('title', 'Katalog Alat')
@section('page-title', 'Katalog Alat')
@section('page-subtitle', 'Alat tersedia untuk dipinjam')

@section('content')
<div class="space-y-4">
    <form method="GET" class="flex flex-wrap sm:flex-nowrap gap-2.5 items-center">
        <div class="relative flex-1 min-w-[220px]">
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari alat..." class="input-glass input-has-icon w-full pr-3">
        </div>
        <div class="w-48 shrink-0">
            <select name="kategori_id" class="input-glass w-full">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ request('kategori_id')==$k->id ? 'selected':'' }}>{{ $k->nama_kategori }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn-ghost shrink-0 px-4">Cari</button>
        @if(request('search') || request('kategori_id'))
            <a href="{{ route('peminjam.katalog') }}" class="btn-ghost shrink-0 px-3 text-xs">Reset</a>
        @endif
        <a href="{{ route('peminjam.peminjaman.create') }}" class="btn-fire shrink-0 flex items-center gap-2 sm:ml-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Ajukan Pinjam
        </a>
    </form>

    @if($alats->isEmpty())
        <div class="glass-card p-10 text-center">
            <p class="text-white/40">Tidak ada alat yang tersedia saat ini.</p>
        </div>
    @else
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($alats as $alat)
        <div class="glass-card glass-card-hover p-4 flex flex-col">
            @if($alat->gambar)
                <img src="{{ asset('storage/'.$alat->gambar) }}"
                    class="w-full h-36 object-cover rounded-lg mb-3 border border-orange-500/10"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                <div class="hidden flex w-full h-36 rounded-lg mb-3 bg-orange-900/20 items-center justify-center border border-orange-500/10">
                    <svg class="w-10 h-10 text-orange-500/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                    </svg>
                </div>
            @else
                <div class="w-full h-36 rounded-lg mb-3 bg-orange-900/20 flex items-center justify-center border border-orange-500/10">
                    <svg class="w-10 h-10 text-orange-500/30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/>
                    </svg>
                </div>
            @endif
            <span class="badge badge-gray text-xs self-start mb-2">{{ $alat->kategori?->nama_kategori }}</span>
            <h3 class="font-semibold text-white text-sm flex-1">{{ $alat->nama_alat }}</h3>
            @if($alat->deskripsi)
                <p class="text-xs text-white/40 mt-1">{{ Str::limit($alat->deskripsi, 60) }}</p>
            @endif
            <div class="flex items-center justify-between mt-3">
                <span class="text-xs text-white/50">Stok: <span class="text-green-400 font-semibold">{{ $alat->stok }}</span></span>
                <span class="badge badge-success">Baik</span>
            </div>
        </div>
        @endforeach
    </div>
    <div>{{ $alats->links() }}</div>
    @endif
</div>
@endsection
