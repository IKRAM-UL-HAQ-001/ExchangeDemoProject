<?php

namespace App\Exports;

use App\Models\OpenCloseBalance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;
use Auth;

class OpenCloseBalanceListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
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

        $query = OpenCloseBalance::selectRaw('
            open_close_balances.id, 
            exchanges.name AS name,
            users.name AS user_name,
            open_close_balances.open_balance,
            open_close_balances.remarks,
            DATE_FORMAT(CONVERT_TZ(open_close_balances.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
            DATE_FORMAT(CONVERT_TZ(open_close_balances.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
        ')
        ->join('exchanges', 'open_close_balances.exchange_id', '=', 'exchanges.id')
        ->join('users', 'open_close_balances.user_id', '=', 'users.id')
        ->whereYear('open_close_balances.created_at', $currentYear);

        switch (Auth::user()->role) {
            case "exchange":
                $query->where('open_close_balances.exchange_id', $this->exchangeId);
                break;
            case "admin":
            case "assistant":
                // No additional filters for admin or assistant roles
                break;
        }

        return $query; // Always return the query object
    }

    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Open Balance',
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
            'D' => 20, // Open Balance
            'E' => 20, // Close Balance
            'F' => 30, // Remarks
            'G' => 30, // Created At
        ];
    }
}
