<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SiPinjam (Sistem Peminjaman Alat)</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fire-bg min-h-screen flex items-center justify-center p-4 font-sans relative overflow-hidden selection:bg-orange-500/30 selection:text-orange-200">

    {{-- Ambient Floating Glow Orbs for Glassmorphism --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10" aria-hidden="true">
        <div class="ambient-orb ambient-orb-1"></div>
        <div class="ambient-orb ambient-orb-2"></div>
        <div class="ambient-orb ambient-orb-3"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo & Brand Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 mb-4 shadow-xl shadow-orange-950/70 border border-white/20">
                <svg class="w-8 h-8 text-white drop-shadow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold fire-text tracking-tight">SiPinjam</h1>
            <p class="text-orange-200/70 text-xs font-medium mt-1">Sistem Peminjaman Alat Laboratorium & Inventaris</p>
        </div>

        {{-- Glassmorphism Login Card --}}
        <div class="glass-card p-8 sm:p-10 border border-white/10 shadow-2xl backdrop-blur-2xl">
            <div class="mb-6 text-center">
                <h2 class="text-lg font-bold text-white tracking-wide">Selamat Datang Kembali</h2>
                <p class="text-xs text-white/50 mt-1">Masukkan kredensial akun untuk mengakses sistem</p>
            </div>

            {{-- Error Notifications --}}
            @if($errors->any())
                <div class="alert-error mb-5 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error mb-5 text-xs">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-orange-200/80 mb-2">Alamat Email</label>
                    <div class="relative">
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@sekolah.sch.id"
                            class="input-glass text-sm @error('email') border-red-500/60 @enderror"
                            required autofocus
                        >
                    </div>
                    @error('email')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-orange-200/80 mb-2">Kata Sandi</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="••••••••"
                        class="input-glass text-sm @error('password') border-red-500/60 @enderror"
                        required
                    >
                    @error('password')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember"
                            class="rounded border-orange-500/30 bg-white/5 text-orange-500 focus:ring-0">
                        <span class="text-white/60">Ingat sesi saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-fire w-full py-3 text-sm font-semibold tracking-wide shadow-lg shadow-orange-900/30 mt-2">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-white/30 mt-6">
            SMKN 7 Baleendah — PPLG &copy; {{ date('Y') }}
        </p>
    </div>
</body>
</html>
