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

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];

        // Header
        $rows[] = ['Tanggal', 'Keterangan', 'Jenis', 'Pemasukan (Rp)', 'Pengeluaran (Rp)', 'Saldo Berjalan (Rp)'];

        $saldo = 0;

        // Gabungkan semua transaksi dan sort by tanggal
        $transaksi = collect();

        foreach ($this->data['pembayaran'] as $p) {
            $transaksi->push([
                'tgl'    => $p->tgl_bayar,
                'ket'    => 'Iuran ' . $p->tagihan->iuran->nama_iuran . ' — ' . $p->warga->nama_lengkap,
                'jenis'  => 'Pemasukan Iuran',
                'masuk'  => (int) $p->nominal,
                'keluar' => 0,
            ]);
        }

        foreach ($this->data['pemasukan'] as $p) {
            $transaksi->push([
                'tgl'    => $p->tgl_masuk,
                'ket'    => $p->keterangan . ($p->sumber ? ' (dari: ' . $p->sumber . ')' : ''),
                'jenis'  => 'Pemasukan ' . ucfirst($p->kategori),
                'masuk'  => (int) $p->jumlah,
                'keluar' => 0,
            ]);
        }

        foreach ($this->data['pengeluaran'] as $p) {
            $transaksi->push([
                'tgl'    => $p->tgl_keluar,
                'ket'    => $p->keterangan . ($p->penerima ? ' (ke: ' . $p->penerima . ')' : ''),
                'jenis'  => 'Pengeluaran ' . ucfirst($p->kategori),
                'masuk'  => 0,
                'keluar' => (int) $p->jumlah,
            ]);
        }

        // Sort by tanggal
        $transaksi = $transaksi->sortBy('tgl')->values();

        foreach ($transaksi as $t) {
            $saldo += $t['masuk'] - $t['keluar'];
            $rows[] = [
                $t['tgl']->format('d/m/Y'),
                $t['ket'],
                $t['jenis'],
                $t['masuk'] > 0 ? $t['masuk'] : '',
                $t['keluar'] > 0 ? $t['keluar'] : '',
                $saldo,
            ];
        }

        // Baris total
        $rows[] = [
            '',
            '',
            'TOTAL',
            $this->data['totalPemasukan'],
            $this->data['totalPengeluaran'],
            $this->data['saldoAkhir'],
        ];

        return $rows;
    }

    public function title(): string
    {
        return 'Rincian Transaksi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 45,
            'C' => 22,
            'D' => 20,
            'E' => 20,
            'F' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
        ];

        // Style baris total
        $totalStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1B2A4A']],
        ];

        $sheet->getStyle("A1:F1")->applyFromArray($headerStyle);
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->applyFromArray($totalStyle);

        // Border semua data
        $sheet->getStyle("A1:F{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
        ]);

        // Format angka kolom D, E, F
        $sheet->getStyle("D2:F{$lastRow}")->getNumberFormat()
            ->setFormatCode('#,##0');

        // Warna baris data selang-seling
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