<?php

namespace App\Exports;

use Carbon\Carbon;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanPeminjamanExport
{
    protected $peminjamans;

    public function __construct($peminjamans)
    {
        $this->peminjamans = $peminjamans;
    }

    public function download(string $filename): StreamedResponse
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'laporan_').'.xlsx';

        $writer = new Writer;
        $writer->openToFile($tmpFile);

        // ===== STYLE HEADER =====
        $headerStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(10)
            ->withFontColor(Color::WHITE)
            ->withBackgroundColor('EA580C') // Oranye SiPinjam
            ->withShouldWrapText(false);

        // ===== STYLE TITLE =====
        $titleStyle = (new Style())
            ->withFontBold(true)
            ->withFontSize(14)
            ->withFontColor('EA580C');

        $metaStyle = (new Style())
            ->withFontItalic(true)
            ->withFontSize(9)
            ->withFontColor('475569');

        // ===== STYLE ZEBRA =====
        $zebraStyle = (new Style())
            ->withBackgroundColor('FFF7ED') // warm light orange zebra
            ->withFontSize(9);

        $normalStyle = (new Style())
            ->withFontSize(9);

        // Metadata pembuat laporan
        $generatorName = auth()->user()->name ?? 'System';
        $generatorRole = ucfirst(auth()->user()->role ?? 'Admin');
        $waktuGenerate = now()->format('d F Y, H:i:s') . ' WIB';

        // ===== TITLE ROW =====
        $writer->addRow(Row::fromValuesWithStyle(
            ['LAPORAN REKAPITULASI PEMINJAMAN ALAT - SIPINJAM SMKN 7 BALEENDAH'],
            $titleStyle
        ));
        $writer->addRow(Row::fromValuesWithStyle(
            ["Di-generate oleh: {$generatorName} ({$generatorRole}) | Tanggal & Waktu: {$waktuGenerate}"],
            $metaStyle
        ));
        $writer->addRow(Row::fromValuesWithStyle(
            ['Total Data Transaksi: ' . count($this->peminjamans) . ' transaksi'],
            $metaStyle
        ));
        $writer->addRow(Row::fromValues([''])); // baris kosong

        // ===== HEADER ROW =====
        $writer->addRow(Row::fromValuesWithStyle([
            'No',
            'Nama Peminjam',
            'Tanggal Pinjam',
            'Rencana Kembali',
            'Nama Alat Dipinjam',
            'Jumlah Unit',
            'Status Peminjaman',
            'Tgl Kembali Aktual',
            'Kondisi Kembali',
            'Nominal Denda (Rp)',
        ], $headerStyle));

        // ===== DATA ROWS =====
        $no = 1;
        foreach ($this->peminjamans as $item) {
            $alats = $item->detailPinjam->map(fn ($d) => $d->alat->nama_alat ?? '-')->implode(', ');
            $jumlahAlat = $item->detailPinjam->sum('jumlah');
            $tglKembali = $item->pengembalian?->tgl_kembali ?? '-';
            $kondisi = $item->pengembalian?->kondisi_kembali ?? '-';
            $denda = $item->pengembalian?->denda ?? 0;

            $statusLabel = match ($item->status) {
                'diajukan' => 'Diajukan',
                'dipinjam' => 'Dipinjam',
                'dikembalikan' => 'Dikembalikan',
                'telat' => 'Telat',
                default => ucfirst($item->status),
            };

            $style = ($no % 2 === 0) ? $zebraStyle : $normalStyle;

            $writer->addRow(Row::fromValuesWithStyle([
                $no,
                $item->user->name ?? '-',
                $item->tgl_pinjam instanceof Carbon
                    ? $item->tgl_pinjam->format('d/m/Y')
                    : (string) $item->tgl_pinjam,
                $item->tgl_kembali_plan instanceof Carbon
                    ? $item->tgl_kembali_plan->format('d/m/Y')
                    : (string) $item->tgl_kembali_plan,
                $alats,
                (int) $jumlahAlat,
                $statusLabel,
                $tglKembali instanceof Carbon
                    ? $tglKembali->format('d/m/Y')
                    : (string) $tglKembali,
                ucfirst($kondisi),
                (int) $denda,
            ], $style));

            $no++;
        }

        $writer->close();

        // Stream ke browser
        $content = file_get_contents($tmpFile);
        unlink($tmpFile);

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
