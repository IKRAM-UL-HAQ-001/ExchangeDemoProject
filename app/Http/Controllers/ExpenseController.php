<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Cash;
Use App\Exports\ExpenseListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

class ExpenseController extends Controller
{
    public function expenseExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new ExpenseListExport($exchangeId), 'expenseRecord.xlsx');
        }
    }
    
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $expenseRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->orderBy('created_at', 'desc')
                ->get();
    
            return response()
                ->view('admin.expense.list', compact('expenseRecords'))
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }
    
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $expenseRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->get();
    
            return response()
                ->view('assistant.expense.list', compact('expenseRecords'))
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $expense = Cash::find($request->id);
            if ($expense) {
                $expense->delete();
                return response()->json(['success' => true, 'message' => 'Expense deleted successfully!'])
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                    ]);
            }
            return response()->json(['success' => false, 'message' => 'Expense not found.'], 404)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }
    
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeId = auth()->user()->exchange_id; 
            $userId = auth()->user()->id;
    
            $expenseRecords = Cash::with(['exchange', 'user'])
                ->where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->where('cash_type', 'expense')
                ->get();
    
            return response()
                ->view('exchange.expense.list', compact('expenseRecords'))
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }    
}
