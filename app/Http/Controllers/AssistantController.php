<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
use App\Models\Cash;
use App\Models\OwnerProfit;
use App\Models\Customer;
use App\Models\MasterSettling;
use App\Models\OpenCloseBalance;
use App\Models\BankEntry;
use App\Models\User;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
class AssistantController extends Controller
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
            
            $entriesDaily = OpenCloseBalance::whereDate('created_at', $today)
            ->get();

            $entriesMonth = OpenCloseBalance::whereMonth('created_at', $currentMonth)
            ->get();

            $totalOpenCloseBalanceDaily = 0;
            $totalOpenCloseBalanceMonthly = 0;

            if ($entriesDaily->count() === 1) {
                $entry = $entriesDaily->first();
                $totalOpenCloseBalanceDaily = $entry->open_balance + $entry->close_balance;
            } else {
                foreach ($entriesDaily as $entry) {
                    if ($totalOpenCloseBalanceDaily === 0) {
                        $totalOpenCloseBalanceDaily += $entry->open_balance;
                    }
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
            $totalDepositDaily = Cash::where('cash_type', 'deposit')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');

            $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');   

            $totalExpenseDaily = Cash::where('cash_type', 'expense')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');  

            $totalBonusDaily = Cash::whereDate('created_at', $today)
                ->sum('bonus_amount');
            $totalOldCustomersDaily = Cash::whereDate('created_at', $today)
                ->distinct('reference_number')
                ->count('reference_number');
            
            $totalOwnerProfitDaily = OwnerProfit::whereDate('created_at', $today)
                ->sum('cash_amount');
                
            $totalCustomerDaily = Customer::whereDate('created_at', $today)
                ->distinct('id')
                ->count('id');

            $totalBalanceDaily =  $totalDepositDaily -  $totalWithdrawalDaily -  $totalExpenseDaily ;
            
            $totalDepositMonthly = Cash::where('cash_type', 'deposit')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
        
            $totalWithdrawalMonthly = Cash::where('cash_type', 'withdrawal')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
            
            $totalExpenseMonthly = Cash::where('cash_type', 'expense')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
        
            $totalBonusMonthly = Cash::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('bonus_amount');
                
            $totalOldCustomersMonthly = Cash::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('reference_number')
                ->count('reference_number');
            
            $totalMasterSettlingMonthly = MasterSettling::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->distinct('settling_point')
            ->sum('settling_point');
            
            $totalOwnerProfitMonthly= OwnerProfit::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');
            
            $totalCustomerMonthly = Customer::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('id')
                ->count('id');
            
            $totalAmountAdd = BankEntry::where('cash_type', 'add')
                ->sum('cash_amount');

            $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
                ->sum('cash_amount');

            $totalBankBalance = $totalAmountAdd - $totalAmountSubtract;
            
            $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;
            $totalUsers = User::count();
            $totalExchanges = Exchange::count();
            return view('/assistant.dashboard',compact('totalUsers','totalExchanges',
                'totalBalanceMonthly','totalDepositMonthly','totalWithdrawalMonthly',
                'totalExpenseMonthly','totalMasterSettlingMonthly','totalOpenCloseBalanceMonthly',
                'totalBonusMonthly','totalOldCustomersMonthly','totalOwnerProfitMonthly',
                'totalCustomerMonthly','totalBalanceDaily','totalDepositDaily',
                'totalWithdrawalDaily','totalExpenseDaily','totalBonusDaily','totalOldCustomersDaily',
                'totalOwnerProfitDaily','totalCustomerDaily','totalBankBalance','totalOpenCloseBalanceDaily',

            ));
        }   
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Assistant $assistant)
    {
        //
    }

    public function edit(Assistant $assistant)
    {
        //
    }

    public function update(Request $request, Assistant $assistant)
    {
        //
    }

    public function destroy(Assistant $assistant)
    {
        //
    }
}
