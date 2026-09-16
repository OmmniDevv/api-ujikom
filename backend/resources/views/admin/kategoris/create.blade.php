@extends('layouts.app')
@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori')
@section('page-subtitle', 'Buat kategori alat baru')

@section('content')
<div class="max-w-lg">
    <div class="glass-card p-6">
        <form action="{{ route('admin.kategoris.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="{{ old('nama_kategori') }}"
                    class="input-glass @error('nama_kategori') border-red-500/60 @enderror"
                    placeholder="Contoh: Alat Ukur">
                @error('nama_kategori')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-orange-200/80 mb-2">Deskripsi <span class="text-white/30">(opsional)</span></label>
                <textarea name="deskripsi" rows="3"
                    class="input-glass"
                    placeholder="Deskripsi singkat kategori">{{ old('deskripsi') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Simpan</button>
                <a href="{{ route('admin.kategoris.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
