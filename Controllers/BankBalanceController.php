<?php

namespace App\Http\Controllers;

use App\Models\BankBalance;
use App\Models\BankEntry;
use App\Exports\BankBalanceListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class BankBalanceController extends Controller
{

    public function bankBalanceListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $exchangeId = Auth::user()->role === "admin" || Auth::user()->role === "assistant"
            ? null
            : Auth::user()->exchange_id;

        return Excel::download(new BankBalanceListExport($exchangeId), 'bankBalanceRecord.xlsx');
    }

    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
    }

    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $bankBalance = BankEntry::find($request->id);
        if ($bankBalance) {
            $bankBalance->delete();
            return response()->json(['success' => true, 'message' => 'Bank Balance deleted successfully!'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Bank Balance not found.'], 404);
    }
}
