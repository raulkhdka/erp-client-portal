<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        return [
            'SN',
            'Name',
            'Email',
            'Department',
            'Position',
            'Hire Date',
            'Status',
        ];
    }

    public function map($employee): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $employee->name,
            $employee->user->email ?? 'N/A',
            $employee->department ?? 'Not specified',
            $employee->position,
            $employee->hire_date_formatted ?? 'N/A',
            ucfirst($employee->status),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // SN (narrow, as it contains small numbers)
            'B' => 25,  // Name (wider for full names)
            'C' => 30,  // Email (wider for longer email addresses)
            'D' => 20,  // Department (moderate width)
            'E' => 20,  // Position (moderate width)
            'F' => 15,  // Hire Date (narrow, as dates are short)
            'G' => 15,  // Status (narrow, as status is short text)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate the total number of rows (1 for header + number of employees)
        $totalRows = $this->employees->count() + 1;

        return [
            // Style the header row (row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12, // Set font size for headers
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFD3D3D3', // Light grey background (ARGB format)
                    ],
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Slightly thicker border for headers
                        'color' => ['argb' => 'FF000000'], // Black border
                    ],
                ],
            ],
            // Center all data cells (rows 2 to end)
            'A2:G' . $totalRows => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'], //Black Border
                    ],
                ],
            ],
        ];
    }
}
