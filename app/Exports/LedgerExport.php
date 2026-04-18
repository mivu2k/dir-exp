<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LedgerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $lines;

    public function __construct($lines)
    {
        $this->lines = $lines;
    }

    public function collection()
    {
        return $this->lines;
    }

    public function headings(): array
    {
        return [
            ['DIR-EXPENSE CONSOLIDATED LEDGER TRANSCRIPT'],
            ['Exported At:', now()->format('Y-m-d H:i:s')],
            [''],
            ['Date', 'Voucher No', 'Director', 'Type', 'Category', 'Description', 'Amount (PKR)', 'Status'],
        ];
    }

    public function map($line): array
    {
        return [
            $line->date->format('Y-m-d'),
            $line->report->voucher_no ?? '—',
            $line->report?->director?->name ?? '—',
            strtoupper($line->type),
            $line->category?->name ?? '—',
            $line->description,
            ($line->type === 'income' ? '+' : '-') . round($line->amount),
            strtoupper($line->report->status ?? '—'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FF0F6CBD']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FFEFF6FC']],
            ],
        ];
    }
}
