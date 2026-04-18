<?php

namespace App\Exports;

use App\Models\ExpenseLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
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
            'Date',
            'Voucher No',
            'Director',
            'Category',
            'Description',
            'Amount',
            'Status'
        ];
    }

    public function map($line): array
    {
        return [
            $line->date->format('Y-m-d'),
            $line->report->voucher_no,
            $line->report->director->name,
            $line->category->name,
            $line->description,
            'Rs. ' . round($line->amount),
            $line->report->status,
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
