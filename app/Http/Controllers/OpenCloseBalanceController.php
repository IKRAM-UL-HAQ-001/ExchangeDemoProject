<?php

namespace App\Http\Controllers;

use App\Models\OpenCloseBalance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpenCloseBalanceListExport;
use Carbon\Carbon;
use Auth;

class OpenCloseBalanceController extends Controller
{

    public function openCloseBalanceExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
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
        ->orderBy('created_at', 'desc')->get();
        return response()
            ->view('admin.open_close_balance.list', compact('openingClosingBalanceRecords'));
    }
    
    public function exchangeIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $exchangeId = Auth::user()->exchange_id;
        $openingClosingBalanceRecords = OpenCloseBalance::where('exchange_id', $exchangeId)
            ->where('created_at', '>=', $startOfWeek)
            ->orderBy('created_at', 'desc')
            ->get();
    
        return response()
            ->view('exchange.open_close_balance.list', compact("openingClosingBalanceRecords"))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }
    
    public function assistantIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)->get();
    
        return response()
            ->view('assistant.open_close_balance.list', compact("openingClosingBalanceRecords"))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }
    
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
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
    
                return response()->json(['success' => true, 'message' => 'Opening Closing Balance saved successfully!'])
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                    ]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500)
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                    ]);
            }
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            $openCloseBalance = OpenCloseBalance::find($request->id);
            if ($openCloseBalance) {
                $openCloseBalance->delete();
                return response()->json(['success' => true, 'message' => 'Opening closing balance deleted successfully!'])
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                    ]);
            }
            return response()->json(['success' => false, 'message' => 'Opening closing balance not found.'], 404)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }    
}
