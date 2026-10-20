@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Biodata akunmu')

@section('content')
<div class="max-w-4xl space-y-5">

    <div class="glass-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center gap-5">
            @if($user->foto_profile_url)
                <img src="{{ $user->foto_profile_url }}" alt="Foto {{ $user->name }}"
                     class="w-24 h-24 rounded-full object-cover shadow-lg shadow-orange-900/40 border-2 border-orange-500/40 flex-shrink-0">
            @else
                <div class="w-24 h-24 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg shadow-orange-900/40 flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="min-w-0">
                <h2 class="text-xl font-bold text-white truncate">{{ $user->name }}</h2>
                <p class="text-white/50 text-sm">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <span class="badge
                        {{ $user->role === 'admin'    ? 'badge-danger' : '' }}
                        {{ $user->role === 'petugas'  ? 'badge-info' : '' }}
                        {{ $user->role === 'peminjam' ? 'badge-success' : '' }}
                    ">{{ ucfirst($user->role) }}</span>
                    @if($user->role === 'peminjam')
                        @php $tier = $user->tier_reputasi; @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-white/5 border border-white/10" title="{{ $tier['deskripsi'] }}">
                            <span class="text-xs">{{ $tier['icon'] }}</span>
                            <span class="text-xs font-semibold text-white/90">{{ $tier['nama'] }}</span>
                            <span class="font-mono text-xs font-bold {{ $tier['color'] }}">{{ $user->skor_reputasi ?? 100 }} pts</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="fire-divider my-5"></div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">No. HP</p>
                <p class="text-white font-medium">{{ $user->no_hp ?? '—' }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Alamat</p>
                <p class="text-white font-medium">{{ $user->alamat ?? '—' }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Bergabung</p>
                <p class="text-white font-medium">{{ $user->created_at->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Total Peminjaman</p>
                <p class="text-white font-medium">{{ $stats['total'] }} kali</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Sedang Aktif</p>
                <p class="text-white font-medium">{{ $stats['aktif'] }} peminjaman</p>
            </div>
            <div>
                <p class="text-white/40 text-xs uppercase tracking-wider mb-1">Selesai</p>
                <p class="text-white font-medium">{{ $stats['selesai'] }} peminjaman</p>
            </div>
        </div>
    </div>

    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-1">Ubah Biodata</h3>
        <p class="text-white/40 text-xs mb-5">Perbarui nama, kontak, atau foto profilmu di sini.</p>

        <form action="{{ route('me.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-glass" required>
                    @error('name')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-glass" required>
                    @error('email')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" class="input-glass" placeholder="08xxxxxxxxxx">
                    @error('no_hp')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Foto Profil</label>
                    <input type="file" name="foto_profile" class="input-glass" accept="image/*">
                    <p class="text-white/30 text-xs mt-1">JPG/PNG, maks 2 MB. Kosongkan kalau tidak ingin ganti.</p>
                    @error('foto_profile')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Alamat</label>
                    <textarea name="alamat" rows="2" class="input-glass">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-fire px-6 py-2.5">Simpan Perubahan</button>
            </div>
        </form>
    </div>

</div>
@endsection
