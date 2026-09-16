@extends('layouts.app')
@section('title', 'Proses Pengembalian')
@section('page-title', 'Proses Pengembalian')
@section('page-subtitle', 'Catat pengembalian alat')

@section('content')
<div class="max-w-2xl space-y-4">

    {{-- Pilih peminjaman --}}
    @if(!$selectedPeminjaman)
    <div class="glass-card p-5">
        <h2 class="font-semibold text-white mb-4">Pilih Peminjaman</h2>
        <div class="space-y-2">
            @forelse($peminjamanAktif as $p)
            <a href="{{ route('petugas.pengembalian.create', ['peminjaman_id' => $p->id]) }}"
               class="flex items-center justify-between p-3 rounded-lg bg-white/3 hover:bg-white/6 transition border border-transparent hover:border-orange-500/30">
                <div>
                    <p class="text-sm font-medium text-white">{{ $p->user->name }}</p>
                    <p class="text-xs text-white/40 mt-0.5">{{ $p->detailPinjam->count() }} alat · Rencana kembali: {{ $p->tgl_kembali_plan->format('d M Y') }}</p>
                </div>
                <span class="badge badge-info">Dipinjam</span>
            </a>
            @empty
            <p class="text-center text-white/40 py-6 text-sm">Tidak ada peminjaman yang sedang aktif.</p>
            @endforelse
        </div>
    </div>
    @else

    {{-- Form pengembalian --}}
    <div class="glass-card p-5">
        <h2 class="font-semibold text-white mb-1">Peminjam: {{ $selectedPeminjaman->user->name }}</h2>
        <p class="text-xs text-white/40 mb-4">Rencana kembali: {{ $selectedPeminjaman->tgl_kembali_plan->format('d M Y') }}</p>
        <div class="fire-divider"></div>

        <div class="mt-4 space-y-2">
            @foreach($selectedPeminjaman->detailPinjam as $d)
            <div class="flex items-center gap-3 p-2 rounded-lg bg-white/3 text-sm">
                <span class="text-white font-medium">{{ $d->alat?->nama_alat }}</span>
                <span class="badge badge-fire ml-auto">{{ $d->jumlah }} unit</span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="glass-card p-5">
        <form action="{{ route('petugas.pengembalian.store') }}" method="POST" class="space-y-5">
            @csrf
            <input type="hidden" name="peminjaman_id" value="{{ $selectedPeminjaman->id }}">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Tanggal Pengembalian</label>
                    <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', now()->format('Y-m-d')) }}"
                        class="input-glass @error('tgl_kembali') border-red-500/60 @enderror">
                    @error('tgl_kembali')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Kondisi Alat</label>
                    <select name="kondisi_kembali" class="input-glass">
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                        <option value="perbaikan">Perlu Perbaikan</option>
                    </select>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Catatan <span class="text-white/30">(opsional)</span></label>
                    <textarea name="catatan" rows="2" class="input-glass" placeholder="Catatan kondisi alat">{{ old('catatan') }}</textarea>
                </div>
            </div>

            <div class="p-3 rounded-lg bg-yellow-500/10 border border-yellow-500/20 text-xs text-yellow-300">
                ⚠ Denda otomatis dihitung Rp 5.000/hari jika terlambat dari rencana kembali {{ $selectedPeminjaman->tgl_kembali_plan->format('d M Y') }}
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-fire px-6 py-2.5">Proses Pengembalian</button>
                <a href="{{ route('petugas.pengembalian.create') }}" class="btn-ghost px-6 py-2.5">← Pilih Lain</a>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
