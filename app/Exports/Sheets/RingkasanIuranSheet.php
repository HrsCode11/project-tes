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

class RingkasanIuranSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;
    protected int $tableHeaderRow = 0;
    protected int $tableEndRow    = 0;
    protected int $footerRow      = 0;

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
        $d      = $this->data;
        $iuran  = $d['iuran'];
        $persen = $d['totalLunas'] > 0
            ? round(($d['totalLunas'] / $d['tagihan']->count()) * 100)
            : 0;

        $rows = [
            ['LAPORAN IURAN — ' . strtoupper($iuran->nama_iuran), '', '', '', ''],
            ['RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan', '', '', '', ''],
            ['Nominal: ' . $this->rp($iuran->nominal) . ' · Deadline: ' . $iuran->deadline->format('d M Y'), '', '', '', ''],
            ['', '', '', '', ''],
            ['RINGKASAN IURAN', '', '', '', ''],
            ['Total Warga', $d['tagihan']->count() . ' warga', '', '', ''],
            ['Sudah Lunas', $d['totalLunas'] . ' warga (' . $persen . '%)', '', '', ''],
            ['Belum Lunas', $d['totalBelum'] . ' warga', '', '', ''],
            ['Dana Terkumpul', $this->rp($d['totalTerkumpul']), '', '', ''],
            ['Target', $this->rp($d['totalTarget']), '', '', ''],
            ['Sisa Target', $this->rp($d['totalTarget'] - $d['totalTerkumpul']), '', '', ''],
            ['', '', '', '', ''],
            ['DETAIL PEMBAYARAN WARGA', '', '', '', ''],
            ['No', 'Nama Warga', 'No. RT', 'Nominal', 'Status'],
        ];

        $this->tableHeaderRow = count($rows);

        foreach ($d['tagihan'] as $i => $t) {
            $rows[] = [
                $i + 1,
                $t->warga->nama_lengkap,
                $t->warga->no_rt,
                $this->rp($t->nominal),
                match ($t->status) {
                    'lunas'       => 'Lunas',
                    'menunggu'    => 'Menunggu',
                    'belum_bayar' => 'Belum Bayar',
                },
            ];
        }

        $this->tableEndRow = count($rows);

        $rows[] = ['', '', '', '', ''];

        $pencatat = auth()->user()?->nama_lengkap ?? 'Sistem';
        $rows[] = [
            'Digenerate otomatis pada ' . now()->translatedFormat('d F Y, H:i') . ' oleh ' . $pencatat,
            '', '', '', '',
        ];
        $this->footerRow = count($rows);

        return $rows;
    }

    public function title(): string
    {
        return 'Laporan Iuran';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 35,
            'C' => 14,
            'D' => 20,
            'E' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header utama
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');

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

        // Ringkasan Iuran
        $sheet->mergeCells('A5:B5');
        $sheet->getStyle('A5:B5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
        ]);
        $sheet->getStyle('A5:B11')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);
        $sheet->getStyle('B6:B11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('B7')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '10B981']]]);
        $sheet->getStyle('B8')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => 'EF4444']]]);
        $sheet->getStyle('B9')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '10B981']]]);
        $sheet->getStyle('B10')->applyFromArray(['font' => ['bold' => true, 'color' => ['rgb' => '3B82F6']]]);

        // Judul bagian detail
        $sectionRow = $this->tableHeaderRow - 1;
        $sheet->mergeCells("A{$sectionRow}:E{$sectionRow}");
        $sheet->getStyle("A{$sectionRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1B2A4A']],
        ]);

        // Header tabel detail
        $hRow = $this->tableHeaderRow;
        $sheet->getStyle("A{$hRow}:E{$hRow}")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Border & warna baris status pada tabel detail
        if ($this->tableEndRow >= $hRow) {
            $sheet->getStyle("A{$hRow}:E{$this->tableEndRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            for ($i = $hRow + 1; $i <= $this->tableEndRow; $i++) {
                $sheet->getStyle("A{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("C{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle("E{$i}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $status = $sheet->getCell("E{$i}")->getValue();
                if ($status === 'Lunas') {
                    $sheet->getStyle("E{$i}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '059669']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                    ]);
                } elseif ($status === 'Menunggu') {
                    $sheet->getStyle("E{$i}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'D97706']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                    ]);
                } elseif ($status === 'Belum Bayar') {
                    $sheet->getStyle("E{$i}")->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'DC2626']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                    ]);
                }
            }
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