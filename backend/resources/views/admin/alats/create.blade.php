@extends('layouts.app')
@section('title', 'Tambah Alat')
@section('page-title', 'Tambah Alat')
@section('page-subtitle', 'Daftarkan alat baru ke inventaris')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card p-6">
        <form action="{{ route('admin.alats.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Alat</label>
                    <input type="text" name="nama_alat" value="{{ old('nama_alat') }}"
                        class="input-glass @error('nama_alat') border-red-500/60 @enderror" placeholder="Nama alat">
                    @error('nama_alat')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Kategori</label>
                    <select name="kategori_id" class="input-glass @error('kategori_id') border-red-500/60 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id')==$k->id ? 'selected':'' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Stok</label>
                    <input type="number" name="stok" value="{{ old('stok', 0) }}" min="0"
                        class="input-glass @error('stok') border-red-500/60 @enderror">
                    @error('stok')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Status Kondisi</label>
                    <select name="status_kondisi" class="input-glass">
                        <option value="baik"      {{ old('status_kondisi')=='baik'      ? 'selected':'' }}>Baik</option>
                        <option value="rusak"     {{ old('status_kondisi')=='rusak'     ? 'selected':'' }}>Rusak</option>
                        <option value="perbaikan" {{ old('status_kondisi')=='perbaikan' ? 'selected':'' }}>Dalam Perbaikan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="input-glass" placeholder="Deskripsi alat (opsional)">{{ old('deskripsi') }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Foto Alat</label>
                    <input type="file" name="gambar" class="input-glass" accept="image/*">
                    @error('gambar')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Simpan Alat</button>
                <a href="{{ route('admin.alats.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
