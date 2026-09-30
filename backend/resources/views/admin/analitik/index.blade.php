@extends('layouts.app')
@section('title', 'Analitik Eksekutif')
@section('page-title', 'Analitik Eksekutif')
@section('page-subtitle', 'Wawasan operasional, utilisasi aset, dan kepatuhan peminjam')

@section('content')
<div class="space-y-6">

    {{-- 1. Executive Stat Cards Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Kepatuhan / Disiplin --}}
        <div class="stat-card p-5 group hover:border-orange-500/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Disiplin Pengembalian</span>
                <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm font-bold border border-emerald-500/30">
                    ✓
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white font-mono" data-counter="{{ $tingkatDisiplin }}">{{ $tingkatDisiplin }}</span>
                <span class="text-lg font-semibold text-emerald-400">%</span>
            </div>
            <p class="text-xs text-white/40 mt-1">Rasio kembali tepat waktu tanpa denda</p>
        </div>

        {{-- Rata-rata Skor Reputasi --}}
        <div class="stat-card p-5 group hover:border-orange-500/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Indeks Reputasi Siswa</span>
                <span class="w-8 h-8 rounded-lg bg-orange-500/20 text-orange-400 flex items-center justify-center text-sm font-bold border border-orange-500/30">
                    ★
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white font-mono" data-counter="{{ $rataSkorReputasi }}">{{ $rataSkorReputasi }}</span>
                <span class="text-xs text-white/40 font-mono">/ 150</span>
            </div>
            <p class="text-xs text-white/40 mt-1">Rata-rata skor kredibilitas peminjam</p>
        </div>

        {{-- Akumulasi Denda --}}
        <div class="stat-card p-5 group hover:border-orange-500/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Penerimaan Denda</span>
                <span class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center text-sm font-bold border border-rose-500/30">
                    Rp
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-1">
                <span class="text-xs text-rose-300 font-mono">Rp</span>
                <span class="text-2xl font-extrabold text-white font-mono">{{ number_format($totalDenda, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-white/40 mt-1">Total denda dari {{ $totalTelat }} kasus telat</p>
        </div>

        {{-- Volume Peminjaman --}}
        <div class="stat-card p-5 group hover:border-orange-500/40 transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-white/50 uppercase tracking-wider">Total Transaksi</span>
                <span class="w-8 h-8 rounded-lg bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/30">
                    ⇄
                </span>
            </div>
            <div class="mt-3 flex items-baseline gap-2">
                <span class="text-3xl font-extrabold text-white font-mono" data-counter="{{ $totalPeminjaman }}">{{ $totalPeminjaman }}</span>
                <span class="text-xs text-cyan-400 font-medium">Pengajuan</span>
            </div>
            <p class="text-xs text-white/40 mt-1">Semua riwayat peminjaman tercatat</p>
        </div>

    </div>

    {{-- 2. Bar Chart Tren Peminjaman & Distribusi Reputasi --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Tren Peminjaman Bulanan --}}
        <div class="glass-card p-5 lg:col-span-2 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h2 class="font-semibold text-white text-base">Tren Peminjaman Alat</h2>
                    <span class="badge badge-fire text-xs">6 Bulan Terakhir</span>
                </div>
                <p class="text-xs text-white/40 mb-6">Grafik frekuensi transaksi peminjaman laboratorium per bulan</p>
            </div>

            {{-- Visual Bar Chart (Neon Glass Columns) --}}
            <div class="grid grid-cols-6 gap-3 items-end h-56 pt-6 pb-2 border-b border-white/10 px-2">
                @foreach($trenBulanan as $tren)
                @php
                    $pct = max(8, round(($tren['total'] / $maxTren) * 100));
                @endphp
                <div class="flex flex-col items-center gap-2 group h-full justify-end">
                    <span class="text-xs font-mono font-bold text-orange-400 opacity-0 group-hover:opacity-100 transition duration-200">
                        {{ $tren['total'] }}
                    </span>
                    <div class="w-full max-w-[48px] rounded-t-lg bg-gradient-to-t from-orange-600/40 via-orange-500/70 to-red-500 border-t border-x border-orange-400/50 shadow-[0_0_15px_rgba(249,115,22,0.3)] transition-all duration-500 group-hover:from-orange-500 group-hover:to-red-400"
                         style="height: {{ $pct }}%;">
                    </div>
                    <span class="text-[11px] font-medium text-white/60 text-center truncate w-full mt-1">
                        {{ $tren['bulan'] }}
                    </span>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between text-xs text-white/40 pt-4">
                <span>Rata-rata: {{ round(array_sum(array_column($trenBulanan, 'total')) / 6, 1) }} trx / bulan</span>
                <span class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 inline-block"></span> Total Peminjaman
                </span>
            </div>
        </div>

        {{-- Distribusi Tier Reputasi Siswa --}}
        <div class="glass-card p-5 flex flex-col justify-between">
            <div>
                <h2 class="font-semibold text-white text-base mb-1">Distribusi Reputasi Peminjam</h2>
                <p class="text-xs text-white/40 mb-5">Tingkat integritas siswa berdasarkan riwayat pengembalian</p>
            </div>

            <div class="space-y-3.5">
                {{-- Teladan --}}
                <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-emerald-400 flex items-center gap-1.5">
                            🌟 Peminjam Teladan
                        </span>
                        <span class="text-xs font-mono font-bold text-white">{{ $distribusiReputasi['teladan'] }} siswa</span>
                    </div>
                    <p class="text-[11px] text-white/50">Skor 120 - 150 (Disiplin tinggi & prima)</p>
                </div>

                {{-- Kredibel --}}
                <div class="p-3 rounded-lg bg-cyan-500/10 border border-cyan-500/20">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-cyan-400 flex items-center gap-1.5">
                            🟢 Kredibel & Tertib
                        </span>
                        <span class="text-xs font-mono font-bold text-white">{{ $distribusiReputasi['kredibel'] }} siswa</span>
                    </div>
                    <p class="text-[11px] text-white/50">Skor 90 - 119 (Siswa patuh aturan)</p>
                </div>

                {{-- Perlu Perhatian --}}
                <div class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                            🟡 Perlu Perhatian
                        </span>
                        <span class="text-xs font-mono font-bold text-white">{{ $distribusiReputasi['perhatian'] }} siswa</span>
                    </div>
                    <p class="text-[11px] text-white/50">Skor 60 - 89 (Pernah terlambat)</p>
                </div>

                {{-- Rawan --}}
                <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-rose-400 flex items-center gap-1.5">
                            🔴 Rawan / Peninjauan
                        </span>
                        <span class="text-xs font-mono font-bold text-white">{{ $distribusiReputasi['rawan'] }} siswa</span>
                    </div>
                    <p class="text-[11px] text-white/50">Skor &lt; 60 (Sering telat / sanksi)</p>
                </div>
            </div>

            <p class="text-[11px] text-white/30 text-center mt-3">Skor dihitung otomatis saat alat dikembalikan</p>
        </div>

    </div>

    {{-- 3. Utilisasi Aset: Hot Assets vs Idle Assets --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Hot Assets (Paling Sering Dipinjam) --}}
        <div class="glass-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-white text-base flex items-center gap-2">
                        🔥 Top 5 Alat Paling Diminati (Hot Assets)
                    </h2>
                    <p class="text-xs text-white/40 mt-0.5">Alat dengan frekuensi peminjaman tertinggi</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($topAlats as $index => $item)
                <div class="flex items-center justify-between p-3 rounded-lg bg-white/3 hover:bg-white/6 transition border border-white/5">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-6 h-6 rounded-full bg-orange-500/20 text-orange-400 font-mono text-xs font-bold flex items-center justify-center shrink-0 border border-orange-500/30">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $item->alat?->nama_alat ?? 'Alat Dihapus' }}</p>
                            <span class="text-xs text-white/40">{{ $item->alat?->kategori?->nama_kategori ?? 'Kategori' }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-sm font-mono font-extrabold text-orange-400">{{ $item->total_dipinjam }} unit</span>
                        <p class="text-[10px] text-white/40">{{ $item->frekuensi_pinjam }}x peminjaman</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-white/40 py-6 text-sm">Belum ada data peminjaman.</p>
                @endforelse
            </div>
        </div>

        {{-- Idle Assets (Alat Mengendap / Kurang Dimanfaatkan) --}}
        <div class="glass-card p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="font-semibold text-white text-base flex items-center gap-2">
                        💤 Top 5 Alat Mengendap (Idle Assets)
                    </h2>
                    <p class="text-xs text-white/40 mt-0.5">Alat yang paling jarang dipinjam (stok pasif di lab)</p>
                </div>
            </div>

            <div class="space-y-3">
                @forelse($idleAlats as $index => $alat)
                <div class="flex items-center justify-between p-3 rounded-lg bg-white/3 hover:bg-white/6 transition border border-white/5">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="w-6 h-6 rounded-full bg-blue-500/20 text-blue-400 font-mono text-xs font-bold flex items-center justify-center shrink-0 border border-blue-500/30">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-white truncate">{{ $alat->nama_alat }}</p>
                            <span class="text-xs text-white/40">{{ $alat->kategori?->nama_kategori ?? 'Kategori' }}</span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-sm font-mono font-bold text-white/70">{{ $alat->stok }} unit di lab</span>
                        <p class="text-[10px] text-amber-400/80">{{ $alat->detail_pinjam_count }}x pernah dipinjam</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-white/40 py-6 text-sm">Tidak ada alat pasif.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- 4. Status Kondisi Fisik Alat Lab --}}
    <div class="glass-card p-5">
        <h2 class="font-semibold text-white text-base mb-1">Status Kesehatan Aset Fisik</h2>
        <p class="text-xs text-white/40 mb-4">Persentase kondisi seluruh inventaris alat laboratorium</p>

        @php
            $totalSemuaKondisi = max(1, array_sum($kondisiStat));
            $pctBaik = round(($kondisiStat['baik'] / $totalSemuaKondisi) * 100);
            $pctRusak = round(($kondisiStat['rusak'] / $totalSemuaKondisi) * 100);
            $pctPerbaikan = round(($kondisiStat['perbaikan'] / $totalSemuaKondisi) * 100);
        @endphp

        {{-- Multi-color bar --}}
        <div class="w-full h-4 rounded-full bg-white/10 overflow-hidden flex mb-4">
            <div class="h-full bg-emerald-500 transition-all duration-700" style="width: {{ $pctBaik }}%;" title="Baik: {{ $pctBaik }}%"></div>
            <div class="h-full bg-amber-500 transition-all duration-700" style="width: {{ $pctPerbaikan }}%;" title="Perbaikan: {{ $pctPerbaikan }}%"></div>
            <div class="h-full bg-rose-500 transition-all duration-700" style="width: {{ $pctRusak }}%;" title="Rusak: {{ $pctRusak }}%"></div>
        </div>

        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                <span class="text-xs text-emerald-400 font-semibold block">Kondisi Baik</span>
                <span class="text-xl font-bold text-white font-mono">{{ $kondisiStat['baik'] }}</span>
                <span class="text-xs text-white/40 block">({{ $pctBaik }}%)</span>
            </div>
            <div class="p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                <span class="text-xs text-amber-400 font-semibold block">Perbaikan / Servis</span>
                <span class="text-xl font-bold text-white font-mono">{{ $kondisiStat['perbaikan'] }}</span>
                <span class="text-xs text-white/40 block">({{ $pctPerbaikan }}%)</span>
            </div>
            <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20">
                <span class="text-xs text-rose-400 font-semibold block">Rusak Parah</span>
                <span class="text-xl font-bold text-white font-mono">{{ $kondisiStat['rusak'] }}</span>
                <span class="text-xs text-white/40 block">({{ $pctRusak }}%)</span>
            </div>
        </div>
    </div>

</div>
@endsection
