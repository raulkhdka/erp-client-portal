<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithStyles
{
    protected $clients;

    public function __construct($clients)
    {
        $this->clients = $clients;
    }

    public function collection()
    {
        return $this->clients;
    }

    public function headings(): array
    {
        return [
            'SN',
            'Client Name',
            'Company Name',
            'Employee Name',
            'Email',
            'Phone',
            'Status',
        ];
    }

    public function map($client): array
    {
        static $index = 0;
        $index++;
        return [
            $index,
            $client->name,
            $client->company_name ?? 'Not specified',
            $client->assignedEmployees->first()->name ?? 'Unassigned',
            $client->user->email ?? 'N/A',
            $client->phones->first()->phone ?? 'No phone',
            ucfirst($client->status),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // SN (narrow, as it contains small numbers)
            'B' => 25,  // Client Name (wider for full names)
            'C' => 25,  // Company Name (wider for company names)
            'D' => 20,  // Employee Name (moderate width)
            'E' => 30,  // Email (wider for longer email addresses)
            'F' => 15,  // Phone (narrow, as phone numbers are short)
            'G' => 15,  // Status (narrow, as status is short text)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Calculate the total number of rows (1 for header + number of clients)
        $totalRows = $this->clients->count() + 1;

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
                        'color' => ['argb' => 'FF000000'], // Black Border
                    ],
                ],
            ],
        ];
    }
}
