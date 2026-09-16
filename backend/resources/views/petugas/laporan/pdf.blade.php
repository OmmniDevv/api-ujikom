<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }
  h1 { text-align: center; font-size: 16px; margin: 0; color: #1e40af; }
  h2 { text-align: center; font-size: 11px; font-weight: normal; margin: 4px 0 12px; color: #475569; }
  .meta { text-align: right; font-size: 9px; color: #64748b; margin-bottom: 10px; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #1e40af; color: #fff; font-size: 9px; padding: 6px 4px; text-align: left; }
  td { padding: 5px 4px; border-bottom: 1px solid #e2e8f0; font-size: 9px; }
  tr:nth-child(even) td { background: #eff6ff; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; color: #fff; }
  .badge-diajukan { background: #f59e0b; }
  .badge-dipinjam { background: #3b82f6; }
  .badge-dikembalikan { background: #10b981; }
  .badge-telat { background: #ef4444; }
  .footer { margin-top: 16px; text-align: right; font-size: 9px; color: #64748b; }
</style>
</head>
<body>

<h1>LAPORAN PEMINJAMAN ALAT</h1>
<h2>SMKN 7 BALEENDAH</h2>
<div class="meta">Tanggal Cetak: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp; Total Data: {{ $peminjamans->count() }}</div>

<table>
  <thead>
    <tr>
      <th class="text-center" style="width: 28px">No</th>
      <th>Nama Peminjam</th>
      <th>Tgl Pinjam</th>
      <th>Rencana Kembali</th>
      <th>Nama Alat</th>
      <th class="text-center">Jml</th>
      <th>Status</th>
      <th>Tgl Kembali</th>
      <th>Kondisi</th>
      <th class="text-right">Denda</th>
    </tr>
  </thead>
  <tbody>
    @forelse($peminjamans as $i => $item)
      @php
        $alats = $item->detailPinjam->map(fn($d) => $d->alat->nama_alat ?? '-')->implode(', ');
        $jml = $item->detailPinjam->sum('jumlah');
        $tglKembali = $item->pengembalian?->tgl_kembali;
        $kondisi = $item->pengembalian?->kondisi_kembali ?? '-';
        $denda = $item->pengembalian?->denda ?? 0;
        $badgeClass = match($item->status) {
          'diajukan' => 'badge-diajukan',
          'dipinjam' => 'badge-dipinjam',
          'dikembalikan' => 'badge-dikembalikan',
          'telat' => 'badge-telat',
          default => 'badge-dipinjam',
        };
      @endphp
      <tr>
        <td class="text-center">{{ $i + 1 }}</td>
        <td>{{ $item->user->name ?? '-' }}</td>
        <td>{{ $item->tgl_pinjam instanceof \Carbon\Carbon ? $item->tgl_pinjam->format('d/m/Y') : $item->tgl_pinjam }}</td>
        <td>{{ $item->tgl_kembali_plan instanceof \Carbon\Carbon ? $item->tgl_kembali_plan->format('d/m/Y') : $item->tgl_kembali_plan }}</td>
        <td>{{ $alats }}</td>
        <td class="text-center">{{ $jml }}</td>
        <td><span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span></td>
        <td>{{ $tglKembali instanceof \Carbon\Carbon ? $tglKembali->format('d/m/Y') : ($tglKembali ?? '-') }}</td>
        <td>{{ ucfirst($kondisi) }}</td>
        <td class="text-right">Rp {{ number_format($denda, 0, ',', '.') }}</td>
      </tr>
    @empty
      <tr><td colspan="10" class="text-center" style="padding: 20px; color: #94a3b8">Belum ada data peminjaman.</td></tr>
    @endforelse
  </tbody>
</table>

<div class="footer">
  Dicetak oleh sistem pada {{ now()->format('d F Y, H:i') }} WIB
</div>

</body>
</html>
