<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankEntry;
use Illuminate\Http\Request;
Use App\Exports\BankListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
class BankController extends Controller
{
    public function bankExportExcel()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            return Excel::download(new BankListExport, 'BankList.xlsx');
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bankRecords = Bank::orderBy('created_at', 'desc')->paginate(20);
            return view("admin.bank.list", compact('bankRecords'));
        }
    }
    
    public function freezBankIndex(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeId = Auth::user()->exchange_id;
            $userId = Auth::user()->id;
            $bankEntryRecords = BankEntry::where('status', "freez")
            ->paginate(20);
            $bankRecords = Bank::all();
            
            return view('admin.bank_freez.list', compact('bankEntryRecords', 'bankRecords'));
        }    
    }
    
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            Bank::create([
                'name' => $request->name
            ]);
            return response()->json(['message' => 'Bank added successfully!'], 201);
        }
    }

    public function destroy(Request $request)
    {
        dd($request);
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bank = Bank::find($request->id);
            if ($bank) {
                $bank->delete();
                return response()->json(['success' => true, 'message' => 'Bank deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Bank not found.'], 404);
        }
    }
    public function delete(Request $request)
    {
        $bank = BankEntry::find($request->id);
        if ($bank) {
            $bank->delete();
            return redirect()->back();
        }
    }

}
