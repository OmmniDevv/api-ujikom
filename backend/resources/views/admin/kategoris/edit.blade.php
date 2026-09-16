@extends('layouts.app')
@section('title', 'Edit Kategori')
@section('page-title', 'Edit Kategori')
@section('page-subtitle', 'Perbarui data kategori')

@section('content')
<div class="max-w-lg">
    <div class="glass-card p-6">
        <form action="{{ route('admin.kategoris.update', $kategori) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori', $kategori->nama_kategori) }}"
                    class="input-glass @error('nama_kategori') border-red-500/60 @enderror">
                @error('nama_kategori')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-orange-200/80 mb-2">Deskripsi</label>
                <textarea name="deskripsi" rows="3" class="input-glass">{{ old('deskripsi', $kategori->deskripsi) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Update</button>
                <a href="{{ route('admin.kategoris.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
