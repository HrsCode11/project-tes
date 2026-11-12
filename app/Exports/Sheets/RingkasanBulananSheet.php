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

class RingkasanBulananSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;
    protected array $sectionRows = [];
    protected array $headerRows  = [];
    protected array $totalRows   = [];
    protected array $dataRanges  = [];
    protected ?int $footerRow    = null;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    private function rp(float|int $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    public function array(): array
    {
        $d = $this->data;
        $periode = isset($d['bulan'], $d['tahun'])
            ? 'Periode: ' . \Carbon\Carbon::create()->month($d['bulan'])->translatedFormat('F') . ' ' . $d['tahun']
            : '';

        $rows = [
            ['LAPORAN KEUANGAN BULANAN', '', '', '', ''],
            ['RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan', '', '', '', ''],
            [$periode, '', '', '', ''],
            ['', '', '', '', ''],
            ['RINGKASAN KEUANGAN', '', '', '', ''],
            ['Pemasukan dari Iuran Warga', $this->rp($d['totalPemasukanIuran']), '', '', ''],
            ['Pemasukan Non-Iuran', $this->rp($d['totalPemasukanNonIuran']), '', '', ''],
            ['Total Pemasukan', $this->rp($d['totalPemasukan']), '', '', ''],
            ['Total Pengeluaran', $this->rp($d['totalPengeluaran']), '', '', ''],
            ['SALDO AKHIR', $this->rp($d['saldoAkhir']), '', '', ''],
            ['', '', '', '', ''],
        ];

        $row = count($rows);

        // 1. Pemasukan dari Iuran
        if ($d['pembayaran']->count() > 0) {
            $rekapIuran = $d['pembayaran']->groupBy(fn($p) => $p->tagihan->iuran_id);

            $rows[] = ['Pemasukan dari Iuran', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Nama Iuran', 'Jumlah Pembayar', 'Nominal / Warga', 'Total Terkumpul', ''];
            $this->headerRows[] = [$row + 1, 'D'];
            $row++;

            $startTable = $row;
            foreach ($rekapIuran as $grup) {
                $itemPertama = $grup->first();
                $rows[] = [
                    $itemPertama->tagihan->iuran->nama_iuran,
                    $grup->count() . ' warga',
                    $this->rp($itemPertama->nominal),
                    $this->rp($grup->sum('nominal')),
                    '',
                ];
                $row++;
            }

            $rows[] = [
                'Total (' . $d['pembayaran']->count() . ' transaksi)',
                '',
                '',
                $this->rp($d['totalPemasukanIuran']),
                '',
            ];
            $this->totalRows[]  = [++$row, 'D', '10B981'];
            $this->dataRanges[] = [$startTable, $row, 'D', ['C', 'D']];

            $rows[] = ['', '', '', '', ''];
            $row++;
        }

        // 2. Pemasukan Non-Iuran
        if ($d['pemasukan']->count() > 0) {
            $rows[] = ['Pemasukan Non-Iuran', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Tanggal', 'Keterangan', 'Kategori', 'Sumber', 'Jumlah'];
            $this->headerRows[] = [$row + 1, 'E'];
            $row++;

            $startTable = $row;
            foreach ($d['pemasukan'] as $p) {
                $rows[] = [
                    $p->tgl_masuk->format('d/m/Y'),
                    $p->keterangan,
                    ucfirst($p->kategori),
                    $p->sumber ?? '—',
                    $this->rp($p->jumlah),
                ];
                $row++;
            }

            $rows[] = [
                'Total',
                '',
                '',
                '',
                $this->rp($d['totalPemasukanNonIuran']),
            ];
            $this->totalRows[]  = [++$row, 'E', '10B981'];
            $this->dataRanges[] = [$startTable, $row, 'E', ['E']];

            $rows[] = ['', '', '', '', ''];
            $row++;
        }

        // 3. Pengeluaran
        if ($d['pengeluaran']->count() > 0) {
            $rows[] = ['Pengeluaran', '', '', '', ''];
            $this->sectionRows[] = ++$row;

            $rows[] = ['Tanggal', 'Keterangan', 'Kategori', 'Penerima', 'Jumlah'];
            $this->headerRows[] = [$row + 1, 'E'];
            $row++;

            $startTable = $row;
            foreach ($d['pengeluaran'] as $p) {
                $rows[] = [
                    $p->tgl_keluar->format('d/m/Y'),
                    $p->keterangan,
                    ucfirst($p->kategori),
                    $p->penerima ?? '—',
                    $this->rp($p->jumlah),
                ];
                $row++;
            }

            $rows[] = [
                'Total',
                '',
                '',
                '',
                $this->rp($d['totalPengeluaran']),
            ];
            $this->totalRows[]  = [++$row, 'E', 'EF4444'];
            $this->dataRanges[] = [$startTable, $row, 'E', ['E']];

            $rows[] = ['', '', '', '', ''];
            $row++;
        }

        // Footer seperti di PDF
        $pencatat = auth()->user()?->nama_lengkap ?? 'Sistem';
        $rows[] = [
            'Digenerate otomatis pada ' . now()->translatedFormat('d F Y, H:i') . ' oleh ' . $pencatat,
            '', '', '', '',
        ];
        $this->footerRow = ++$row;

        return $rows;
    }

    public function title(): string
    {
        return 'Laporan Bulanan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 32,
            'B' => 32,
            'C' => 22,
            'D' => 24,
            'E' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header utama
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->mergeCells('A5:B5');

        $sheet->getStyle('A1:E3')->applyFromArray([
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => 'FFFFFF']],
        ]);

        // Ringkasan Keuangan
        $sheet->getStyle('A5:B5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
        ]);
        $sheet->getStyle('A5:B10')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);
        $sheet->getStyle('B6:B10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('A8:B8')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '10B981']],
        ]);
        $sheet->getStyle('A9:B9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'EF4444']],
        ]);
        $sheet->getStyle('A10:B10')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
        ]);

        // Judul tiap bagian detail
        foreach ($this->sectionRows as $r) {
            $sheet->getStyle("A{$r}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1B2A4A']],
            ]);
        }

        // Header tabel detail
        foreach ($this->headerRows as [$r, $endCol]) {
            $sheet->getStyle("A{$r}:{$endCol}{$r}")->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        // Border & rata kanan kolom nominal
        foreach ($this->dataRanges as [$startRow, $endRow, $endCol, $rightCols]) {
            $sheet->getStyle("A{$startRow}:{$endCol}{$endRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            foreach ($rightCols as $col) {
                $dataStart = $startRow + 1;
                $sheet->getStyle("{$col}{$dataStart}:{$col}{$endRow}")
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        // Baris total tiap tabel detail
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

        // Footer
        if ($this->footerRow) {
            $sheet->mergeCells("A{$this->footerRow}:E{$this->footerRow}");
            $sheet->getStyle("A{$this->footerRow}")->applyFromArray([
                'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '94A3B8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
            ]);
        }

        return [];
    }
}