<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SiPinjam') — Sistem Peminjaman Alat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fire-bg text-white font-sans h-full">

<div class="flex h-screen overflow-hidden">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="glass-sidebar w-64 flex-shrink-0 flex flex-col h-full overflow-y-auto">
        {{-- Logo --}}
        <div class="p-5 border-b border-orange-900/30">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center shadow-lg shadow-orange-900/50">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-white text-sm leading-tight">SiPinjam</p>
                    <p class="text-xs text-orange-400/70">Peminjaman Alat</p>
                </div>
            </a>
        </div>

        {{-- User info --}}
        <div class="p-4 border-b border-orange-900/20">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="badge badge-fire text-xs">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 p-3 space-y-0.5">
            @php $role = auth()->user()->role; @endphp

            {{-- ADMIN NAV --}}
            @if($role === 'admin')
                <p class="text-xs font-semibold text-orange-500/50 uppercase tracking-widest px-3 py-2 mt-1">Menu Admin</p>
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg> Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m6-4a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg> Kelola User
                </a>
                <a href="{{ route('admin.kategoris.index') }}"
                   class="nav-link {{ request()->routeIs('admin.kategoris*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l7 7-7 7-5-5V3z"/>
                    </svg> Kategori
                </a>
                <a href="{{ route('admin.alats.index') }}"
                   class="nav-link {{ request()->routeIs('admin.alats*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    </svg> Alat
                </a>
                <a href="{{ route('admin.peminjaman.index') }}"
                   class="nav-link {{ request()->routeIs('admin.peminjaman*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg> Peminjaman
                </a>
            @endif

            {{-- PETUGAS NAV --}}
            @if($role === 'petugas')
                <p class="text-xs font-semibold text-orange-500/50 uppercase tracking-widest px-3 py-2 mt-1">Menu Petugas</p>
                <a href="{{ route('petugas.dashboard') }}"
                   class="nav-link {{ request()->routeIs('petugas.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg> Dashboard
                </a>
                <a href="{{ route('petugas.peminjaman.index') }}"
                   class="nav-link {{ request()->routeIs('petugas.peminjaman*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg> Persetujuan Peminjaman
                </a>
                <a href="{{ route('petugas.pengembalian.index') }}"
                   class="nav-link {{ request()->routeIs('petugas.pengembalian*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg> Pemantauan Pengembalian
                </a>
                <a href="{{ route('petugas.laporan.index') }}"
                   class="nav-link {{ request()->routeIs('petugas.laporan*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg> Cetak Laporan
                </a>
            @endif

            {{-- PEMINJAM NAV --}}
            @if($role === 'peminjam')
                <p class="text-xs font-semibold text-orange-500/50 uppercase tracking-widest px-3 py-2 mt-1">Menu Peminjam</p>
                <a href="{{ route('peminjam.dashboard') }}"
                   class="nav-link {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                    </svg> Dashboard
                </a>
                <a href="{{ route('peminjam.katalog') }}"
                   class="nav-link {{ request()->routeIs('peminjam.katalog') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2"/>
                    </svg> Katalog Alat
                </a>
                <a href="{{ route('peminjam.peminjaman.riwayat') }}"
                   class="nav-link {{ request()->routeIs('peminjam.peminjaman*') ? 'active' : '' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg> Riwayat Saya
                </a>
            @endif
        </nav>

        {{-- Logout --}}
        <div class="p-3 border-t border-orange-900/20">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-full text-left text-red-400 hover:bg-red-900/20 hover:text-red-300">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== MAIN CONTENT ===== --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Topbar --}}
        <header class="glass-nav h-14 flex items-center justify-between px-6 flex-shrink-0">
            <div>
                <h1 class="text-sm font-semibold text-white">@yield('page-title', 'Dashboard')</h1>
                <p class="text-xs text-orange-400/60">@yield('page-subtitle', '')</p>
            </div>
            <div class="flex items-center gap-3 text-sm text-orange-300/60">
                <span>{{ now()->format('d M Y') }}</span>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4 space-y-2">
            @if(session('success'))
                <div class="alert-success flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert-error flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>
</div>

{{-- ===== MODAL KONFIRMASI HAPUS ===== --}}
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
    {{-- Modal --}}
    <div class="relative glass-card p-6 w-full max-w-sm border border-red-500/30 shadow-2xl shadow-red-900/30">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-500/20 border border-red-500/30 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <h3 class="font-semibold text-white text-sm">Konfirmasi Hapus</h3>
                <p class="text-white/50 text-xs mt-0.5" id="deleteModalMessage">Yakin ingin menghapus data ini?</p>
            </div>
        </div>
        <p class="text-white/40 text-xs mb-5">Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="closeDeleteModal()" class="btn-ghost px-4 py-2 text-sm">Batal</button>
            <form id="deleteForm" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger px-4 py-2 text-sm">Ya, Hapus</button>
            </form>
        </div>
    </div>
</div>

<script>
function confirmDelete(url, message) {
    document.getElementById('deleteModalMessage').textContent = message || 'Yakin ingin menghapus data ini?';
    document.getElementById('deleteForm').action = url;
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDeleteModal();
});
</script>

</body>
</html>
