@extends('layouts.app')
@section('title', 'Detail User — ' . $user->name)
@section('page-title', 'Detail User')
@section('page-subtitle', $user->name)

@section('content')
<div class="max-w-3xl space-y-5">

    {{-- Info Akun --}}
    <div class="glass-card p-6">
        <div class="flex items-center gap-5 mb-5">
            <div class="w-16 h-16 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-orange-900/40 flex-shrink-0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                <p class="text-white/50 text-sm">{{ $user->email }}</p>
                <span class="badge mt-1
                    {{ $user->role === 'admin'    ? 'badge-danger' : '' }}
                    {{ $user->role === 'petugas'  ? 'badge-info' : '' }}
                    {{ $user->role === 'peminjam' ? 'badge-success' : '' }}
                ">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
        <div class="fire-divider"></div>
        <div class="grid grid-cols-2 gap-4 mt-4 text-sm">
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
                <p class="text-white font-medium">{{ $user->peminjaman->count() }} kali</p>
            </div>
        </div>
    </div>

    {{-- Riwayat Peminjaman --}}
    <div class="glass-card p-6">
        <h3 class="font-semibold text-white mb-4">Riwayat Peminjaman</h3>
        <div class="fire-divider mb-4"></div>
        @forelse($user->peminjaman()->latest()->take(10)->get() as $p)
        <div class="flex items-center justify-between py-3 border-b border-white/5 last:border-0">
            <div>
                <p class="text-white text-sm font-medium">Peminjaman #{{ $p->id }}</p>
                <p class="text-white/40 text-xs mt-0.5">{{ $p->tgl_pinjam->format('d M Y') }} → {{ $p->tgl_kembali_plan->format('d M Y') }}</p>
            </div>
            <span class="badge
                {{ $p->status === 'diajukan'     ? 'badge-warning' : '' }}
                {{ $p->status === 'dipinjam'     ? 'badge-info' : '' }}
                {{ $p->status === 'dikembalikan' ? 'badge-success' : '' }}
                {{ $p->status === 'telat'        ? 'badge-danger' : '' }}
            ">{{ ucfirst($p->status) }}</span>
        </div>
        @empty
        <p class="text-white/40 text-sm text-center py-4">Belum ada riwayat peminjaman.</p>
        @endforelse
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.users.edit', $user) }}" class="btn-fire">Edit User</a>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">← Kembali</a>
    </div>
</div>
@endsection
