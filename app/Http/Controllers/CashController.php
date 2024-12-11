<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;

class CashController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();
        $exchangeId = $user->exchange_id;
        $cashRecords = Cash::where('exchange_id', $exchangeId)->get();

        return view('exchange.cash.list', compact('cashRecords'))
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
        $validatedData = $request->validate([
            'reference_number' => 'nullable|string|max:255|unique:cashes,reference_number',
            'customer_name' => 'nullable|string|max:255|required_if:cash_type,deposit',
            'cash_amount' => 'required|numeric',
            'cash_type' => 'required|in:deposit,withdrawal,expense',
            'bonus_amount' => 'nullable|numeric|required_if:cash_type,deposit',
            'payment_type' => 'nullable|string|required_if:cash_type,deposit',
            'remarks' => 'nullable|string|max:255|required_if:cash_type,deposit|required_if:cash_type,withdraw',
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

            return response()->json(['success' => true, 'message' => 'Transaction successfully added!'], 201)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $cash = Cash::find($request->id);
        if ($cash) {
            $cash->delete();
            return response()->json(['success' => true, 'message' => 'Cash deleted successfully!'])
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }

        return response()->json(['success' => false, 'message' => 'Cash not found.'], 404)
            ->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
    }
}
