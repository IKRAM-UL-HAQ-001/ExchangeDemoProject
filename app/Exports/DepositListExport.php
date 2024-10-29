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
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class DepositListExport implements FromQuery,  WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchnageId;

    public function __construct($exchnageId)
    {
        $this->exchnageId = $exchnageId;
    }
    public function query()
    {
        $currentMonth = Carbon::now()->month;
        $query = Cash::selectRaw('
            cashes.id, 
            exchanges.name as name,
            users.name as user_name,
            cashes.reference_number,
            cashes.customer_name,
            cashes.cash_type,
            cashes.cash_amount,
            cashes.bonus_amount,
            cashes.payment_type,
            cashes.remarks,
            DATE_FORMAT(CONVERT_TZ(cashes.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
            DATE_FORMAT(CONVERT_TZ(cashes.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
        ')
        ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id') 
        ->join('users', 'cashes.user_id', '=', 'users.id') 
        ->whereMonth('cashes.created_at', $currentMonth) 
        ->where('cashes.cash_type', 'deposit');
       
        if (Auth::user()->role == "exchange") {
            return $query->where('cashes.exchange_id', $this->exchnageId); // No ->get() here, return the query builder
        } elseif (Auth::user()->role == "admin") {
            return $query;
        }elseif(Auth::user()->role == "assistant"){
            return $query;
        }
    }

    public function headings(): array{
        return [
            'ID',
            'Exchange Name',
            'User Name',
            'Reference Number',
            'Customer Name',
            'Cash Type',
            'Cash Amount',
            'Bonus Amount',
            'Payment Type',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:L1')->getFont()->setBold(true); // Bold the header row
        $sheet->getStyle('A1:L1')->getFont()->setSize(12); // Optional: set font size
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10, 
            'B' => 20, 
            'C' => 15, 
            'D' => 20, 
            'E' => 20, 
            'F' => 20, 
            'G' => 20, 
            'H' => 20,
            'I' => 15,
            'J' => 20,
            'K' => 30,
            'L' => 30,
        ];
    }
}
