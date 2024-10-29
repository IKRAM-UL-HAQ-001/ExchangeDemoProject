<?php

namespace App\Http\Controllers;

use App\Models\DepositWithdrawal;
Use App\Exports\WithdrawalListExport;
Use App\Exports\DepositListExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;

class DepositWithdrawalController extends Controller
{
    public function withdrawalExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            if(Auth::user()->role == "admin" || Auth::user()->role == "assistant"){
                $exchangeId = null;
            }
            else{
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new WithdrawalListExport($exchangeId), 'withdrawalRecord.xlsx');
        }
    }
    
    public function depositExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            if(Auth::user()->role == "admin" || Auth::user()->role == "assistant"){
                $exchangeId = null;
            }
            else{
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new DepositListExport($exchangeId), 'depositRecord.xlsx');
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
            ->get();
            return view('admin.deposit_withdrawal.list',compact('depositWithdrawalRecords'));
        }
    }
    
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
            ->get();
            return view('assistant.deposit_withdrawal.list',compact('depositWithdrawalRecords'));
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
    public function show(DepositWithdrawal $depositWithdrawal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DepositWithdrawal $depositWithdrawal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DepositWithdrawal $depositWithdrawal)
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
            $depositWithdarwal = Cash::find($request->id);
            if ($depositWithdarwal) {
                $depositWithdarwal->delete();
                return response()->json(['success' => true, 'message' => 'Deposit/Withdarwal deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Deposit/Withdarwal not found.'], 404);
        }
    }
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $exchangeId = auth()->user()->exchange_id;
        $userId = auth()->user()->id;
        $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
            ->where('exchange_id', $exchangeId) 
            ->where('user_id', $userId) 
            ->whereIn('cash_type', ['deposit', 'withdrawal']) 
            ->get();
        return view('exchange.deposit_withdrawal.list', compact('depositWithdrawalRecords'));
    }

}
