@extends('layouts.app')
@section('title', 'Kelola Alat')
@section('page-title', 'Kelola Alat')
@section('page-subtitle', 'Inventaris alat tersedia')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col md:flex-row gap-3 justify-between items-stretch md:items-center">
        <form method="GET" class="flex flex-1 flex-wrap sm:flex-nowrap gap-2.5 items-center max-w-3xl">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama alat, kategori..." class="input-glass input-has-icon w-full pr-3">
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
                <a href="{{ route('admin.alats.index') }}" class="btn-ghost shrink-0 px-3 text-xs">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.alats.create') }}" class="btn-fire shrink-0 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Tambah Alat
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Gambar</th>
                    <th>Nama Alat</th>
                    <th>Kategori</th>
                    <th class="text-center">Stok</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alats as $i => $alat)
                <tr>
                    <td class="text-white/40">{{ $alats->firstItem() + $i }}</td>
                    <td>
                        <div class="w-10 h-10 rounded-lg overflow-hidden flex items-center justify-center bg-orange-950/40 border border-orange-500/20 flex-shrink-0">
                            @if($alat->gambar)
                                <img src="{{ asset('storage/'.$alat->gambar) }}"
                                     class="w-full h-full object-cover"
                                     alt="{{ $alat->nama_alat }}"
                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <div class="hidden w-full h-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-orange-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @else
                                <svg class="w-5 h-5 text-orange-500/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            @endif
                        </div>
                    </td>
                    <td class="font-medium text-white">{{ $alat->nama_alat }}</td>
                    <td><span class="badge badge-gray">{{ $alat->kategori?->nama_kategori }}</span></td>
                    <td class="text-center font-semibold {{ $alat->stok > 0 ? 'text-green-400' : 'text-red-400' }}">{{ $alat->stok }}</td>
                    <td class="text-center">
                        @if($alat->status_kondisi === 'baik')
                            <span class="badge badge-success">Baik</span>
                        @elseif($alat->status_kondisi === 'rusak')
                            <span class="badge badge-danger">Rusak</span>
                        @else
                            <span class="badge badge-warning">Perbaikan</span>
                        @endif
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.alats.edit', $alat) }}" class="btn-ghost px-3 py-1.5 text-xs">Edit</a>
                            <button type="button"
                                onclick="confirmDelete('{{ route('admin.alats.destroy', $alat) }}', 'Hapus alat {{ addslashes($alat->nama_alat) }}?')"
                                class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-white/40 py-10">Tidak ada data alat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($alats->hasPages())
        <div class="p-4 border-t border-white/10">{{ $alats->links() }}</div>
        @endif
    </div>
</div>
@endsection
