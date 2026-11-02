<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class LaporanExport implements WithMultipleSheets
{
    protected string $jenis;
    protected array $data;

    public function __construct(string $jenis, array $data)
    {
        $this->jenis = $jenis;
        $this->data  = $data;
    }

    public function sheets(): array
    {
        if ($this->jenis === 'bulanan') {
            return [
                new Sheets\RingkasanBulananSheet($this->data),
                new Sheets\RincianBulananSheet($this->data),
            ];
        } elseif ($this->jenis === 'tahunan') {
            return [
                new Sheets\RekapTahunanSheet($this->data),
            ];
        } else {
            return [
                new Sheets\RingkasanIuranSheet($this->data),
                new Sheets\DetailIuranSheet($this->data),
            ];
        }
    }
}