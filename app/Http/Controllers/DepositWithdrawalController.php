<?php

namespace App\Http\Controllers;

use App\Models\DepositWithdrawal;
Use App\Exports\WithdrawalListExport;
Use App\Exports\DepositListExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            ->paginate(20);

        return view('admin.deposit_withdrawal.list', compact('depositWithdrawalRecords'));
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
            ->paginate(20);

        return view('assistant.deposit_withdrawal.list', compact('depositWithdrawalRecords'));
    }


    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $depositWithdarwal = Cash::find($request->id);
        if ($depositWithdarwal) {
            $depositWithdarwal->delete();
            return response()->json(['success' => true, 'message' => 'Deposit/Withdrawal deleted successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Deposit/Withdrawal not found.'], 404);
    }

}
