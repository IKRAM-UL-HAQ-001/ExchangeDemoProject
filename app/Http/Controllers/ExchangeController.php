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
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{

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
    
        // freez amount
        $totalFreezAmount = BankEntry::where('status', 'freez')
        ->where('exchange_id', $exchangeId)
        ->sum('cash_amount');
        
        
        $totalFreezAmountDaily = BankEntry::where('status', 'freez')
        ->where('exchange_id', $exchangeId)
        ->whereDate('created_at', $today)
        ->sum('cash_amount');

        // Total Bank Balance
        $totalAmountAdd = BankEntry::where('cash_type', 'add')
            ->where('exchange_id', $exchangeId)
            ->where('status', null)
            ->sum('cash_amount');
    
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
            ->where('exchange_id', $exchangeId)
            ->where('status', null)
            ->sum('cash_amount');
    
        $totalBankBalance = $totalAmountAdd - $totalAmountSubtract - $totalFreezAmount;
    
        // Vendor Payments Metrics
        $totalVendorPaymentsDaily = VenderPayment::where('exchange_id', $exchangeId)
            ->whereDate('created_at', $today)
            ->sum('paid_amount');
    
        $totalVendorPaymentsWeekly = VenderPayment::where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
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
            ->where('approval', '1')
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
    

        $totalMasterSettlingDaily = MasterSettling::where('exchange_id', $exchangeId)
        ->whereDate('created_at', $today)
        ->distinct('settling_point')
        ->sum('settling_point');

        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;
    
        $totalOpenCloseBalanceDaily = $totalOpenCloseBalance + $totalBalanceDaily;
    
        // Weekly Metrics
        $totalFreezAmountWeekly = BankEntry::where('status', 'freez')
            ->where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');
    
        $totalDepositWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'deposit')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');
    
        $totalWithdrawalWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'withdrawal')
            ->where('approval', '1')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');
    
        $totalExpenseWeekly = Cash::where('exchange_id', $exchangeId)
            ->where('cash_type', 'expense')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');
    
        $totalBonusWeekly = Cash::where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('bonus_amount');
    
        $customerCountWeekly = Cash::where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->distinct('reference_number')
            ->count('reference_number');
    
        $totalOwnerProfitWeekly = OwnerProfit::where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');
    
        $totalMasterSettlingWeekly = MasterSettling::where('exchange_id', $exchangeId)
        ->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])
        ->whereNotNull('settling_point')
        ->groupBy('settling_point')
        ->sum('settling_point');

        
        $totalNewCustomerWeekly = Customer::where('exchange_id', $exchangeId)
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
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
            ->where('approval', '1')
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
            'totalFreezAmountDaily', 'totalBalanceDaily','totalMasterSettlingDaily','customerCountDaily',
            'customerCountWeekly',  
            'totalFreezAmountWeekly', 'totalDepositWeekly', 'totalWithdrawalWeekly', 'totalExpenseWeekly', 'totalBonusWeekly', 'customerCountWeekly',
            'totalOwnerProfitWeekly', 'totalMasterSettlingWeekly', 'totalNewCustomerWeekly', 'totalBalanceWeekly',
            'totalFreezAmountMonthly', 'totalDepositMonthly', 'totalWithdrawalMonthly', 'totalExpenseMonthly',
            'totalBonusMonthly', 'totalOwnerProfitMonthly', 'totalMasterSettlingMonthly',
            'totalNewCustomerMonthly', 'totalBalanceMonthly','customerCountMonthly'
        ));
    }
    
    public function exchangeList()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeRecords = Exchange::orderBy('created_at', 'desc')->paginate(20);
            return response()
                ->view("admin.exchange.list", compact('exchangeRecords'));
        }
    }

    public function assistantExchangeList()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeRecords = Exchange::orderBy('created_at', 'desc')->paginate(20);
            return response()
                ->view("assistant.exchange.list", compact('exchangeRecords'));
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
            $exchange = Exchange::find($request->id);
            if ($exchange) {
                $exchange->delete();
                return response()->json(['success' => true, 'message' => 'Exchange deleted successfully!'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Exchange not found.'], 404);
    
    }
}
