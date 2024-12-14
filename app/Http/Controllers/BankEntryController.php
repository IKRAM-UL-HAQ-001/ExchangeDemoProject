<?php

namespace App\Http\Controllers;

use App\Models\BankEntry;
use App\Models\Bank;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
class BankEntryController extends Controller
{

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeId = Auth::user()->exchange_id;
            $bankEntryRecords = BankEntry::where('exchange_id', $exchangeId)
            ->where('user_id', $userId)->get();
            $bankRecords = Bank::all();
            
            return view('exchange.bank.list', compact('bankEntryRecords', 'bankRecords'));
        }    
    }

    public function freezBankIndex(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeId = Auth::user()->exchange_id;
            $userId = Auth::user()->id;
            $bankEntryRecords = BankEntry::where('exchange_id', $exchangeId)
            ->where('user_id', $userId)
            ->where('status', "freez")
            ->get();
            $bankRecords = Bank::all();
            
            return view('exchange.bank.freezbank', compact('bankEntryRecords', 'bankRecords'));
        }    
    }
    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bankRecords = Bank::all();
            
            return view("exchange.bank.list", compact('bankRecords'));
        }
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        $validatedData = $request->validate([
            'account_number' => Rule::requiredIf(!$request->has('freez')),'string','max:255',
            'bank_name' => 'required|string|max:255',
            'cash_type' => 'required|string|max:255',
            'cash_amount' => 'required|numeric',
            'remarks' => 'required|string',
        ]);
        try {

            $user = Auth::user();
            if ($user->role == "exchange") {
                $bankEntry = BankEntry::create([
                    'account_number' => $validatedData['account_number'] ?? 0,
                    'bank_name' => $validatedData['bank_name'],
                    'cash_amount' => (int) $validatedData['cash_amount'],
                    'cash_type' => $validatedData['cash_type'],
                    'remarks' => $validatedData['remarks'],
                    'status' => "freez",
                    'exchange_id' => $user->exchange_id,
                    'user_id' => $user->id,
                ]);
                return response()->json(['success' => true,'message' => 'Bank Entry Data saved successfully!'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => 'An error occurred while saving Bank Entry Data: ' . $e->getMessage()], 500);
        }
    }

    public function getBankBalance(Request $request) 
    {
        $request->validate(['bank_name' => 'required|string']);

        $sumBalance = BankEntry::where('bank_name', $request->bank_name)
            ->selectRaw('SUM(CASE WHEN cash_type = "add" THEN cash_amount WHEN cash_type = "minus" THEN -cash_amount END) as balance')
            ->value('balance');
        return response()->json(['balance' => $sumBalance ?? 0]);
    }
}
