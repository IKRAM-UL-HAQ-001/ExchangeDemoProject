<?php

namespace App\Exports;

use Auth;
use Carbon\Carbon;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Font;

class CustomerListExport  implements FromQuery,  WithHeadings, WithStyles, WithColumnWidths
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
        $currentYear = Carbon::now()->year;
        
        $query = Customer::selectRaw('
            customers.id, 
            exchanges.name as exchange_name,
            users.name as user_name,
            customers.reference_number,
            customers.name,
            customers.cash_amount,
            customers.remarks,
            DATE_FORMAT(CONVERT_TZ(customers.created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
            DATE_FORMAT(CONVERT_TZ(customers.updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
        ')
        ->join('exchanges', 'customers.exchange_id', '=', 'exchanges.id') 
        ->join('users', 'customers.user_id', '=', 'users.id') 
        ->whereMonth('customers.created_at', $currentMonth)
        ->whereYear('customers.created_at', $currentYear)
        ->distinct();
       
        if (Auth::user()->role == "exchange") {
            return $query->where('customers.exchange_id', $this->exchnageId); // No ->get() here, return the query builder
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
            'A' => 10, 
            'B' => 20, 
            'C' => 15, 
            'D' => 20, 
            'E' => 20, 
            'F' => 20, 
            'G' => 20, 
            'H' => 30,
            'I' => 30,

        ];
    }
}

