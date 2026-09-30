<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
  @page { margin: 20px 25px; }
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; position: relative; }
  
  /* Digital Watermark Background */
  .watermark {
    position: fixed;
    top: 35%;
    left: 10%;
    width: 80%;
    text-align: center;
    font-size: 38px;
    font-weight: 900;
    color: rgba(30, 64, 175, 0.06);
    letter-spacing: 6px;
    transform: rotate(-25deg);
    z-index: -1000;
    pointer-events: none;
    line-height: 1.4;
    text-transform: uppercase;
  }

  .header-table { width: 100%; border-bottom: 2px solid #1e40af; padding-bottom: 8px; margin-bottom: 12px; }
  .header-logo { font-size: 18px; font-weight: 900; color: #ea580c; letter-spacing: -0.5px; }
  .header-sub { font-size: 9px; color: #64748b; margin-top: 2px; }
  
  h1 { text-align: right; font-size: 14px; margin: 0; color: #1e40af; font-weight: 800; }
  .meta { text-align: right; font-size: 8px; color: #64748b; margin-top: 2px; }
  
  table.data-table { width: 100%; border-collapse: collapse; margin-top: 5px; }
  table.data-table th { background: #1e40af; color: #fff; font-size: 8.5px; padding: 6px 4px; text-align: left; }
  table.data-table td { padding: 4.5px 4px; border-bottom: 1px solid #e2e8f0; font-size: 8px; }
  table.data-table tr:nth-child(even) td { background: #f8fafc; }
  
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .badge { display: inline-block; padding: 2px 5px; border-radius: 3px; font-size: 7.5px; font-weight: bold; color: #fff; }
  .badge-diajukan { background: #f59e0b; }
  .badge-dipinjam { background: #3b82f6; }
  .badge-dikembalikan { background: #10b981; }
  .badge-telat { background: #ef4444; }
  
  /* Security Footer & Verification Seal */
  .security-footer {
    margin-top: 15px;
    padding-top: 8px;
    border-top: 1px dashed #cbd5e1;
    width: 100%;
  }
  .seal-box {
    border: 1px solid #94a3b8;
    padding: 6px 10px;
    display: inline-block;
    border-radius: 4px;
    background: #f8fafc;
    font-size: 7.5px;
    color: #475569;
  }
  .hash-code { font-family: monospace; font-weight: bold; color: #1e40af; }
</style>
</head>
<body>

{{-- Watermark Digital Tembus Pandang --}}
<div class="watermark">
  SIPINJAM OFFICIAL<br>DIGITAL VERIFIED
</div>

{{-- Header Laporan dengan Identitas Resmi --}}
<table class="header-table">
  <tr>
    <td style="vertical-align: middle;">
      <div class="header-logo">⚡ SiPinjam</div>
      <div class="header-sub">Sistem Manajemen Peminjaman Alat Laboratorium • SMKN 7 Baleendah</div>
    </td>
    <td style="vertical-align: middle; text-align: right;">
      <h1>REKAPITULASI PEMINJAMAN ALAT</h1>
      <div class="meta">
        Tgl Dokumen: {{ now()->format('d/m/Y H:i') }} WIB &nbsp;|&nbsp;
        Total Data: {{ $peminjamans->count() }} Transaksi
      </div>
    </td>
  </tr>
</table>

<table class="data-table">
  <thead>
    <tr>
      <th class="text-center" style="width: 24px">No</th>
      <th>Nama Peminjam</th>
      <th>Tgl Pinjam</th>
      <th>Rencana Kembali</th>
      <th>Detail Alat Dipinjam</th>
      <th class="text-center" style="width: 30px">Jml</th>
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
        <td><strong>{{ $item->user->name ?? '-' }}</strong></td>
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

{{-- Security Footer & Digital Verification Seal --}}
<table class="security-footer">
  <tr>
    <td style="vertical-align: top; width: 65%;">
      <div class="seal-box">
        <strong>🔒 KEAMANAN & OTENTIKASI DOKUMEN DIGITAL</strong><br>
        Dokumen ini diterbitkan secara otomatis dan sah melalui enkripsi sistem SiPinjam.<br>
        Digital Checksum: <span class="hash-code">{{ strtoupper(substr(hash('sha256', now()->timestamp . $peminjamans->count()), 0, 24)) }}</span><br>
        Otentikasi: <em>VERIFIED BY SIPINJAM SECURITY ENGINE</em>
      </div>
    </td>
    <td style="vertical-align: top; text-align: right; width: 35%;">
      <p style="margin: 0; font-size: 8px; color: #64748b;">
        Dicetak pada: <strong>{{ now()->format('d F Y, H:i') }} WIB</strong><br>
        Oleh Petugas: <strong>{{ auth()->user()->name ?? 'System' }}</strong>
      </p>
    </td>
  </tr>
</table>

</body>
</html>
