@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman')
@section('page-title', 'Persetujuan Peminjaman')
@section('page-subtitle', 'Daftar pengajuan peminjaman yang menunggu verifikasi')

@section('content')
<div class="space-y-4">

    {{-- Search --}}
    <div class="flex flex-col md:flex-row gap-3 justify-between items-stretch md:items-center">
        <form method="GET" class="flex flex-1 flex-wrap sm:flex-nowrap gap-2.5 items-center max-w-xl">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/40">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama peminjam..." class="input-glass input-has-icon w-full pr-3">
            </div>
            <button type="submit" class="btn-ghost shrink-0 px-4">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="btn-ghost shrink-0 px-3 text-xs">Reset</a>
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
