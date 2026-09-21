@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman')
@section('page-title', 'Persetujuan Peminjaman')
@section('page-subtitle', 'Daftar pengajuan peminjaman yang menunggu verifikasi')

@section('content')
<div class="space-y-4">

    {{-- Search --}}
    <div class="flex flex-col sm:flex-row gap-3 justify-between">
        <form method="GET" class="flex gap-2 flex-1 max-w-lg">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama peminjam..." class="input-glass flex-1">
            <button type="submit" class="btn-ghost px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn-ghost px-4">Reset</a>
            @endif
        </form>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Tgl Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Detail Alat</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $item)
                <tr class="align-top">
                    <td class="font-medium text-white">{{ $item->user->name ?? 'User Dihapus' }}</td>
                    <td class="text-white/60">{{ $item->tgl_pinjam }}</td>
                    <td class="text-white/60">{{ $item->tgl_kembali_plan }}</td>
                    <td>
                        <ul class="list-disc list-inside space-y-1 text-xs text-white/70">
                            @foreach($item->detailPinjam as $detail)
                                <li>
                                    <span class="font-semibold text-white/90">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                    (Jumlah: {{ $detail->jumlah }})
                                </li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center">
                        @if($item->status == 'diajukan')
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('petugas.peminjaman.setujui', $item) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        onclick="return confirm('Setujui peminjaman alat ini?')"
                                        class="btn-fire px-3 py-1.5 text-xs">
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('petugas.peminjaman.tolak', $item) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin menolak pengajuan peminjaman ini?')">
                                    @csrf
                                    <button type="submit" class="btn-danger px-3 py-1.5 text-xs">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        @else
                            <span class="badge badge-info">{{ ucfirst($item->status) }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-white/40 py-10">Tidak ada pengajuan peminjaman baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
