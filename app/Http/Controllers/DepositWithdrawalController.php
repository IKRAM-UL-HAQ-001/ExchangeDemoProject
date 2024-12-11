<?php

namespace App\Http\Controllers;

use App\Models\DepositWithdrawal;
Use App\Exports\WithdrawalListExport;
Use App\Exports\DepositListExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;

class DepositWithdrawalController extends Controller
{
    
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.deposit_withdrawal.list', compact('depositWithdrawalRecords'))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }

    public function assistantIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->get();

        return view('assistant.deposit_withdrawal.list', compact('depositWithdrawalRecords'))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }


    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $depositWithdarwal = Cash::find($request->id);
        if ($depositWithdarwal) {
            $depositWithdarwal->delete();
            return response()->json(['success' => true, 'message' => 'Deposit/Withdrawal deleted successfully!'])
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }

        return response()->json(['success' => false, 'message' => 'Deposit/Withdrawal not found.'], 404)
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }

}
