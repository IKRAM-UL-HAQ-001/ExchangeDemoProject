<?php

namespace App\Exports;

use App\Models\MasterSettling;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Auth;

class MasterSettlingMonthlyListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
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

        $query = MasterSettling::selectRaw('
                master_settlings.id, 
                exchanges.name AS exchange_name,
                users.name AS user_name,
                master_settlings.white_label,
                master_settlings.credit_reff,
                master_settlings.settling_point,
                master_settlings.price,
                master_settlings.settling_point * master_settlings.price AS total_amount,
                DATE_FORMAT(CONVERT_TZ(master_settlings.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") AS created_at,
                DATE_FORMAT(CONVERT_TZ(master_settlings.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") AS updated_at
            ')
            ->join('exchanges', 'master_settlings.exchange_id', '=', 'exchanges.id')
            ->join('users', 'master_settlings.user_id', '=', 'users.id')
            ->whereMonth('master_settlings.created_at', $currentMonth)
            ->whereYear('master_settlings.created_at', $currentYear)
            ->distinct();

        // Check if the query returns any results
        if ($query->count() === 0) {
            return collect(); // Return an empty collection if no records found
        }

        switch (Auth::user()->role) {
            case "exchange":
                return $query->where('master_settlings.exchange_id', $this->exchangeId);
            case "admin":
            case "assistant":
                return $query; // Admin and assistant can see all
            default:
                return collect(); // Return an empty collection for unrecognized roles
        }
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'White Label',
            'Credit Ref',
            'Settling Point',
            'Price',
            'Total Amount',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getFont()->setSize(12);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, // ID
            'B' => 20, // Exchange Name
            'C' => 20, // User Name
            'D' => 20, // White Label
            'E' => 15, // Credit Ref
            'F' => 20, // Settling Point
            'G' => 15, // Price
            'H' => 30, // Total Amount
            'I' => 30, // Created At
            'J' => 30, // Updated At
        ];
    }
}
