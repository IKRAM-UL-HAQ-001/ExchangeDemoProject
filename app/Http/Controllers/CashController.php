<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\ExcelFile;

class CashController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        $user = Auth::user();
        $userId = $user->id;
        $exchangeId = $user->exchange_id;
        $cashRecords = Cash::where('exchange_id', $exchangeId)
            ->where('user_id', $userId)->paginate(20);

        return view('exchange.cash.list', compact('cashRecords'));
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
        // dd($request);
        $validatedData = $request->validate([
            'reference_number' => 'nullable|string|max:255|unique:cashes,reference_number',
            'customer_name' => 'nullable|string|max:255|required_if:cash_type,deposit',
            'cash_amount' => 'nullable|numeric',
            'customer_phone' => 'nullable|numeric',
            'cash_type' => 'required|in:deposit,withdrawal,expense',
            'bonus_amount' => 'nullable|numeric|required_if:cash_type,deposit',
            'payment_type' => 'nullable|string|required_if:cash_type,deposit,withdrawal',
            'remarks' => 'nullable|string|max:255|',
        ]);
        try {
            $user = Auth::user();
            Cash::create([
                'reference_number' => $validatedData['reference_number'] ?? null,
                'customer_name' => $validatedData['customer_name'] ?? null,
                'cash_amount' => $validatedData['cash_amount'] ?? null,
                'customer_phone' => $validatedData['customer_phone'] ?? null,
                'cash_type' => $validatedData['cash_type'] ?? null,
                'bonus_amount' => $validatedData['bonus_amount'] ?? 0,
                'payment_type' => $validatedData['payment_type'] ?? null,
                'remarks' => $validatedData['remarks'] ?? null,
                'user_id' => $user->id,
                'exchange_id' => $user->exchange_id,
            ]);
            return response()->json(['success' => true, 'message' => 'Transaction successfully added!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }
    public function approval(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }
        $approval = $request->approval;
        $id = $request->id;
        $cash = Cash::find($id);
        if ($cash) {
            $cash->approval = $approval;
            $cash->update();
        } else {
            return redirect()->back()->withErrors([
                'error' => 'Entry Not Found',
            ]);
        }
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $cash = Cash::find($request->id);
        if ($cash) {
            $cash->delete();
            return response()->json(['success' => true, 'message' => 'Cash deleted successfully!']);
        }

        return response()->json(['error' => false, 'message' => 'Cash not found.'], 404);
    }
}
