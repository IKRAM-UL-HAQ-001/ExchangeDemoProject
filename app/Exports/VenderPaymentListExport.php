<?php

namespace App\Exports;

use App\Models\VenderPayment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class VenderPaymentListExport implements FromQuery, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function query()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        return VenderPayment::selectRaw('
            vender_payments..id, 
            vender_payments..paid_amount,
            vender_payments..remaining_amount,
            vender_payments..payment_type,
            vender_payments..remarks,
            DATE_FORMAT(CONVERT_TZ(vender_payments..created_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as created_at,
            DATE_FORMAT(CONVERT_TZ(vender_payments..updated_at, "+00:00", "+05:30"), "%Y-%m-%d %H:%i:%s") as updated_at
        ')
        ->whereMonth('vender_payments..created_at', $currentMonth)
        ->whereYear('vender_payments..created_at', $currentYear);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Paid Amount',
            'Remaining Amount',
            'Payment Type',
            'Remarks',
            'Created At',
            'Updated At',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFont()->setSize(12);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 30,
            'F' => 30,
            'G' => 30,
        ];
    }
}
