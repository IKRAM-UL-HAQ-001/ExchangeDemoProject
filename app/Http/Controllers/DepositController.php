<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Cash;
use App\Models\Bank;
use App\Models\ExcelFile;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
Use App\Exports\DepositListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
class DepositController extends Controller
{
    
    public function depositExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $exchangeId = (Auth::user()->role == "admin" || Auth::user()->role == "assistant") ? null : Auth::user()->exchange_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Excel::download(new DepositListExport($exchangeId, $startDate, $endDate), 'depositRecord.xlsx');
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $exchangeId = auth()->user()->exchange_id;
        $userId = auth()->user()->id;

        $depositRecords = Cash::with(['exchange', 'user'])
            ->where('exchange_id', $exchangeId)
            ->where('user_id', $userId)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->paginate(20);
        $bankRecords= Bank::all();
        $excelData = ExcelFile::all();

        return view('exchange.deposit.list', compact('depositRecords','excelData','bankRecords'));
    }

}
