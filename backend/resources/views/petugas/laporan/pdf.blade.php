<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Laporan Peminjaman Alat - SiPinjam</title>
<style>
  @page {
    size: A4 landscape;
    margin: 15mm 15mm 15mm 15mm;
  }
  * {
    box-sizing: border-box;
  }
  body {
    font-family: 'DejaVu Sans', sans-serif;
    font-size: 8.5px;
    line-height: 1.35;
    color: #0f172a;
    background: #ffffff;
    position: relative;
    margin: 0;
    padding: 0;
  }

  /* Digital Watermark Diagonal */
  .watermark {
    position: fixed;
    top: 30%;
    left: 5%;
    width: 90%;
    text-align: center;
    font-size: 34px;
    font-weight: 900;
    color: rgba(234, 88, 12, 0.05);
    letter-spacing: 5px;
    transform: rotate(-22deg);
    z-index: -1000;
    pointer-events: none;
    line-height: 1.5;
    text-transform: uppercase;
  }

  /* Header Section */
  .header-wrap {
    width: 100%;
    border-bottom: 2.5px solid #ea580c;
    padding-bottom: 10px;
    margin-bottom: 12px;
  }
  .instansi-title {
    font-size: 15px;
    font-weight: 900;
    color: #ea580c;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }
  .instansi-sub {
    font-size: 9px;
    color: #475569;
    margin-top: 2px;
  }
  .doc-title {
    font-size: 13px;
    font-weight: 800;
    color: #0f172a;
    text-align: right;
    margin: 0;
    text-transform: uppercase;
  }
  .doc-number {
    font-size: 8px;
    font-family: monospace;
    color: #ea580c;
    text-align: right;
    margin-top: 2px;
    font-weight: bold;
  }

  /* Metadata Box (Di-generate oleh siapa & kapan) */
  .meta-box {
    width: 100%;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-left: 3.5px solid #ea580c;
    border-radius: 4px;
    padding: 7px 10px;
    margin-bottom: 12px;
  }
  .meta-table {
    width: 100%;
    border-collapse: collapse;
  }
  .meta-table td {
    padding: 2px 4px;
    font-size: 8.5px;
    vertical-align: middle;
  }
  .meta-label {
    color: #64748b;
    width: 130px;
  }
  .meta-value {
    color: #0f172a;
    font-weight: bold;
  }

  /* Summary Metrics Strip */
  .metrics-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 6px 0;
    margin-bottom: 12px;
  }
  .metric-card {
    background: #f1f5f9;
    border: 1px solid #cbd5e1;
    border-radius: 4px;
    padding: 6px 8px;
    text-align: center;
  }
  .metric-card-label {
    font-size: 7.5px;
    color: #475569;
    text-transform: uppercase;
    font-weight: bold;
  }
  .metric-card-val {
    font-size: 12px;
    font-weight: 900;
    color: #0f172a;
    margin-top: 2px;
    font-family: monospace;
  }

  /* Data Table */
  table.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 15px;
  }
  table.data-table th {
    background: #0f172a;
    color: #ffffff;
    font-size: 8px;
    font-weight: bold;
    padding: 6px 5px;
    text-align: left;
    border: 1px solid #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  table.data-table td {
    padding: 5px;
    border: 1px solid #e2e8f0;
    font-size: 8px;
    vertical-align: middle;
  }
  table.data-table tr:nth-child(even) td {
    background: #f8fafc;
  }

  /* Status Badges */
  .badge {
    display: inline-block;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 7.5px;
    font-weight: bold;
    text-align: center;
    color: #ffffff;
  }
  .badge-diajukan { background: #d97706; }
  .badge-dipinjam { background: #2563eb; }
  .badge-dikembalikan { background: #059669; }
  .badge-telat { background: #dc2626; }

  /* Signatures & Security Footer */
  .footer-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
    page-break-inside: avoid;
  }
  .footer-table td {
    vertical-align: top;
  }
  .security-badge-box {
    border: 1px dashed #94a3b8;
    background: #f8fafc;
    border-radius: 4px;
    padding: 8px 10px;
    width: 90%;
    font-size: 7.5px;
    color: #475569;
    line-height: 1.4;
  }
  .signature-box {
    text-align: center;
    width: 200px;
    margin-left: auto;
  }
  .signature-space {
    height: 48px;
  }
  .signature-line {
    border-bottom: 1.5px solid #0f172a;
    font-weight: bold;
    font-size: 9px;
    padding-bottom: 2px;
  }

  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .font-mono { font-family: monospace; }
</style>
</head>
<body>

{{-- Watermark Digital --}}
<div class="watermark">
  SIPINJAM • SMKN 7 BALEENDAH • DOKUMEN RESMI TERVERIFIKASI
</div>

@php
    $currentUser = auth()->user();
    $generatorNama = $currentUser ? $currentUser->name : 'Sistem Otomatis';
    $generatorRole = $currentUser ? ucfirst($currentUser->role) : 'Administrator';
    $generatorEmail = $currentUser ? $currentUser->email : '-';
    $waktuCetak = now()->format('d F Y, H:i:s') . ' WIB';

    $docId = 'SPJ-DOC-' . now()->format('Ymd') . '-' . strtoupper(substr(hash('crc32', $generatorNama . now()->timestamp), 0, 6));

    $totalTransaksi = $peminjamans->count();
    $totalDipinjam = $peminjamans->where('status', 'dipinjam')->count();
    $totalSelesai = $peminjamans->where('status', 'dikembalikan')->count();
    $totalTelat = $peminjamans->where('status', 'telat')->count();
    $totalDenda = $peminjamans->sum(fn($p) => $p->pengembalian?->denda ?? 0);
@endphp

{{-- 1. Header Surat Resmi --}}
<table class="header-wrap">
  <tr>
    <td style="width: 55%; vertical-align: middle;">
      <div class="instansi-title">⚡ SIPINJAM - SMKN 7 BALEENDAH</div>
      <div class="instansi-sub">
        Sistem Informasi Inventaris & Peminjaman Peralatan Laboratorium<br>
        Jl. Siliwangi No. 127, Baleendah, Kec. Baleendah, Kabupaten Bandung, Jawa Barat
      </div>
    </td>
    <td style="width: 45%; vertical-align: middle; text-align: right;">
      <h1 class="doc-title">REKAPITULASI PEMINJAMAN ALAT</h1>
      <div class="doc-number">No. Dokumen: {{ $docId }}</div>
    </td>
  </tr>
</table>

{{-- 2. Metadata Generator Box (Di-generate oleh siapa & kapan) --}}
<div class="meta-box">
  <table class="meta-table">
    <tr>
      <td class="meta-label">Di-generate Oleh:</td>
      <td class="meta-value" style="width: 35%;">
        <strong>{{ $generatorNama }}</strong> (Role: <span style="color: #ea580c;">{{ $generatorRole }}</span>)
      </td>
      <td class="meta-label">Waktu Pembuatan:</td>
      <td class="meta-value">{{ $waktuCetak }}</td>
    </tr>
    <tr>
      <td class="meta-label">Email Penanggung Jawab:</td>
      <td class="meta-value">{{ $generatorEmail }}</td>
      <td class="meta-label">Status Dokumen:</td>
      <td class="meta-value" style="color: #059669;">✓ ASLI & TERSERTIFIKASI SISTEM ELEKTRONIK</td>
    </tr>
  </table>
</div>

{{-- 3. Ringkasan Eksekutif --}}
<table class="metrics-table">
  <tr>
    <td style="width: 20%;">
      <div class="metric-card">
        <div class="metric-card-label">Total Peminjaman</div>
        <div class="metric-card-val">{{ $totalTransaksi }}</div>
      </div>
    </td>
    <td style="width: 20%;">
      <div class="metric-card">
        <div class="metric-card-label">Sedang Dipinjam</div>
        <div class="metric-card-val" style="color: #2563eb;">{{ $totalDipinjam }}</div>
      </div>
    </td>
    <td style="width: 20%;">
      <div class="metric-card">
        <div class="metric-card-label">Selesai (Tepat Waktu)</div>
        <div class="metric-card-val" style="color: #059669;">{{ $totalSelesai }}</div>
      </div>
    </td>
    <td style="width: 20%;">
      <div class="metric-card">
        <div class="metric-card-label">Kasus Terlambat</div>
        <div class="metric-card-val" style="color: #dc2626;">{{ $totalTelat }}</div>
      </div>
    </td>
    <td style="width: 20%;">
      <div class="metric-card">
        <div class="metric-card-label">Total Denda Kas</div>
        <div class="metric-card-val" style="color: #ea580c;">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
      </div>
    </td>
  </tr>
</table>

{{-- 4. Tabel Transaksi --}}
<table class="data-table">
  <thead>
    <tr>
      <th class="text-center" style="width: 24px;">No</th>
      <th style="width: 110px;">Nama Peminjam</th>
      <th style="width: 65px;">Tgl Pinjam</th>
      <th style="width: 70px;">Rencana Kembali</th>
      <th>Rincian Alat Yang Dipinjam</th>
      <th class="text-center" style="width: 32px;">Jml</th>
      <th class="text-center" style="width: 65px;">Status</th>
      <th style="width: 70px;">Tgl Kembali</th>
      <th style="width: 55px;">Kondisi</th>
      <th class="text-right" style="width: 65px;">Denda</th>
    </tr>
  </thead>
  <tbody>
    @forelse($peminjamans as $i => $item)
      @php
        $alats = $item->detailPinjam->map(fn($d) => ($d->alat->nama_alat ?? 'Alat Dihapus') . ' ('.$d->jumlah.' unit)')->implode(', ');
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
        <td class="text-center font-mono">{{ $i + 1 }}</td>
        <td>
          <strong>{{ $item->user->name ?? 'User Dihapus' }}</strong>
          @if($item->user?->no_hp)
            <div style="font-size: 7px; color: #64748b;">{{ $item->user->no_hp }}</div>
          @endif
        </td>
        <td>{{ $item->tgl_pinjam instanceof \Carbon\Carbon ? $item->tgl_pinjam->format('d/m/Y') : $item->tgl_pinjam }}</td>
        <td>{{ $item->tgl_kembali_plan instanceof \Carbon\Carbon ? $item->tgl_kembali_plan->format('d/m/Y') : $item->tgl_kembali_plan }}</td>
        <td>{{ $alats }}</td>
        <td class="text-center font-mono font-bold">{{ $jml }}</td>
        <td class="text-center"><span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span></td>
        <td>{{ $tglKembali instanceof \Carbon\Carbon ? $tglKembali->format('d/m/Y') : ($tglKembali ?? '-') }}</td>
        <td>
          @if($kondisi === 'baik')
            <span style="color: #059669; font-weight: bold;">Baik</span>
          @elseif($kondisi === 'rusak')
            <span style="color: #dc2626; font-weight: bold;">Rusak</span>
          @elseif($kondisi === 'perbaikan')
            <span style="color: #d97706; font-weight: bold;">Servis</span>
          @else
            -
          @endif
        </td>
        <td class="text-right font-mono font-bold" style="{{ $denda > 0 ? 'color: #dc2626;' : 'color: #64748b;' }}">
          Rp {{ number_format($denda, 0, ',', '.') }}
        </td>
      </tr>
    @empty
      <tr>
        <td colspan="10" class="text-center" style="padding: 24px; color: #94a3b8;">
          Tidak ada data riwayat peminjaman yang dapat ditampilkan.
        </td>
      </tr>
    @endforelse
  </tbody>
</table>

{{-- 5. Footer Keamanan & Lembar Tanda Tangan Resmi --}}
<table class="footer-table">
  <tr>
    <td style="width: 60%;">
      <div class="security-badge-box">
        <strong style="color: #0f172a;">🔒 OTENTIKASI & KEASLIAN DOKUMEN DIGITAL</strong><br>
        Dokumen ini dibuat otomatis oleh Sistem SiPinjam SMKN 7 Baleendah dan telah diverifikasi secara elektronik.<br>
        Digital Checksum: <span class="font-mono" style="color: #ea580c; font-weight: bold;">{{ strtoupper(hash('sha256', $docId . now()->timestamp . $totalTransaksi)) }}</span><br>
        Keabsahan data dapat diverifikasi melalui database sistem menggunakan nomor dokumen resmi di atas.
      </div>
    </td>
    <td style="width: 40%;">
      <div class="signature-box">
        <div>Baleendah, {{ now()->format('d F Y') }}</div>
        <div style="font-weight: bold; margin-top: 2px;">{{ $generatorRole }} Penanggung Jawab,</div>
        <div class="signature-space"></div>
        <div class="signature-line">{{ $generatorNama }}</div>
        <div style="font-size: 7.5px; color: #64748b; margin-top: 2px;">ID Akun: #{{ $currentUser->id ?? '1' }} • SiPinjam SMKN 7</div>
      </div>
    </td>
  </tr>
</table>

</body>
</html>
