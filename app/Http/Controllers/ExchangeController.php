<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\BankEntry;
use App\Models\Cash;
use App\Models\Customer;
use App\Models\VenderPayment;
use App\Models\OwnerProfit;
use App\Models\OpenCloseBalance;
use App\Models\MasterSettling;
use App\Models\User;
use Carbon\Carbon;
USE DB;
use Auth;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    // public function index()
    // {
    //     if (!auth()->check()) {
    //         return redirect()->route('auth.login');
    //     } else {
    //         $today = Carbon::today();
    //         $currentMonth = Carbon::now()->month;
    //         $currentYear = Carbon::now()->year;
    //         $currentWeek = Carbon::now()->weekOfYear;

    //         $userId = Auth::User()->id;
    //         $user = User::find($userId);
    //         $exchangeId = $user->exchange_id;
    //         $exchange = Exchange::find($exchangeId);
    //         $exchange_name = $exchange ? $exchange->name : null;
    //         $userCount = Cash::where('exchange_id', $exchangeId)->distinct('user_id')->count('user_id');
            
    //         $totalOpenCloseBalance = OpenCloseBalance::where('exchange_id', $exchangeId)
    //         ->whereDate('created_at', $today)
    //         ->sum('open_balance');
            
    //         $customerCountDaily = Cash::where('exchange_id', $exchangeId)
    //             ->whereDate('created_at', $today)
    //             ->distinct('reference_number')
    //             ->count('reference_number');



    //         $totalDepositDaily = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'deposit')
    //             ->whereDate('created_at', $today)
    //             ->sum('cash_amount');
            
    //         $totalWithdrawalDaily = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'withdrawal')
    //             ->whereDate('created_at', $today)
    //             ->sum('cash_amount');

    //         $totalExpenseDaily = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'expense')
    //             ->whereDate('created_at', $today)
    //             ->sum('cash_amount');

    //         $totalBonusDaily = Cash::where('exchange_id', $exchangeId)
    //             ->whereDate('created_at', $today)
    //             ->sum('bonus_amount');
            
    //         $totalOwnerProfitDaily = OwnerProfit::where('exchange_id', $exchangeId)
    //             ->whereDate('created_at', $today)
    //             ->sum('cash_amount');
                
    //         $totalNewCustomerDaily = Customer::where('exchange_id', $exchangeId)
    //             ->whereDate('created_at', $today)
    //             ->distinct('id')
    //             ->count('id');
    //          // Weekly Metrics
            
    //         $totalOpenCloseBalanceWeekly = OpenCloseBalance::where('exchange_id', $exchangeId)
    //         ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('open_balance');
            
    //          $totalDepositWeekly = Cash::where('exchange_id', $exchangeId)
    //         ->where('cash_type', 'deposit')
    //         ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('cash_amount');

    //         $customerCountWeekly = Cash::where('exchange_id', $exchangeId)
    //         ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //         ->whereYear('created_at', $currentYear)
    //         ->distinct('reference_number')
    //         ->count('reference_number');

    //         $totalWithdrawalWeekly = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'withdrawal')
    //             ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');

    //         $totalExpenseWeekly = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'expense')
    //             ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');

    //         $totalBonusWeekly = Cash::where('exchange_id', $exchangeId)
    //             ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('bonus_amount');

    //         $totalOwnerProfitWeekly = OwnerProfit::where('exchange_id', $exchangeId)
    //             ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');

    //         $totalMasterSettlingWeekly = MasterSettling::where('exchange_id', $exchangeId)
    //         ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //         ->whereYear('created_at', $currentYear)
    //         ->distinct('settling_point')
    //         ->sum('settling_point');
            
    //         $totalNewCustomerWeekly = Customer::where('exchange_id', $exchangeId)
    //             ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //             ->whereYear('created_at', $currentYear)
    //             ->distinct('id')
    //             ->count('id');
    //         $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;
    //         $totalBalanceWeekly = $totalDepositWeekly - $totalWithdrawalWeekly - $totalExpenseWeekly;

    //         $totalOpenCloseBalanceDaily = $totalOpenCloseBalance + $totalBalanceDaily;
    //         $totalOpenCloseBalanceWeekly = $totalOpenCloseBalanceWeekly + $totalBalanceWeekly;
            
    //         $customerCountMonthly = Cash::where('exchange_id', $exchangeId)
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->distinct('reference_number')
    //             ->count('reference_number');

    //         $totalDepositMonthly = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'deposit')
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');

    //         $totalWithdrawalMonthly = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'withdrawal')
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');
            
    //         $totalExpenseMonthly = Cash::where('exchange_id', $exchangeId)
    //             ->where('cash_type', 'expense')
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');

    //         $totalBonusMonthly = Cash::where('exchange_id', $exchangeId)
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('bonus_amount');
            
    //         $totalMasterSettlingMonthly = MasterSettling::where('exchange_id', $exchangeId)
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->distinct('settling_point')
    //             ->sum('settling_point');
            
    //         $totalOwnerProfitMonthly = OwnerProfit::where('exchange_id', $exchangeId)
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->sum('cash_amount');
            
    //         $totalAmountAdd = BankEntry::where('cash_type', 'add')
    //         ->where('exchange_id',$exchangeId)
    //         ->where('status','!=','freez')
    //         ->sum('cash_amount');

    //         $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
    //         ->where('exchange_id',$exchangeId)
    //         ->where('status','!=','freez')
    //         ->sum('cash_amount');
            
    //         $totalFreezAmountDaily = BankEntry::where('status', 'freez')
    //         ->where('exchange_id',$exchangeId)
    //         ->whereDate('created_at', $today)
    //         ->sum('cash_amount');
            
    //         $totalFreezAmountWeekly = BankEntry::where('status', 'freez')
    //         ->where('exchange_id',$exchangeId)
    //         ->where(DB::raw("WEEK(created_at)"), $currentWeek)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('cash_amount');

    //         $totalFreezAmountMonthly = BankEntry::where('status', 'freez')
    //         ->where('exchange_id',$exchangeId)
    //         ->whereMonth('created_at', $currentMonth)
    //         ->whereYear('created_at', $currentYear)
    //         ->sum('cash_amount');
            
    //         $totalBankBalance = $totalAmountAdd - $totalAmountSubtract;
    //         $totalNewCustomerMonthly = Customer::where('exchange_id', $exchangeId)
    //             ->whereMonth('created_at', $currentMonth)
    //             ->whereYear('created_at', $currentYear)
    //             ->distinct('id')
    //             ->count('id');

    //         $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;
    //         $nonce = base64_encode(random_bytes(16));
            
    //         return response()
    //             ->view("exchange.dashboard", compact('totalBankBalance', 'exchange_name', 'userCount',
    //                 'totalBalanceDaily', 'totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily',
    //                 'customerCountDaily', 'totalBonusDaily', 'totalNewCustomerDaily', 'totalOwnerProfitDaily',
    //                 'totalOpenCloseBalanceDaily','totalFreezAmountDaily',
                    
    //                 'totalBankBalance', 'exchange_name', 'userCount',
    //                 'totalBalanceWeekly', 'totalDepositWeekly', 'totalWithdrawalWeekly', 'totalExpenseWeekly',
    //                 'customerCountWeekly', 'totalBonusWeekly', 'totalNewCustomerWeekly', 'totalOwnerProfitWeekly',
    //                 'totalOpenCloseBalanceWeekly','totalFreezAmountWeekly','totalMasterSettlingWeekly',

    //                 'totalBalanceMonthly', 'totalDepositMonthly', 'totalWithdrawalMonthly', 'totalExpenseMonthly',
    //                 'totalMasterSettlingMonthly', 'totalBonusMonthly', 'customerCountMonthly', 'totalNewCustomerMonthly',
    //                 'totalOwnerProfitMonthly','totalFreezAmountMonthly',));
    //     }
    // }
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
    
        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentWeek = Carbon::now()->weekOfYear;
    
        $userId = Auth::user()->id;
        $user = User::find($userId);
    
        if (!$user) {
            return redirect()->route('auth.login')->with('error', 'User not found.');
        }
    
        $exchangeId = $user->exchange_id;
        if (!$exchangeId) {
            return redirect()->route('auth.login')->with('error', 'No associated exchange found for the user.');
        }
    
        $exchange = Exchange::find($exchangeId);
        $exchange_name = $exchange ? $exchange->name : 'Unknown Exchange';
    
        $userCount = Cash::where('exchange_id', $exchangeId)->distinct('user_id')->count('user_id');
    
        // Total Bank Balance
        $totalAmountAdd = BankEntry::where('cash_type', 'add')
            ->where('exchange_id', $exchangeId)
            ->where('status', '!=', 'freez')
            ->sum('cash_amount');
    
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
            ->where('exchange_id', $exchangeId)
            ->where('status', '!=', 'freez')
            ->sum('cash_amount');
    
        $totalBankBalance = $totalAmountAdd - $totalAmountSubtract;
    
        // Vendor Payments Metrics
        $totalVendorPaymentsDaily = VenderPayment::where('exchange_id', $exchangeId)
            ->whereDate('created_at', $today)
            ->sum('paid_amount');
    
        $totalVendorPaymentsWeekly = VenderPayment::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('paid_amount');
    
        $totalVendorPaymentsMonthly = VenderPayment::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('paid_amount');
    
        // Daily Metrics
        $totalOpenCloseBalance = OpenCloseBalance::where('exchange_id', $exchangeId)
            ->whereDate('created_at', $today)
            ->sum('open_balance');
    
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
    
        $totalFreezAmountDaily = BankEntry::where('status', 'freez')
            ->where('exchange_id', $exchangeId)
            ->whereDate('created_at', $today)
            ->sum('cash_amount');
    
        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;
    
        $totalOpenCloseBalanceDaily = $totalOpenCloseBalance + $totalBalanceDaily;
    
        // Weekly Metrics
        $totalFreezAmountWeekly = BankEntry::where('status', 'freez')
            ->where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalDepositWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'deposit')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalWithdrawalWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'withdrawal')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalExpenseWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'expense')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalBonusWeekly = Cash::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('bonus_amount');
    
        $customerCountWeekly = Cash::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('reference_number')
            ->count('reference_number');
    
        $totalOwnerProfitWeekly = OwnerProfit::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalMasterSettlingWeekly = MasterSettling::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('settling_point')
            ->sum('settling_point');
    
        $totalNewCustomerWeekly = Customer::where('exchange_id', $exchangeId)
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('id')
            ->count('id');
    
        $totalBalanceWeekly = $totalDepositWeekly - $totalWithdrawalWeekly - $totalExpenseWeekly;
    
        // Monthly Metrics
        $totalFreezAmountMonthly = BankEntry::where('status', 'freez')
            ->where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
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
    
        $customerCountMonthly = Cash::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->distinct('reference_number')
            ->count('reference_number');
    
        $totalOwnerProfitMonthly = OwnerProfit::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
    
        $totalMasterSettlingMonthly = MasterSettling::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->distinct('settling_point')
            ->sum('settling_point');
    
        $totalNewCustomerMonthly = Customer::where('exchange_id', $exchangeId)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->distinct('id')
            ->count('id');
    
        $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;
    
        // Return to view
        return response()->view("exchange.dashboard", compact(
            'totalBankBalance', 'totalVendorPaymentsDaily', 'totalVendorPaymentsWeekly', 'totalVendorPaymentsMonthly',
            'totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily', 'totalBonusDaily', 'totalOwnerProfitDaily',
            'totalNewCustomerDaily', 'exchange_name', 'userCount', 'totalOpenCloseBalanceDaily', 'customerCountDaily',
            'totalFreezAmountDaily', 'totalBalanceDaily', 'totalFreezAmountWeekly', 'totalDepositWeekly',
            'totalWithdrawalWeekly', 'totalExpenseWeekly', 'totalBonusWeekly', 'customerCountWeekly',
            'totalOwnerProfitWeekly', 'totalMasterSettlingWeekly', 'totalNewCustomerWeekly', 'totalBalanceWeekly',
            'totalFreezAmountMonthly', 'totalDepositMonthly', 'totalWithdrawalMonthly', 'totalExpenseMonthly',
            'totalBonusMonthly', 'customerCountMonthly', 'totalOwnerProfitMonthly', 'totalMasterSettlingMonthly',
            'totalNewCustomerMonthly', 'totalBalanceMonthly'
        ));
    }
    
    public function exchangeList()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeRecords = Exchange::orderBy('created_at', 'desc')->get();
            return response()
                ->view("admin.exchange.list", compact('exchangeRecords'));
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
            Exchange::create([
                'name' => $request->name
            ]);
            return response()->json(['message' => 'Exchange added successfully!'], 201);
        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchange = Exchange::find($request->id);
            if ($exchange) {
                $exchange->delete();
                return response()->json(['success' => true, 'message' => 'Exchange deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Exchange not found.'], 404);
        }
    }
}
