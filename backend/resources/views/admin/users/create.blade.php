@extends('layouts.app')
@section('title', 'Tambah User')
@section('page-title', 'Tambah User Baru')
@section('page-subtitle', 'Buat akun pengguna baru')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card p-6">
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="input-glass @error('name') border-red-500/60 @enderror" placeholder="Nama lengkap">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="input-glass @error('email') border-red-500/60 @enderror" placeholder="email@example.com">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Role</label>
                    <select name="role" class="input-glass @error('role') border-red-500/60 @enderror">
                        <option value="">-- Pilih Role --</option>
                        <option value="admin"    {{ old('role')=='admin'    ? 'selected':'' }}>Admin</option>
                        <option value="petugas"  {{ old('role')=='petugas'  ? 'selected':'' }}>Petugas</option>
                        <option value="peminjam" {{ old('role')=='peminjam' ? 'selected':'' }}>Peminjam</option>
                    </select>
                    @error('role')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Password</label>
                    <input type="password" name="password"
                        class="input-glass @error('password') border-red-500/60 @enderror" placeholder="Min. 6 karakter">
                    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input-glass" placeholder="Ulangi password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="input-glass" placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2" class="input-glass" placeholder="Alamat lengkap">{{ old('alamat') }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Foto Profile <span class="text-white/30">(opsional)</span></label>
                    <input type="file" name="foto_profile" class="input-glass" accept="image/*">
                    @error('foto_profile')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Simpan User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
