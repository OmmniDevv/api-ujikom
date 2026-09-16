<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SiPinjam</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="fire-bg min-h-screen flex items-center justify-center p-4 font-sans">

    {{-- Fire ambient particles --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-orange-600/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/3 right-1/4 w-48 h-48 bg-red-600/10 rounded-full blur-3xl animate-pulse" style="animation-delay:1s"></div>
        <div class="absolute top-1/2 right-1/3 w-32 h-32 bg-yellow-500/8 rounded-full blur-2xl animate-pulse" style="animation-delay:2s"></div>
    </div>

    <div class="w-full max-w-md relative z-10">

        {{-- Logo & title --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 mb-4 shadow-2xl shadow-orange-900/60">
                <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold fire-text">SiPinjam</h1>
            <p class="text-orange-300/60 text-sm mt-1">Sistem Peminjaman Alat — SMKN 7 Baleendah</p>
        </div>

        {{-- Login card --}}
        <div class="glass-card p-8">
            <h2 class="text-lg font-semibold text-white mb-6 text-center">Masuk ke Akun Anda</h2>

            {{-- Error global --}}
            @if($errors->any())
                <div class="alert-error mb-5">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error mb-5">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Email</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Masukkan email"
                        class="input-glass @error('email') border-red-500/60 @enderror"
                        required autofocus
                    >
                    @error('email')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Password</label>
                    <input
                        type="password"
                        name="password"
                        placeholder="Masukkan password"
                        class="input-glass @error('password') border-red-500/60 @enderror"
                        required
                    >
                    @error('password')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember"
                        class="rounded border-orange-500/30 bg-white/5 text-orange-500">
                    <label for="remember" class="text-sm text-orange-200/60">Ingat saya</label>
                </div>

                <button type="submit" class="btn-fire w-full py-3 text-base mt-2">
                    Masuk
                </button>
            </form>

            {{-- Demo credentials --}}
            <div class="mt-6 pt-5 border-t border-white/10">
                <p class="text-xs text-center text-orange-300/50 mb-3">Akun Demo</p>
                <div class="grid grid-cols-3 gap-2 text-xs text-center">
                    <div class="glass-card p-2 rounded-lg">
                        <p class="text-orange-400 font-semibold">Admin</p>
                        <p class="text-white/50 mt-1">admin@gmail.com</p>
                        <p class="text-white/50">password123</p>
                    </div>
                    <div class="glass-card p-2 rounded-lg">
                        <p class="text-orange-400 font-semibold">Petugas</p>
                        <p class="text-white/50 mt-1">petugas@gmail.com</p>
                        <p class="text-white/50">password123</p>
                    </div>
                    <div class="glass-card p-2 rounded-lg">
                        <p class="text-orange-400 font-semibold">Peminjam</p>
                        <p class="text-white/50 mt-1">rian@gmail.com</p>
                        <p class="text-white/50">password123</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
