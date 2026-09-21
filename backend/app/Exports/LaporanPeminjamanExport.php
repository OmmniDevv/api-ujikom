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
        $headerStyle = new Style;
        $headerStyle->setFontBold();
        $headerStyle->setFontSize(11);
        $headerStyle->setFontColor(Color::WHITE);
        $headerStyle->setBackgroundColor('1E40AF'); // biru gelap
        $headerStyle->setShouldWrapText(false);

        // ===== STYLE TITLE =====
        $titleStyle = new Style;
        $titleStyle->setFontBold();
        $titleStyle->setFontSize(14);
        $titleStyle->setFontColor('1E40AF');

        // ===== STYLE ZEBRA =====
        $zebraStyle = new Style;
        $zebraStyle->setBackgroundColor('EFF6FF'); // biru muda
        $zebraStyle->setFontSize(10);

        $normalStyle = new Style;
        $normalStyle->setFontSize(10);

        // ===== TITLE ROW =====
        $writer->addRow(Row::fromValues(
            ['LAPORAN PEMINJAMAN ALAT - SMKN 7 BALEENDAH'],
            $titleStyle
        ));
        $writer->addRow(Row::fromValues(['Tanggal Cetak: '.now()->format('d/m/Y H:i')]));
        $writer->addRow(Row::fromValues([''])); // baris kosong

        // ===== HEADER ROW =====
        $writer->addRow(Row::fromValues([
            'No',
            'Nama Peminjam',
            'Tanggal Pinjam',
            'Rencana Kembali',
            'Nama Alat',
            'Jumlah',
            'Status',
            'Tgl Kembali Aktual',
            'Kondisi Kembali',
            'Denda (Rp)',
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

            $writer->addRow(Row::fromValues([
                $no,
                $item->user->name ?? '-',
                $item->tgl_pinjam instanceof Carbon
                    ? $item->tgl_pinjam->format('d/m/Y')
                    : (string) $item->tgl_pinjam,
                $item->tgl_kembali_plan instanceof Carbon
                    ? $item->tgl_kembali_plan->format('d/m/Y')
                    : (string) $item->tgl_kembali_plan,
                $alats,
                $jumlahAlat,
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
