<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class RingkasanIuranSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $d    = $this->data;
        $iuran = $d['iuran'];
        $persen = $d['totalLunas'] > 0
            ? round(($d['totalLunas'] / $d['tagihan']->count()) * 100)
            : 0;

        return [
            ['LAPORAN IURAN'],
            [strtoupper($iuran->nama_iuran)],
            ['RT 04 · Kelurahan Dukuh · Kecamatan Lembeyan · Kabupaten Magetan'],
            [''],
            ['INFORMASI IURAN', ''],
            ['Nama Iuran', $iuran->nama_iuran],
            ['Nominal per Warga', 'Rp ' . number_format($iuran->nominal, 0, ',', '.')],
            ['Deadline', $iuran->deadline->format('d M Y')],
            ['Status', ucfirst($iuran->status)],
            [''],
            ['REKAP PEMBAYARAN', ''],
            ['Total Warga', $d['tagihan']->count() . ' warga'],
            ['Sudah Lunas', $d['totalLunas'] . ' warga (' . $persen . '%)'],
            ['Belum Lunas', $d['totalBelum'] . ' warga'],
            [''],
            ['REKAP KEUANGAN', ''],
            ['Dana Terkumpul', 'Rp ' . number_format($d['totalTerkumpul'], 0, ',', '.')],
            ['Target', 'Rp ' . number_format($d['totalTarget'], 0, ',', '.')],
            ['Sisa Target', 'Rp ' . number_format($d['totalTarget'] - $d['totalTerkumpul'], 0, ',', '.')],
        ];
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 30];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:B1');
        $sheet->mergeCells('A2:B2');
        $sheet->mergeCells('A3:B3');

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            3 => [
                'font' => ['size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            5 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
            11 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
            13 => ['font' => ['color' => ['rgb' => '10B981'], 'bold' => true]],
            14 => ['font' => ['color' => ['rgb' => 'EF4444']]],
            16 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2C4A7A']],
            ],
            17 => ['font' => ['color' => ['rgb' => '10B981'], 'bold' => true]],
        ];
    }
}