<?php

namespace App\Exports;

use App\Models\BankEntry;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Auth;

class BankBalanceListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
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
        $currentYear = Carbon::now()->year;

        $query = BankEntry::selectRaw('
                bank_entries.id, 
                exchanges.name AS exchange_name,
                users.name AS user_name,
                bank_entries.bank_name,
                bank_entries.account_number,
                bank_entries.cash_type,
                bank_entries.cash_amount,
                bank_entries.remarks,
                DATE_FORMAT(CONVERT_TZ(bank_entries.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
                DATE_FORMAT(CONVERT_TZ(bank_entries.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
            ')
            ->join('exchanges', 'bank_entries.exchange_id', '=', 'exchanges.id')
            ->join('users', 'bank_entries.user_id', '=', 'users.id')
            ->whereMonth('bank_entries.created_at', $currentMonth)
            ->whereYear('bank_entries.created_at', $currentYear);
        
        // Check if the result is empty before executing the query
        if ($query->count() === 0) {
            // Return an empty collection if no records found
            return collect(); // This will generate an empty Excel file
        }

        if (Auth::user()->role === 'exchange') {
            $query->where('bank_entries.exchange_id', $this->exchangeId);
        }

        return $query->distinct(); // Ensure unique results for all user roles
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Bank Name',
            'Account Number',
            'Cash Type',
            'Cash Amount',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setSize(12); // Bold and set font size for header row
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 20,  // Exchange Name
            'C' => 15,  // User Name
            'D' => 15,  // Bank Name
            'E' => 15,  // Account Number
            'F' => 15,  // Cash Type
            'G' => 15,  // Cash Amount
            'H' => 30,  // Remarks
            'I' => 30,  // Created At
            'J' => 30,  // Updated At
        ];
    }
}
