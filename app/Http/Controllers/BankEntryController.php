<?php

namespace App\Http\Controllers;

use App\Models\BankEntry;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class BankEntryController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        $user = auth()->user();
        $exchangeId = $user->exchange_id;
        $userId = $user->id;

        $bankEntryRecords = BankEntry::with('bank')
            ->where('exchange_id', $exchangeId)
            ->where('user_id', $userId)
            ->whereNull('status')
            ->paginate(20);
        $bankRecords = Bank::whereNull('status')->get();
        return view('exchange.bank.list',compact('bankEntryRecords','bankRecords'));
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
            ->paginate(20);
            $bankRecords = Bank::whereNull('status')->get();
            
            return view('exchange.bank.freezbank', compact('bankEntryRecords', 'bankRecords'));
        }    
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        $validatedData = $request->validate([
            'account_number' => [
                Rule::requiredIf($request->input('status') !== 'freez'), // Required if status is not 'freez'
                'string',
                'max:255',
            ],
            'cash_type' => 'required|string|max:255',
            'cash_amount' => 'required|numeric',
            'remarks' => 'required|string',
            'status' => 'nullable|string',
            'bank_id'=> 'required|numeric',       
        ]);
            $bankExists = Bank::find($request->bank_id);
            if($request->status == 'freez'){
                $bankExists->status = 'freez';
                $bankExists->save();
            }
        try {
            $user = Auth::user();
            if ($user->role == "exchange") {
                    $bankEntry = BankEntry::create([
                        'account_number' => $validatedData['account_number'] ?? 0,
                        'bank_id' =>  $bankExists->id,
                        'cash_amount' => (int) $validatedData['cash_amount'],
                        'cash_type' => $validatedData['cash_type'],
                        'remarks' => $validatedData['remarks'],
                        'status' => $validatedData['status'],
                        'exchange_id' => $user->exchange_id,
                        'user_id' => $user->id,
                    ]);
    
                return response()->json(['success' => true,'message' => 'Bank Entry Data saved successfully!'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false,'message' => 'An error occurred while saving Bank Entry Data: ' . $e->getMessage()], 500);
        }
    }

    public function unFreeze(Request $request)
    {
        $bankEntry = BankEntry::find($request->id);
        $bankRec = Bank::find($bankEntry->bank_id);
        $bankRec->status = null;
        $bankRec->save();

        $bankEntry->delete();
            return redirect()->back();
    }

    public function getBankBalance(Request $request) 
    {
        $request->validate(['bank_id' => 'required']);
        $sumBalance = BankEntry::where('bank_id', $request->bank_id)
            ->selectRaw('SUM(CASE WHEN cash_type = "add" THEN cash_amount WHEN cash_type = "minus" THEN -cash_amount END) as balance')
            ->value('balance');
        return response()->json(['balance' => $sumBalance ?? 0]);
    }
}
