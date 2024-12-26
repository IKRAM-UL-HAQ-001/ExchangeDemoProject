<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Cash;
use App\Models\OwnerProfit;
use App\Models\Customer;
use App\Models\MasterSettling;
use App\Models\BankEntry;
use App\Models\OpenCloseBalance;
use App\Models\User;
use App\Models\Exchange;
use App\Models\VenderPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $today = Carbon::today();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $currentWeek = Carbon::now()->weekOfYear;

            // Daily totals
            $totalOpenCloseBalance = OpenCloseBalance::whereDate('created_at', $today)
                ->sum('open_balance');

            $totalPaidAmountDaily = VenderPayment::whereDate('created_at', $today)
                ->sum('paid_amount');
            


            $totalDepositDaily = Cash::where('cash_type', 'deposit')
                ->whereDate('created_at', $today)
                ->sum('cash_amount');

            $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')
                ->whereDate('created_at', $today)
                ->where('approval', '1')
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
                    
            $totalCustomersDaily = Customer::whereDate('created_at', $today)
                ->distinct('id')
                ->count('id');

            $totalMasterSettlingDaily = MasterSettling::whereDate('created_at', $today)
                ->distinct('settling_point')
                ->sum('settling_point');

            $totalBalanceDaily =  $totalDepositDaily -  $totalWithdrawalDaily -  $totalExpenseDaily ;


            $totalOpenCloseBalanceDaily = $totalOpenCloseBalance + $totalBalanceDaily;
            

            // Weekly totals
            $totalDepositWeekly = Cash::where('cash_type', 'deposit')
            -> whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');

            $totalWithdrawalWeekly = Cash::where('cash_type', 'withdrawal')
            -> whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->where('approval', '1')
            ->sum('cash_amount');

            $totalExpenseWeekly = Cash::where('cash_type', 'expense')
            -> whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');

            $totalBonusWeekly = Cash::
             whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
                ->sum('bonus_amount');

            $totalOldCustomersWeekly = Cash:: whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
                ->distinct('reference_number')
                ->count('reference_number');

            $totalOwnerProfitWeekly = OwnerProfit:: whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
                ->sum('cash_amount');

            $totalCustomersWeekly = Customer:: whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
                ->distinct('id')
                ->count('id');

            $totalMasterSettlingWeekly = MasterSettling:: whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->distinct('settling_point')
            ->sum('settling_point');
            // Monthly totals
           
            $totalDepositMonthly = Cash::where('cash_type', 'deposit')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');

            $totalWithdrawalMonthly = Cash::where('cash_type', 'withdrawal')
                ->whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->where('approval', '1')
                ->sum('cash_amount');

            $totalPaidAmountMonthly = VenderPayment::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('paid_amount');

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

            $totalOwnerProfitMonthly = OwnerProfit::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->sum('cash_amount');

            $totalCustomersMonthly = Customer::whereMonth('created_at', $currentMonth)
                ->whereYear('created_at', $currentYear)
                ->distinct('id')
                ->count('id');
            
            $totalBalanceMonthly =  $totalDepositMonthly -  $totalWithdrawalMonthly -  $totalExpenseMonthly ;
            $totalOpenCloseBalanceMonthly = $totalOpenCloseBalance + $totalBalanceMonthly;
            // Bank data and balances
            $totalAmountAdd = BankEntry::where('cash_type', 'add')
            ->where('status',null)
            ->sum('cash_amount');
            $totalAmountSubtract = BankEntry::where('cash_type', 'minus')
            ->where('status',null)
            ->sum('cash_amount');
            $totalFreezAmount = BankEntry::where('status', 'freez')
            ->sum('cash_amount');
            
            $totalBankBalance = $totalAmountAdd - $totalAmountSubtract - $totalFreezAmount;

            // Weekly total balance calculation
            $totalBalanceWeekly = $totalDepositWeekly - $totalWithdrawalWeekly - $totalExpenseWeekly;

            // Data to pass to view
            $totalUsers = User::count();
            $totalExchanges = Exchange::count();
            
            $totalFreezAmountDaily = BankEntry::where('status', 'freeze')
            ->whereDate('created_at', $today)
            ->sum('cash_amount');
            
            $totalFreezAmountWeekly = BankEntry::where('status', 'freez')
            -> whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->sum('cash_amount');

            $totalFreezAmountMonthly = BankEntry::where('status', 'freeze')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
            $viewData = compact(
                'totalUsers', 'totalExchanges',
                
                'totalBalanceWeekly', 'totalDepositWeekly',
                'totalWithdrawalWeekly', 'totalExpenseWeekly', 'totalBonusWeekly', 'totalOldCustomersWeekly',
                'totalOwnerProfitWeekly', 'totalCustomersWeekly','totalFreezAmountWeekly',
                'totalMasterSettlingWeekly',
                
                'totalBalanceMonthly', 'totalDepositMonthly','totalWithdrawalMonthly', 
                'totalExpenseMonthly', 'totalMasterSettlingMonthly', 'totalPaidAmountMonthly',
                'totalBonusMonthly', 'totalOldCustomersMonthly', 'totalOwnerProfitMonthly', 
                'totalCustomersMonthly','totalFreezAmountMonthly',
                
                'totalBalanceDaily', 'totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily', 'totalBonusDaily',
                'totalOldCustomersDaily', 'totalOwnerProfitDaily', 'totalCustomersDaily', 'totalBankBalance',
                'totalOpenCloseBalanceDaily', 'totalPaidAmountDaily','totalFreezAmountDaily','totalMasterSettlingDaily',
                );

            return response()
                ->view('admin.dashboard', $viewData);
        }
    }

}
