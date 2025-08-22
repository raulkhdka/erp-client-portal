<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $services;

    public function __construct($services)
    {
        $this->services = $services;
    }

    public function collection()
    {
        return $this->services;
    }

    public function headings(): array
    {
        return [
            'SN',
            'Name',
            'Service Type',
            'Clients',
            'Status',
            'Created',
        ];
    }

    public function map($service): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $service->name,
            $service->type,
            $service->clients_count . ' clients',
            $service->is_active ? 'Active' : 'Inactive',
            $service->created_at->format('M d, Y'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // SN (narrow, as it contains small numbers)
            'B' => 25,  // Name (wider for service names)
            'C' => 20,  // Service Type (moderate width)
            'D' => 15,  // Clients (narrow, as it contains counts)
            'E' => 15,  // Status (narrow, as status is short text)
            'F' => 15,  // Created (narrow, as dates are short)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $totalRows = $this->services->count() + 1;

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
            'A2:F' . $totalRows => [
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
