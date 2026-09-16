@extends('layouts.app')
@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('page-subtitle', 'Perbarui data pengguna')

@section('content')
<div class="max-w-2xl">
    <div class="glass-card p-6">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="input-glass @error('name') border-red-500/60 @enderror">
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="input-glass @error('email') border-red-500/60 @enderror">
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Role</label>
                    <select name="role" class="input-glass">
                        <option value="admin"    {{ old('role',$user->role)=='admin'    ? 'selected':'' }}>Admin</option>
                        <option value="petugas"  {{ old('role',$user->role)=='petugas'  ? 'selected':'' }}>Petugas</option>
                        <option value="peminjam" {{ old('role',$user->role)=='peminjam' ? 'selected':'' }}>Peminjam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Password Baru <span class="text-white/30">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" name="password" class="input-glass" placeholder="Min. 6 karakter">
                    @error('password')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="input-glass">
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="input-glass">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2" class="input-glass">{{ old('alamat', $user->alamat) }}</textarea>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Foto Profile</label>
                    @if($user->foto_profile)
                        <img src="{{ asset('storage/'.$user->foto_profile) }}" class="w-16 h-16 rounded-full mb-2 object-cover border border-orange-500/30">
                    @endif
                    <input type="file" name="foto_profile" class="input-glass" accept="image/*">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Update User</button>
                <a href="{{ route('admin.users.index') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
