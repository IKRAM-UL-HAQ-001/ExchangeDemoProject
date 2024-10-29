<?php

namespace App\Exports;

use Auth;
use Carbon\Carbon;
use App\Models\Cash;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WithdrawalListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchangeId;

    public function __construct($exchangeId)
    {
        $this->exchangeId = $exchangeId;
    }

    public function query()
    {
        $currentMonth = Carbon::now()->month;
        $query = Cash::selectRaw('
            cashes.id, 
            exchanges.name AS name,
            users.name AS user_name,
            cashes.customer_name,
            cashes.cash_type,
            cashes.cash_amount,
            cashes.remarks,
            DATE_FORMAT(CONVERT_TZ(cashes.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") AS created_at,
            DATE_FORMAT(CONVERT_TZ(cashes.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") AS updated_at
        ')
        ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id') 
        ->join('users', 'cashes.user_id', '=', 'users.id') 
        ->whereMonth('cashes.created_at', $currentMonth) 
        ->where('cashes.cash_type', 'withdrawal');

        if (Auth::user()->role === "exchange") {
            return $query->where('cashes.exchange_id', $this->exchangeId);
        } 

        return $query; // Return the query for admin and assistant roles
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Customer Name',
            'Cash Type',
            'Cash Amount',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:I1')->getFont()->setSize(12); // Optional: set font size
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 20, // Exchange Name
            'C' => 20, // User Name
            'D' => 20, // Customer Name
            'E' => 15, // Cash Type
            'F' => 15, // Cash Amount
            'G' => 30, // Remarks
            'H' => 30, // Created At
            'I' => 30, // Updated At
        ];
    }
}
