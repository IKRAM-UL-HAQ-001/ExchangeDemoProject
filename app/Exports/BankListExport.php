<?php

namespace App\Exports;

use App\Models\Bank; // Adjust this if your bank model is different
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BankListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function query()
    {
        $query = Bank::query(); // Adjust based on how you want to filter or retrieve banks
        
        // Check if the query returns any results
        if ($query->count() === 0) {
            return collect(); // Return an empty collection if no records found
        }
        
        return $query; // Return the query if there are records
    }

    public function headings(): array
    {
        return [
            'ID',
            'Bank Name',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:D1')->getFont()->setSize(12); // Optional: set font size
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 30,  // Bank Name
            'C' => 30,  // Created At
            'D' => 30,  // Updated At
        ];
    }
}
