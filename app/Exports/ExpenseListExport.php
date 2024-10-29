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

class ExpenseListExport implements FromQuery,  WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    protected $exchangeId;

    public function __construct($exchangeId){
        $this->exchangeId = $exchangeId;
    }

    public function query(){
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $query = Cash::selectRaw('
                cashes.id, 
                exchanges.name as name,
                users.name as user_name,
                cashes.cash_type,
                cashes.cash_amount,
                cashes.remarks,
                DATE_FORMAT(CONVERT_TZ(cashes.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
                DATE_FORMAT(CONVERT_TZ(cashes.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
            ')
            ->join('exchanges', 'cashes.exchange_id', '=', 'exchanges.id') 
            ->join('users', 'cashes.user_id', '=', 'users.id') 
            ->whereMonth('cashes.created_at', $currentMonth) 
            ->whereYear('cashes.created_at', $currentYear) 
            ->where('cashes.cash_type', 'expense');

        if (Auth::user()->role == "exchange") {
            return $query->where('cashes.exchange_id', $this->exchangeId);
        }elseif (Auth::user()->role == "admin") {
            return $query;
        }
    }

    
    public function headings(): array
    {
        return [
            'ID',
            'Exchange Name',
            'User Name',
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
