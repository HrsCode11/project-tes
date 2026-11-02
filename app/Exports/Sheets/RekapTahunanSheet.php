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

class RekapTahunanSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Bulan', 'Pemasukan Iuran (Rp)', 'Pemasukan Non-Iuran (Rp)', 'Total Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Saldo (Rp)'];

        foreach ($this->data['dataBulanan'] as $b) {
            $rows[] = [
                $b['nama_bulan'],
                (int) $b['pemasukan_iuran'],
                (int) $b['pemasukan_nonIuran'],
                (int) $b['total_pemasukan'],
                (int) $b['pengeluaran'],
                (int) $b['saldo'],
            ];
        }

        // Total
        $rows[] = [
            'TOTAL',
            collect($this->data['dataBulanan'])->sum('pemasukan_iuran'),
            collect($this->data['dataBulanan'])->sum('pemasukan_nonIuran'),
            $this->data['totalPemasukan'],
            $this->data['totalPengeluaran'],
            $this->data['saldoAkhir'],
        ];

        return $rows;
    }

    public function title(): string
    {
        return 'Rekap Tahunan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 22,
            'C' => 25,
            'D' => 22,
            'E' => 20,
            'F' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:F1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
        ]);

        $sheet->getStyle("B2:F{$lastRow}")->getNumberFormat()
            ->setFormatCode('#,##0');

        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);

        // Warna saldo negatif merah
        for ($i = 2; $i < $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:F{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFF']],
                ]);
            }
        }

        return [];
    }
}