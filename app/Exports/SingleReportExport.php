<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SingleReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $report;

    public function __construct($report)
    {
        $this->report = $report;
    }

    public function collection()
    {
        return $this->report->lines;
    }

    public function headings(): array
    {
        return [
            ['DIR-EXPENSE OFFICIAL FINANCIAL TRANSCRIPT'],
            ['Protocol ID:', $this->report->voucher_no],
            ['Subject:', $this->report->title],
            ['Director:', $this->report->director->name],
            ['Status:', strtoupper($this->report->status)],
            ['Exported At:', now()->format('Y-m-d H:i:s')],
            [''],
            ['Date', 'Category', 'Description', 'Amount (PKR)']
        ];
    }

    public function map($line): array
    {
        return [
            $line->date->format('Y-m-d'),
            $line->category->name,
            $line->description,
            round($line->amount, 2),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14], 'color' => ['argb' => 'FF0F6CBD']],
            8 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF0F6CBD']],
            ],
        ];
    }
}
