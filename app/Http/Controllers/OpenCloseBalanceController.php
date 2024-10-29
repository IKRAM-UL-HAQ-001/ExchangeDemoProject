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
            return redirect()->route('auth.login');
        }
        else{
            if(Auth::user()->role == "admin" || Auth::user()->role == "assistant"){
                $exchangeId = null;
            }
            else{
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new OpenCloseBalanceListExport($exchangeId), 'openingClosingBalanceRecord.xlsx');
        }
    }

    public function index()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)
        ->get();
        return view ('admin.open_close_balance.list', compact('openingClosingBalanceRecords'));
    }

    public function exchangeIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $exchangeId = Auth::user()->exchange_id;
        $openingClosingBalanceRecords = OpenCloseBalance::where('exchange_id', $exchangeId)
        ->where('created_at', '>=', $startOfWeek)
        ->get();
        return view('exchange.open_close_balance.list',compact("openingClosingBalanceRecords"));
    }

    public function assistantIndex()
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)
        ->get();
        return view('assistant.open_close_balance.list',compact("openingClosingBalanceRecords"));
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
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else {
            $user = Auth::user();
            $exchangeId = $user->exchange_id;  
            $userId = $user->id;
            $validatedData = $request->validate([
                'open_balance' => 'required|numeric|min:0',
                'close_balance' => 'required|numeric|min:0',
                'remarks' => 'nullable|string|max:255',
            ]);

            try {
                OpenCloseBalance::create([
                    'open_balance' => $validatedData['open_balance'],
                    'close_balance' => $validatedData['close_balance'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);

                return response()->json(['success' => true, 'message' => 'Opening Closing Balance saved successfully!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OpenCloseBalance $openCloseBalance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OpenCloseBalance $openCloseBalance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OpenCloseBalance $openCloseBalance)
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
            $openCloseBalance = OpenCloseBalance::find($request->id);
            if ($openCloseBalance) {
                $openCloseBalance->delete();
                return response()->json(['success' => true, 'message' => 'opening closing balance deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'opening closing balance not found.'], 404);
        }
    }
}
