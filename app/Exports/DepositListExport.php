<?php
namespace App\Exports;

use App\Models\Cash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepositListExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $exchangeId;
    protected $startDate;
    protected $endDate;

    public function __construct($exchangeId, $startDate = null, $endDate = null)
    {
        $this->exchangeId = (int)$exchangeId; // Ensure it's an integer
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Query the data to be exported.
     */
    public function query()
    {
        // Building the query
        $query = Cash::query()
            ->select('cashes.*', 'exchanges.name AS exchange_name', 'users.name AS user_name')
            ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id')
            ->join('users', 'cashes.user_id', '=', 'users.id')
            ->where('cashes.cash_type', 'deposit');

        // Restrict by exchange ID for exchange users
        if (Auth::user()->role === 'exchange') {
            $query->where('cashes.exchange_id', $this->exchangeId);
        }

        // Apply the date range filter if provided
        if ($this->startDate) {
            $query->whereDate('cashes.created_at', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('cashes.created_at', '<=', $this->endDate);
        }

        return $query;
    }

    /**
     * Map each record to the desired format.
     */
    public function map($record): array
    {
        // Calculate the total balance dynamically if needed
        static $totalBalance = 0;
        $totalBalance += $record->cash_amount;

        return [
            $record->id,
            $record->exchange_name,
            $record->user_name,
            $record->reference_number,
            $record->customer_name,
            $record->cash_type,
            $record->cash_amount,
            $totalBalance,
            $record->bonus_amount,
            $record->payment_type,
            $record->remarks,
            $record->created_at->format('Y-m-d H:i:s'),
            $record->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Define the headings for the export.
     */
    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Reference Number',
            'Customer Name',
            'Cash Type',
            'Cash Amount',
            'Total Balance',
            'Bonus Amount',
            'Payment Type',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }
}
