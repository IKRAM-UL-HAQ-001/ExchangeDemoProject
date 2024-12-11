<?php

namespace App\Http\Controllers;

use App\Models\BankBalance;
use App\Models\BankEntry;
Use App\Exports\BankBalanceListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth; 
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
                ->get();
            
            return response()
                ->view("admin.bank_balance.list", compact('bankBalanceRecords'))
                // ->header('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;")
                ->header('X-Frame-Options', 'DENY'); // Prevent framing of the page
        }
    }

    public function indexAssistant()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->get();
            
            return response()
                ->view("assistant.bank_balance.list", compact('bankBalanceRecords'))
                // ->header('Content-Security-Policy', "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;")
                ->header('X-Frame-Options', 'DENY'); // Prevent framing of the page
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(BankBalance $bankBalance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankBalance $bankBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankBalance $bankBalance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $bankBalance = BankEntry::find($request->id);
            if ($bankBalance) {
                $bankBalance->delete();
                return response()->json(['success' => true, 'message' => 'Bank Balance deleted successfully!'])
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Bank Balance not found.'], 404)
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
    }
}
