<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\BankEntry;
use App\Models\Cash;
use App\Models\Customer;
use App\Models\OwnerProfit;
use App\Models\OpenCloseBalance;
use App\Models\MasterSettling;
use App\Models\User;
use Carbon\Carbon;
use Auth;
use Illuminate\Http\Request;

class ExchangeController extends Controller
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
            $today = Carbon::today();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            
            $userId= Auth::User()->id;
            $user = User::find($userId);
            $exchangeId = $user->exchange_id;
            $exchange = Exchange::find($exchangeId);
            $exchange_name = $exchange ? $exchange->name : null;
            $userCount = Cash::where('exchange_id', $exchangeId)->distinct('user_id')->count('user_id');
            

            $entriesDaily = OpenCloseBalance::where('exchange_id', $exchangeId)
            ->whereDate('created_at', $today)
            ->get();

            $entriesMonth = OpenCloseBalance::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->get();
        
            $totalOpenCloseBalanceDaily = 0;
            $totalOpenCloseBalanceMonthly=0;
            
            if ($entriesDaily->count() === 1) {
                $entry = $entriesDaily->first();
                $totalOpenCloseBalanceDaily = $entry->open_balance + $entry->close_balance;
            } else {
                // If there are multiple entriesDaily, sum the closing balances
                foreach ($entriesDaily as $entry) {
                    // If it's the first entry, add its opening balance
                    if ($totalOpenCloseBalanceDaily === 0) {
                        $totalOpenCloseBalanceDaily += $entry->open_balance;
                    }
                    // Always add the closing balance
                    $totalOpenCloseBalanceDaily += $entry->close_balance;
                }
            }

            if ($entriesMonth->count() === 1) {
                $entry = $entriesMonth->first();
                $totalOpenCloseBalanceMonthly = $entry->open_balance + $entry->close_balance;
            } else {
                foreach ($entriesMonth as $entry) {
                    if ($totalOpenCloseBalanceMonthly === 0) {
                        $totalOpenCloseBalanceMonthly += $entry->open_balance;
                    }
                    $totalOpenCloseBalanceMonthly += $entry->close_balance;
                }
            }

            $customerCountDaily = Cash::where('exchange_id', $exchangeId)
                ->whereDate('created_at', $today)
                ->distinct('reference_number')
                ->count('reference_number');

            $totalDepositDaily = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');

            $totalWithdrawalDaily = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'withdrawal')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');

            $totalExpenseDaily = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'expense')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');

            $totalBonusDaily = Cash::where('exchange_id', $exchangeId)
                ->whereDate('created_at', $today)
                ->sum('bonus_amount');
            
            $totalOwnerProfitDaily = OwnerProfit::where('exchange_id', $exchangeId)
                ->whereDate('created_at', $today)
                ->sum('cash_amount');
                
            $totalNewCustomerDaily = Customer::where('exchange_id', $exchangeId)
                ->whereDate('created_at', $today)
                ->distinct('id')
                ->count('id');

            $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;

            $customerCountMonthly = Cash::where('exchange_id', $exchangeId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('reference_number')
                ->count('reference_number');

            $totalDepositMonthly = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');

            $totalWithdrawalMonthly = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'withdrawal')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
            
            $totalExpenseMonthly = Cash::where('exchange_id', $exchangeId)
                ->where('cash_type', 'expense')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');

            $totalBonusMonthly = Cash::where('exchange_id', $exchangeId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('bonus_amount');
            
            $totalMasterSettlingMonthly = MasterSettling::where('exchange_id', $exchangeId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('settling_point')
                ->sum('settling_point');
            
            $totalOwnerProfitMonthly = OwnerProfit::where('exchange_id', $exchangeId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
            
            $totalAmountAdd = BankEntry::where('cash_type', 'add')
                ->sum('cash_amount');

            $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
                ->sum('cash_amount');

            $totalBankBalance = $totalAmountAdd - $totalAmountSubtract;
            $totalNewCustomerMonthly = Customer::where('exchange_id', $exchangeId)
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('id')
                ->count('id');

            $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;

            return view("exchange.dashboard",compact('totalBankBalance','exchange_name','userCount',
                'totalBalanceDaily','totalDepositDaily','totalWithdrawalDaily','totalExpenseDaily',
                'customerCountDaily','totalBonusDaily','totalNewCustomerDaily','totalOwnerProfitDaily',
                'totalOpenCloseBalanceDaily',
                
                'totalBalanceMonthly','totalDepositMonthly','totalWithdrawalMonthly','totalExpenseMonthly',
                'totalMasterSettlingMonthly','totalBonusMonthly','customerCountMonthly','totalNewCustomerMonthly',
                'totalOwnerProfitMonthly','totalOpenCloseBalanceMonthly',
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function exchangeList()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $exchangeRecords = Exchange::orderBy('created_at', 'desc')->get();
            return view("admin.exchange.list",compact('exchangeRecords'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            Exchange::create([
                'name' => $request->name
            ]);
            return response()->json(['message' => 'Exchange added successfully!'], 201);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exchange $exchange)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exchange $exchange)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exchange $exchange)
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
            $exchange = Exchange::find($request->id);
            if ($exchange) {
                $exchange->delete();
                return response()->json(['success' => true, 'message' => 'Exchange deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Exchange not found.'], 404);
        }
    }
}
