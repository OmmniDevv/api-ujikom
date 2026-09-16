@extends('layouts.app')
@section('title', 'Data Pengembalian')
@section('page-title', 'Data Pengembalian')
@section('page-subtitle', 'Riwayat pengembalian alat')

@section('content')
<div class="space-y-4">
    <div class="flex justify-end">
        <a href="{{ route('petugas.pengembalian.create') }}" class="btn-fire flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg> Proses Pengembalian
        </a>
    </div>

    <div class="glass-card overflow-hidden">
        <table class="table-glass w-full">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Tgl Kembali</th>
                    <th class="text-center">Kondisi</th>
                    <th class="text-right">Denda</th>
                    <th>Petugas</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pengembalianList as $i => $p)
                <tr>
                    <td class="text-white/40">{{ $pengembalianList->firstItem() + $i }}</td>
                    <td class="font-medium text-white">{{ $p->peminjaman?->user?->name }}</td>
                    <td class="text-white/60">{{ $p->tgl_kembali->format('d M Y') }}</td>
                    <td class="text-center">
                        @if($p->kondisi_kembali === 'baik') <span class="badge badge-success">Baik</span>
                        @elseif($p->kondisi_kembali === 'rusak') <span class="badge badge-danger">Rusak</span>
                        @else <span class="badge badge-warning">Perbaikan</span>
                        @endif
                    </td>
                    <td class="text-right {{ $p->denda > 0 ? 'text-red-400 font-semibold' : 'text-green-400' }}">
                        Rp {{ number_format($p->denda) }}
                    </td>
                    <td class="text-white/60">{{ $p->petugas?->name }}</td>
                    <td class="text-center">
                        <a href="{{ route('petugas.pengembalian.show', $p) }}" class="btn-ghost px-3 py-1.5 text-xs">Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-white/40 py-10">Belum ada data pengembalian.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($pengembalianList->hasPages())
        <div class="p-4 border-t border-white/10">{{ $pengembalianList->links() }}</div>
        @endif
    </div>
</div>
@endsection
