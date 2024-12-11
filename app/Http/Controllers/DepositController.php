<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Cash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
Use App\Exports\DepositListExport;
use Carbon\Carbon;
use Auth;
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
            ->get();

        return view('exchange.deposit.list', compact('depositRecords'))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }


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
    public function show(Deposit $deposit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Deposit $deposit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Deposit $deposit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Deposit $deposit)
    {
        //
    }
}
