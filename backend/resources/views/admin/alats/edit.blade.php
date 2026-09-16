@extends('layouts.app')
@section('title', 'Edit Alat')
@section('page-title', 'Edit Alat')
@section('page-subtitle', 'Perbarui data alat')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card p-6">
        <form action="{{ route('admin.alats.update', $alat) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Alat</label>
                    <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat) }}"
                        class="input-glass @error('nama_alat') border-red-500/60 @enderror">
                    @error('nama_alat')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Kategori</label>
                    <select name="kategori_id" class="input-glass">
                        @foreach($kategoris as $k)
                            <option value="{{ $k->id }}" {{ old('kategori_id',$alat->kategori_id)==$k->id ? 'selected':'' }}>{{ $k->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Stok</label>
                    <input type="number" name="stok" value="{{ old('stok', $alat->stok) }}" min="0" class="input-glass">
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Status Kondisi</label>
                    <select name="status_kondisi" class="input-glass">
                        <option value="baik"      {{ old('status_kondisi',$alat->status_kondisi)=='baik'      ? 'selected':'' }}>Baik</option>
                        <option value="rusak"     {{ old('status_kondisi',$alat->status_kondisi)=='rusak'     ? 'selected':'' }}>Rusak</option>
                        <option value="perbaikan" {{ old('status_kondisi',$alat->status_kondisi)=='perbaikan' ? 'selected':'' }}>Dalam Perbaikan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3" class="input-glass">{{ old('deskripsi', $alat->deskripsi) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Foto Alat</label>
                    @if($alat->gambar)
                        <img src="{{ asset('storage/'.$alat->gambar) }}" class="w-20 h-20 rounded-lg mb-2 object-cover border border-orange-500/30">
                    @endif
                    <input type="file" name="gambar" class="input-glass" accept="image/*">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Update Alat</button>
                <a href="{{ route('admin.alats.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
