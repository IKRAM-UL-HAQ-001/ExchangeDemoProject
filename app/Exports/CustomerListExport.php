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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
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

        // Check if the result is empty before executing the query
        if ($query->count() === 0) {
            // Return an empty collection if no records found
            return collect(); // This will generate an empty Excel file
        }  

        if (Auth::user()->role == "exchange") {
            return $query->where('customers.exchange_id', $this->exchangeId);
        } elseif (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
            return $query; // Admin and assistant can see all
        }

        return collect(); // Return an empty collection for unrecognized roles
    }

    public function headings(): array
    {
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
