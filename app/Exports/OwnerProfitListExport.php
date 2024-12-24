<?php

namespace App\Exports;

use App\Models\OwnerProfit;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Auth;

class OwnerProfitListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchangeId;

    public function __construct($exchangeId)
    {
        $this->exchangeId = $exchangeId;
    }

    public function query()
    {
        $currentYear = Carbon::now()->year; 

        $query = OwnerProfit::selectRaw('
                owner_profits.id, 
                exchanges.name AS name,
                users.name AS user_name,
                owner_profits.cash_amount,
                owner_profits.remarks,
                DATE_FORMAT(CONVERT_TZ(owner_profits.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
                DATE_FORMAT(CONVERT_TZ(owner_profits.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
            ')
            ->join('exchanges', 'owner_profits.exchange_id', '=', 'exchanges.id') // Join with shops
            ->join('users', 'owner_profits.user_id', '=', 'users.id') // Join with users based on user_id
            ->whereYear('owner_profits.created_at', $currentYear)
            ->distinct(); // Ensure unique results

        // Check if the result is empty before executing the query
        if ($query->count() === 0) {
            // Return an empty collection if no records found
            return collect(); // This will generate an empty Excel file
        }

        switch (Auth::user()->role) {
            case "exchange":
                return $query->where('owner_profits.exchange_id', $this->exchangeId);
            case "admin":
            case "assistant":
                return $query;
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Cash Amount',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:G1')->getFont()->setSize(12); // Optional: set font size
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 20, // Exchange Name
            'C' => 15, // User Name
            'D' => 20, // Cash Amount
            'E' => 20, // Remarks
            'F' => 30, // Created At
            'G' => 30, // Updated At
        ];
    }
}
