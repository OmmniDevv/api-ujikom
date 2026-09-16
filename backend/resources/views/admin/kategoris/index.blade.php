@extends('layouts.app')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Manajemen kategori alat')

@section('content')
<div class="space-y-4">
    <div class="flex flex-col sm:flex-row gap-3 justify-between">
        <form method="GET" class="flex gap-2 flex-1 max-w-sm">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari kategori..." class="input-glass flex-1">
            <button type="submit" class="btn-ghost px-4">Cari</button>
        </form>
        <a href="{{ route('admin.kategoris.create') }}" class="btn-fire flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Tambah Kategori
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Kategori</th>
                    <th>Deskripsi</th>
                    <th class="text-center">Jumlah Alat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kategoris as $i => $k)
                <tr>
                    <td class="text-white/40">{{ $kategoris->firstItem() + $i }}</td>
                    <td class="font-medium text-white">{{ $k->nama_kategori }}</td>
                    <td class="text-white/60">{{ Str::limit($k->deskripsi, 60) ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge badge-fire">{{ $k->alat_count }} alat</span>
                    </td>
                    <td>
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.kategoris.edit', $k) }}" class="btn-ghost px-3 py-1.5 text-xs">Edit</a>
                            @if($k->alat_count > 0)
                                <button type="button" disabled title="Masih digunakan oleh {{ $k->alat_count }} alat"
                                    class="btn-danger px-3 py-1.5 text-xs opacity-40 cursor-not-allowed">Hapus</button>
                            @else
                                <button type="button"
                                    onclick="confirmDelete('{{ route('admin.kategoris.destroy', $k) }}', 'Hapus kategori {{ addslashes($k->nama_kategori) }}?')"
                                    class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-white/40 py-10">Tidak ada kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($kategoris->hasPages())
        <div class="p-4 border-t border-white/10">{{ $kategoris->links() }}</div>
        @endif
    </div>
</div>
@endsection
