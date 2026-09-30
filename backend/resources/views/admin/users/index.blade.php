@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen akun pengguna sistem')

@section('content')
<div class="space-y-4">

    {{-- Header actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
        <form method="GET" class="flex flex-wrap sm:flex-nowrap gap-2.5 flex-1 w-full max-w-xl items-center">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama, email, no hp..."
                    class="input-glass w-full pl-9 pr-3">
                <svg class="w-4 h-4 text-white/40 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="w-36 shrink-0">
                <select name="role" class="input-glass w-full">
                    <option value="">Semua Role</option>
                    <option value="admin"    {{ request('role')=='admin'    ? 'selected':'' }}>Admin</option>
                    <option value="petugas"  {{ request('role')=='petugas'  ? 'selected':'' }}>Petugas</option>
                    <option value="peminjam" {{ request('role')=='peminjam' ? 'selected':'' }}>Peminjam</option>
                </select>
            </div>
            <button type="submit" class="btn-ghost shrink-0 px-4">Cari</button>
            @if(request('search') || request('role'))
                <a href="{{ route('admin.users.index') }}" class="btn-ghost shrink-0 px-3 text-xs">Reset</a>
            @endif
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn-fire shrink-0 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Tambah User
        </a>
    </div>

    {{-- Table --}}
    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th class="text-left">No</th>
                    <th class="text-left">Nama</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Role</th>
                    <th class="text-left">No HP</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td class="text-white/40">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-xs font-bold flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="text-white/60">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'admin')
                            <span class="badge badge-fire">Admin</span>
                        @elseif($user->role === 'petugas')
                            <span class="badge badge-info">Petugas</span>
                        @else
                            <span class="badge badge-gray">Peminjam</span>
                        @endif
                    </td>
                    <td class="text-white/60">{{ $user->no_hp ?? '-' }}</td>
                    <td>
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn-ghost px-3 py-1.5 text-xs">Edit</a>
                            @if($user->id !== auth()->id())
                            <button type="button"
                                onclick="confirmDelete('{{ route('admin.users.destroy', $user) }}', 'Hapus user {{ addslashes($user->name) }}?')"
                                class="btn-danger px-3 py-1.5 text-xs">Hapus</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-white/40 py-10">Tidak ada data user.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="p-4 border-t border-white/10">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
