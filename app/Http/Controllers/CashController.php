<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;
class CashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $user = Auth::user();
            $exchangeId = $user->exchange_id;
            $cashRecords = Cash::where('exchange_id', $exchangeId)->get();
            return view('exchange.cash.list',compact('cashRecords'));
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
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
    
        $validatedData = $request->validate([
            'reference_number' => 'nullable|string|max:255|unique:cashes,reference_number',
            'customer_name' => 'nullable|string|max:255|required_if:cash_type,deposit',
            'cash_amount' => 'required|numeric',
            'cash_type' => 'required|in:deposit,withdrawal,expense',
            'bonus_amount' => 'nullable|numeric|required_if:cash_type,deposit',
            'payment_type' => 'nullable|string|required_if:cash_type,deposit',
            'remarks' => 'nullable|string|max:255|required_if:cash_type,deposit,|required_if:cash_type,withdraw',
        ]);
    
        try {
            $user = Auth::user();
            Cash::create([
                'reference_number' => $validatedData['reference_number'] ?? null,
                'customer_name' => $validatedData['customer_name'] ?? null,
                'cash_amount' => $validatedData['cash_amount'],
                'cash_type' => $validatedData['cash_type'],
                'bonus_amount' => $validatedData['bonus_amount'] ?? null,
                'payment_type' => $validatedData['payment_type'] ?? null,
                'remarks' => $validatedData['remarks'] ?? null,
                'user_id' => $user->id,
                'exchange_id' => $user->exchange_id,
            ]);
    
            return response()->json(['success' => true, 'message' => 'Transaction successfully added!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function show(Cash $cash)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cash $cash)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cash $cash)
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
            $cash = Cash::find($request->id);
            if ($cash) {
                $cash->delete();
                return response()->json(['success' => true, 'message' => 'Cash deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Cank not found.'], 404);
        }
    }
}
