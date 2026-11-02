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

class DetailIuranSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows   = [];
        $rows[] = ['No', 'Nama Warga', 'NIK', 'No. RT', 'Nominal (Rp)', 'Status', 'Tgl Bayar', 'Metode'];

        foreach ($this->data['tagihan'] as $i => $t) {
            $pembayaran = $t->pembayaran;
            $rows[] = [
                $i + 1,
                $t->warga->nama_lengkap,
                $t->warga->nik,
                $t->warga->no_rt,
                (int) $t->nominal,
                match($t->status) {
                    'lunas'       => 'Lunas',
                    'menunggu'    => 'Menunggu',
                    'belum_bayar' => 'Belum Bayar',
                },
                $pembayaran && $pembayaran->tgl_bayar
                    ? $pembayaran->tgl_bayar->format('d/m/Y')
                    : '—',
                $pembayaran
                    ? strtoupper($pembayaran->metode)
                    : '—',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Detail per Warga';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 8,
            'E' => 18,
            'F' => 14,
            'G' => 14,
            'H' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header
        $sheet->getStyle("A1:H1")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // Border semua
        $sheet->getStyle("A1:H{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);

        // Format angka nominal
        $sheet->getStyle("E2:E{$lastRow}")->getNumberFormat()
            ->setFormatCode('#,##0');

        // Warna per status
        for ($i = 2; $i <= $lastRow; $i++) {
            $status = $sheet->getCell("F{$i}")->getValue();

            if ($status === 'Lunas') {
                $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                ]);
            } elseif ($status === 'Menunggu') {
                $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']],
                ]);
            } elseif ($status === 'Belum Bayar') {
                $sheet->getStyle("A{$i}:H{$i}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
                ]);
            }
        }

        return [];
    }
}