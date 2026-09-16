@extends('layouts.app')

@section('title', 'Cetak Laporan')
@section('page-title', 'Cetak Laporan')
@section('page-subtitle', 'Data peminjaman alat yang sudah diproses')

@section('content')
<div class="space-y-4">

    <div class="flex justify-end">
        <button onclick="window.print()" class="btn-fire flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg> Cetak / Print
        </button>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tgl Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Detail Alat</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjamans as $i => $item)
                <tr class="align-top">
                    <td class="text-white/40">{{ $i + 1 }}</td>
                    <td class="font-medium text-white">{{ $item->user->name ?? '-' }}</td>
                    <td class="text-white/60">{{ $item->tgl_pinjam }}</td>
                    <td class="text-white/60">{{ $item->tgl_kembali_plan }}</td>
                    <td>
                        <ul class="list-disc list-inside space-y-1 text-xs text-white/70">
                            @foreach($item->detailPinjam as $detail)
                                <li><span class="font-semibold text-white/90">{{ $detail->alat->nama_alat ?? '-' }}</span> ({{ $detail->jumlah }})</li>
                            @endforeach
                        </ul>
                    </td>
                    <td class="text-center">
                        @if($item->status === 'dipinjam')     <span class="badge badge-info">Dipinjam</span>
                        @elseif($item->status === 'dikembalikan') <span class="badge badge-success">Dikembalikan</span>
                        @else <span class="badge badge-danger">Telat</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-white/40 py-10">Belum ada data laporan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
