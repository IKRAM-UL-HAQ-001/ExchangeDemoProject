<?php

namespace App\Http\Controllers;

use App\Models\OwnerProfit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OwnerProfitListExport;
use Auth;
class OwnerProfitController extends Controller
{
    public function ownerProfitListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new OwnerProfitListExport($exchangeId), 'ownerProfitRecord.xlsx');
        }
    }
    
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();
            $ownerProfitRecords = OwnerProfit::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->orderBy('created_at', 'desc')->get();    
            return response()
                ->view('admin.owner_profit.list', compact('ownerProfitRecords'));
        }
    }
    
    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        } else {
            $user = Auth::user();
            $exchangeId = $user->exchange_id;
            $userId = $user->id;
    
            $validatedData = $request->validate([
                'cash_amount' => 'required|numeric',
                'remarks' => 'required|string|max:255',
            ]);
    
            try {
                // Create a new owner profit entry
                OwnerProfit::create([
                    'cash_amount' => $validatedData['cash_amount'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);
    
                // Return a JSON response
                return response()->json(['success' => true, 'message' => 'Transaction successfully added!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
            }
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $ownerProfit = OwnerProfit::find($request->id);
            if ($ownerProfit) {
                $ownerProfit->delete();
                return response()->json(['success' => true, 'message' => 'Owner Profit deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Owner Profit not found.'], 404);
        }
    }
    
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $user = Auth::user();
            $ownerProfitRecords = OwnerProfit::where('exchange_id', $user->exchange_id)
                ->where('user_id', $user->id)
                ->get();
            return response()
                ->view("exchange.owner_profit.list", compact('ownerProfitRecords'));
        }
    }
    
}
