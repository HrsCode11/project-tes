<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RincianBulananSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    protected array $sectionRows = [];
    protected array $headerRows  = [];
    protected array $totalRows   = [];
    protected array $dataRanges  = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $d    = $this->data;
        $rows = [];
        $row  = 0;

        // 1. Pemasukan dari Iuran
        if ($d['pembayaran']->count() > 0) {
            $rekapIuran = $d['pembayaran']->groupBy(fn($p) => $p->tagihan->iuran_id);

            $rows[] = ['PEMASUKAN DARI IURAN', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Nama Iuran', 'Jumlah Pembayar', 'Nominal / Warga (Rp)', 'Total Terkumpul (Rp)', ''];
            $this->headerRows[] = [$row + 1, 'D'];
            $row++;

            $startData = $row + 1;
            foreach ($rekapIuran as $grup) {
                $itemPertama = $grup->first();
                $rows[] = [
                    $itemPertama->tagihan->iuran->nama_iuran,
                    $grup->count() . ' warga',
                    (int) $itemPertama->nominal,
                    (int) $grup->sum('nominal'),
                    '',
                ];
                $row++;
            }
            $endData = $row;

            $rows[] = [
                'Total (' . $d['pembayaran']->count() . ' transaksi)',
                '',
                '',
                (int) $d['totalPemasukanIuran'],
                '',
            ];
            $this->totalRows[]  = [++$row, 'D', '10B981'];
            $this->dataRanges[] = [$startData - 1, $row, 'D', ['C', 'D']];

            $rows[] = ['', '', '', '', ''];
            $row++;
        }

        // 2. Pemasukan Non-Iuran
        if ($d['pemasukan']->count() > 0) {
            $rows[] = ['PEMASUKAN NON-IURAN', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Tanggal', 'Keterangan', 'Kategori', 'Sumber', 'Jumlah (Rp)'];
            $this->headerRows[] = [$row + 1, 'E'];
            $row++;

            $startData = $row + 1;
            foreach ($d['pemasukan'] as $p) {
                $rows[] = [
                    $p->tgl_masuk->format('d/m/Y'),
                    $p->keterangan,
                    ucfirst($p->kategori),
                    $p->sumber ?? '—',
                    (int) $p->jumlah,
                ];
                $row++;
            }

            $rows[] = [
                'Total',
                '',
                '',
                '',
                (int) $d['totalPemasukanNonIuran'],
            ];
            $this->totalRows[]  = [++$row, 'E', '10B981'];
            $this->dataRanges[] = [$startData - 1, $row, 'E', ['E']];

            $rows[] = ['', '', '', '', ''];
            $row++;
        }

        // 3. Pengeluaran
        if ($d['pengeluaran']->count() > 0) {
            $rows[] = ['PENGELUARAN', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Tanggal', 'Keterangan', 'Kategori', 'Penerima', 'Jumlah (Rp)'];
            $this->headerRows[] = [$row + 1, 'E'];
            $row++;

            $startData = $row + 1;
            foreach ($d['pengeluaran'] as $p) {
                $rows[] = [
                    $p->tgl_keluar->format('d/m/Y'),
                    $p->keterangan,
                    ucfirst($p->kategori),
                    $p->penerima ?? '—',
                    (int) $p->jumlah,
                ];
                $row++;
            }

            $rows[] = [
                'Total',
                '',
                '',
                '',
                (int) $d['totalPengeluaran'],
            ];
            $this->totalRows[]  = [++$row, 'E', 'EF4444'];
            $this->dataRanges[] = [$startData - 1, $row, 'E', ['E']];
        }

        if (empty($rows)) {
            $rows[] = ['Tidak ada transaksi pada bulan ini.'];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Rincian Transaksi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 35,
            'C' => 22,
            'D' => 24,
            'E' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Judul tiap bagian
        foreach ($this->sectionRows as $r) {
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1B2A4A']],
            ]);
        }

        // Header tabel
        foreach ($this->headerRows as [$r, $endCol]) {
            $sheet->getStyle("A{$r}:{$endCol}{$r}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Border & format angka per tabel
        foreach ($this->dataRanges as [$startRow, $endRow, $endCol, $numCols]) {
            $sheet->getStyle("A{$startRow}:{$endCol}{$endRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            foreach ($numCols as $col) {
                $dataStart = $startRow + 1;
                $sheet->getStyle("{$col}{$dataStart}:{$col}{$endRow}")->getNumberFormat()
                    ->setFormatCode('#,##0');
            }
        }

        // Baris total tiap tabel
        foreach ($this->totalRows as [$r, $endCol, $color]) {
            $prevCol = chr(ord($endCol) - 1);
            $sheet->mergeCells("A{$r}:{$prevCol}{$r}");
            $sheet->getStyle("A{$r}:{$endCol}{$r}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFF']],
            ]);
            $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("{$endCol}{$r}")->getFont()->getColor()->setRGB($color);
        }

        return [];
    }
}