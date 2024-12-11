<?php

namespace App\Exports;

use Auth;
use Carbon\Carbon;
use DB;
use App\Models\Cash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ExpenseListExport implements FromCollection,  WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchangeId;

    public function __construct($exchangeId){
        $this->exchangeId = $exchangeId;
    }

    public function collection()
    {
        $currentMonth = Carbon::now()->month;

        // Fetching the records
        $records = Cash::select('cashes.*', 'exchanges.name AS exchange_name', 'users.name AS user_name')
            ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id')
            ->join('users', 'cashes.user_id', '=', 'users.id')
            ->whereMonth('cashes.created_at', $currentMonth)
            ->where('cashes.cash_type', 'expense');

        if (Auth::user()->role === "exchange") {
            $records->where('cashes.exchange_id', $this->exchangeId);
        }

        // Getting the results
        $records = $records->get();

        // If there are no records for the month, return an empty collection
        if ($records->isEmpty()) {
            return collect(); // Return an empty collection
        }

        // Calculating total balance in PHP
        $totalBalance = 0;
        foreach ($records as $record) {
            $totalBalance += ($record->cash_type === 'deposit' ? $record->cash_amount : -$record->cash_amount);
            $record->total_balance = $totalBalance; // Assign total balance to each record
        }

        // Filtering for withdrawals
        $withdrawals = $records->filter(function ($record) {
            return $record->cash_type === 'expense';
        });

        // Return only non-empty records and arrange columns in the desired order
        return $withdrawals->map(function ($record) {
            return [
                'id' => $record->id,
                'exchange_name' => $record->exchange_name,
                'user_name' => $record->user_name,
                'cash_type' => $record->cash_type,
                'cash_amount' => $record->cash_amount,
                'remarks' => $record->remarks,
                'created_at' => $record->created_at,
                'updated_at' => $record->updated_at,
            ];
        });
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Cash Type',
            'Cash Amount',
            'Total Balance',
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
            'B' => 20, 
            'C' => 15, // Cash Type
            'D' => 15, // Cash Amount
            'E' => 20,
            'F' => 25, // Remarks
            'G' => 30, // created_at
            'H' => 30,
            'I' => 30, // updated_At
        ];
    }
}
