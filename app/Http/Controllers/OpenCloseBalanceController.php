<?php

namespace App\Http\Controllers;

use App\Models\OpenCloseBalance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpenCloseBalanceListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OpenCloseBalanceController extends Controller
{

    public function openCloseBalanceExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new OpenCloseBalanceListExport($exchangeId), 'openingClosingBalanceRecord.xlsx');
        }
    }
    
    public function index()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)
        ->orderBy('created_at', 'desc')
        ->paginate(20);
        return view('admin.open_close_balance.list', compact('openingClosingBalanceRecords'));
    }
    
    public function exchangeIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $exchangeId = Auth::user()->exchange_id;
        $openingClosingBalanceRecords = OpenCloseBalance::where('exchange_id', $exchangeId)
            ->where('created_at', '>=', $startOfWeek)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    
        return view('exchange.open_close_balance.list', compact("openingClosingBalanceRecords"));
    }
    
    public function assistantIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)
        ->orderBy('created_at', 'desc')
        ->paginate(20);
    
        return view('assistant.open_close_balance.list', compact("openingClosingBalanceRecords"));
    }
    
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else {
            $user = Auth::user();
            $exchangeId = $user->exchange_id;  
            $userId = $user->id;
            $validatedData = $request->validate([
                'open_balance' => 'required',
                'remarks' => 'nullable|string|max:255',
            ]);
    
            try {
                OpenCloseBalance::create([
                    'open_balance' => $validatedData['open_balance'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);
    
                return redirect()->back();
            } catch (\Exception $e) {
                return redirect()->back();
            }
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $openCloseBalance = OpenCloseBalance::find($request->id);
            if ($openCloseBalance) {
                $openCloseBalance->delete();
                return response()->json(['success' => true, 'message' => ' deleted successfully!'], 200);
            }
            return response()->json(['error' => true, 'message' => 'Not found.'], 404);
        }
    }    
}
