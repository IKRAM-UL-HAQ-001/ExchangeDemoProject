<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
Use App\Exports\BankListExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
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
            $bankRecords = Bank::orderBy('created_at', 'desc')->get();
            return view("admin.bank.list", compact('bankRecords'))
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
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
            return response()->json(['message' => 'Bank added successfully!'], 201)
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
        } else {
            $bank = Bank::find($request->id);
            if ($bank) {
                $bank->delete();
                return response()->json(['success' => true, 'message' => 'Bank deleted successfully!'])
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                    ]);
            }
            return response()->json(['success' => false, 'message' => 'Bank not found.'], 404)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
        }
    }

}
