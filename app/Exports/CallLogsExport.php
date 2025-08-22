<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CallLogsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $callLogs;

    public function __construct($callLogs)
    {
        $this->callLogs = $callLogs;
    }

    public function collection()
    {
        return $this->callLogs;
    }

    public function headings(): array
    {
        return [
            'SN',
            'Date/Time',
            'Subject',
            'Caller',
            'Type',
            'Priority',
            'Status',
            'Employee',
            'Company',
        ];
    }

    public function map($callLog): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $callLog->call_date->format('M d, Y h:i A'),
            $callLog->subject,
            ($callLog->caller_name ?? '') . ($callLog->caller_phone ? ' (' . $callLog->caller_phone . ')' : ''),
            ucfirst($callLog->call_type),
            ucfirst($callLog->priority),
            $callLog->status_label,
            $callLog->employee->name,
            $callLog->client->company_name,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // SN
            'B' => 20,  // Date/Time
            'C' => 25,  // Subject
            'D' => 20,  // Caller
            'E' => 15,  // Type
            'F' => 15,  // Priority
            'G' => 15,  // Status
            'H' => 20,  // Employee
            'I' => 20,  // Company
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->callLogs->count() + 1;

        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD3D3D3',
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
            'A2:I' . $totalRows => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ],
        ];
    }
}