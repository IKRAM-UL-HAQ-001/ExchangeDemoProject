<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Cash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
Use App\Exports\WithdrawalListExport;
use Carbon\Carbon;
use Auth;
class WithdrawalController extends Controller
{

    public function withdrawalExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            return Excel::download(new WithdrawalListExport($exchangeId, $startDate, $endDate), 'withdrawalRecord.xlsx');
        }
    }
    
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
        
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $exchangeId = auth()->user()->exchange_id;
        $userId = auth()->user()->id;
        $withdrawalRecords = Cash::with(['exchange', 'user'])
            ->where('exchange_id', $exchangeId) 
            ->where('user_id', $userId) 
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();
            
        return response()->view('exchange.withdrawal.list', compact('withdrawalRecords'))->withHeaders([
            'X-Frame-Options' => 'DENY', // Prevents framing
            // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
        ]);
    }    
}
