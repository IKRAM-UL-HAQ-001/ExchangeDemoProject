<?php

namespace App\Http\Controllers;

use App\Models\BankBalance;
use App\Models\BankEntry;
Use App\Exports\BankBalanceListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 
class BankBalanceController extends Controller
{
    
    public function bankBalanceListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new BankBalanceListExport($exchangeId), 'bankBalanceRecord.xlsx');
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);
            
            return response()
                ->view("admin.bank_balance.list", compact('bankBalanceRecords'));
        }
    }

    public function assistantIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);
            
            return response()
                ->view("assistant.bank_balance.list", compact('bankBalanceRecords'));
        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $bankBalance = BankEntry::find($request->id);
            if ($bankBalance) {
                $bankBalance->delete();
                return response()->json(['success' => true, 'message' => 'Bank Balance deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Bank Balance not found.'], 404);
        }
    }
}
