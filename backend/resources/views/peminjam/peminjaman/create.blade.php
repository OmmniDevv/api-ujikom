@extends('layouts.app')
@section('title', 'Ajukan Peminjaman')
@section('page-title', 'Ajukan Peminjaman')
@section('page-subtitle', 'Pilih alat dan tentukan tanggal')

@section('content')
<div class="max-w-3xl">
    <div class="glass-card p-6">
        <form action="{{ route('peminjam.peminjaman.store') }}" method="POST" id="formPeminjaman" class="space-y-6">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" value="{{ old('tgl_pinjam', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="input-glass @error('tgl_pinjam') border-red-500/60 @enderror">
                    @error('tgl_pinjam')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-orange-200/80 mb-2">Rencana Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali_plan" value="{{ old('tgl_kembali_plan') }}"
                        min="{{ now()->addDay()->format('Y-m-d') }}"
                        class="input-glass @error('tgl_kembali_plan') border-red-500/60 @enderror">
                    @error('tgl_kembali_plan')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-white">Pilih Alat</h3>
                    <button type="button" onclick="tambahAlat()" class="btn-ghost px-3 py-1.5 text-xs flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg> Tambah Alat
                    </button>
                </div>

                <div id="daftarAlat" class="space-y-3">
                    <div class="alat-row flex items-center gap-3">
                        <select name="detail[0][alat_id]" class="input-glass flex-1">
                            <option value="">-- Pilih Alat --</option>
                            @foreach($alats as $a)
                                <option value="{{ $a->id }}">{{ $a->nama_alat }} (Stok: {{ $a->stok }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="detail[0][jumlah]" value="1" min="1"
                            placeholder="Jml" class="input-glass w-24">
                    </div>
                </div>
                @error('detail')<p class="text-red-400 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="p-3 rounded-lg bg-orange-500/10 border border-orange-500/20 text-xs text-orange-300">
                ℹ Peminjaman yang diajukan akan menunggu persetujuan petugas sebelum alat dapat diambil.
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-fire px-6 py-2.5">Kirim Pengajuan</button>
                <a href="{{ route('peminjam.katalog') }}" class="btn-ghost px-6 py-2.5">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
let index = 1;
const alats = @json($alats->map(fn($a) => ['id' => $a->id, 'nama' => $a->nama_alat, 'stok' => $a->stok]));

function tambahAlat() {
    const container = document.getElementById('daftarAlat');
    const div = document.createElement('div');
    div.className = 'alat-row flex items-center gap-3';

    let options = '<option value="">-- Pilih Alat --</option>';
    alats.forEach(a => { options += `<option value="${a.id}">${a.nama} (Stok: ${a.stok})</option>`; });

    div.innerHTML = `
        <select name="detail[${index}][alat_id]" class="input-glass flex-1">${options}</select>
        <input type="number" name="detail[${index}][jumlah]" value="1" min="1" placeholder="Jml" class="input-glass w-24">
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-300 p-1 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>`;
    container.appendChild(div);
    index++;
}
</script>
@endsection
