<?php
namespace App\Exports;

use Auth;
use Carbon\Carbon;
use App\Models\Cash;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WithdrawalListExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchangeId;
    protected $startDate;
    protected $endDate;

    public function __construct($exchangeId, $startDate = null, $endDate = null)
    {
        $this->exchangeId = $exchangeId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        // Fetching the records
        $query = Cash::select('cashes.*', 'exchanges.name AS exchange_name', 'users.name AS user_name')
            ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id')
            ->join('users', 'cashes.user_id', '=', 'users.id')
            ->where('cashes.cash_type', 'withdrawal');

        // Filter by exchange ID for exchange users
        if (Auth::user()->role === "exchange") {
            $query->where('cashes.exchange_id', $this->exchangeId);
        }

        // Apply date filters if provided
        if ($this->startDate) {
            $query->whereDate('cashes.created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('cashes.created_at', '<=', $this->endDate);
        }

        // Get the results
        $records = $query->get();

        // If there are no records, return an empty collection
        if ($records->isEmpty()) {
            return collect(); // Return an empty collection
        }

        // Calculate total balance in PHP
        $totalBalance = 0;
        foreach ($records as $record) {
            $totalBalance += ($record->cash_type === 'deposit' ? $record->cash_amount : -$record->cash_amount);
            $record->total_balance = $totalBalance; // Assign total balance to each record
        }

        // Return records in the desired format
        return $records->map(function ($record) {
            return [
                'id' => $record->id,
                'exchange_name' => $record->exchange_name,
                'user_name' => $record->user_name,
                'customer_name' => $record->customer_name,
                'cash_type' => $record->cash_type,
                'cash_amount' => $record->cash_amount,
                'total_balance' => $record->total_balance,
                'remarks' => $record->remarks,
                'created_at' => $record->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $record->updated_at->format('Y-m-d H:i:s'),
            ];
        });
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
            'Total Balance',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:J1')->getFont()->setSize(12); // Optional: set font size
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
            'G' => 30, // Total Balance
            'H' => 30, // Remarks
            'I' => 30, // Created At
            'J' => 30, // Updated At
        ];
    }
}