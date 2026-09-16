@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('page-subtitle', 'Manajemen akun pengguna sistem')

@section('content')
<div class="space-y-4">

    {{-- Header actions --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-between">
        <form method="GET" class="flex gap-2 flex-1 max-w-md">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama, email, no hp..."
                class="input-glass flex-1">
            <select name="role" class="input-glass w-36">
                <option value="">Semua Role</option>
                <option value="admin"    {{ request('role')=='admin'    ? 'selected':'' }}>Admin</option>
                <option value="petugas"  {{ request('role')=='petugas'  ? 'selected':'' }}>Petugas</option>
                <option value="peminjam" {{ request('role')=='peminjam' ? 'selected':'' }}>Peminjam</option>
            </select>
            <button type="submit" class="btn-ghost px-4">Cari</button>
        </form>
        <a href="{{ route('admin.users.create') }}" class="btn-fire flex items-center gap-2">
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
