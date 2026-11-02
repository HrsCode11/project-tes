<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMergeCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RingkasanBulananSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $d = $this->data;

        return [
            ['LAPORAN KEUANGAN BULANAN'],
            ['RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan'],
            [''],
            ['RINGKASAN KEUANGAN', ''],
            ['Pemasukan dari Iuran Warga', 'Rp ' . number_format($d['totalPemasukanIuran'], 0, ',', '.')],
            ['Pemasukan Non-Iuran', 'Rp ' . number_format($d['totalPemasukanNonIuran'], 0, ',', '.')],
            ['Total Pemasukan', 'Rp ' . number_format($d['totalPemasukan'], 0, ',', '.')],
            [''],
            ['Total Pengeluaran', 'Rp ' . number_format($d['totalPengeluaran'], 0, ',', '.')],
            [''],
            ['SALDO AKHIR', 'Rp ' . number_format($d['saldoAkhir'], 0, ',', '.')],
            [''],
            ['DETAIL PEMASUKAN', ''],
            ['Jumlah Transaksi Iuran', $d['pembayaran']->count() . ' transaksi'],
            ['Jumlah Pemasukan Non-Iuran', $d['pemasukan']->count() . ' transaksi'],
            [''],
            ['DETAIL PENGELUARAN', ''],
            ['Jumlah Transaksi Pengeluaran', $d['pengeluaran']->count() . ' transaksi'],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 40,
            'B' => 25,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Judul utama
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
            7 => [
                'font' => ['bold' => true],
                'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN]],
            ],
            11 => [
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '10B981']],
            ],
            13 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
            17 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
        ];
    }
}