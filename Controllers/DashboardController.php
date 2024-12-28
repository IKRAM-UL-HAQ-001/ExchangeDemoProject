<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Cash;
use App\Models\BankEntry;
use App\Models\User;
use App\Models\Exchange;
use App\Models\VenderPayment;
use App\Models\OwnerProfit;
use App\Models\OpenCloseBalance;
use App\Models\MasterSettling;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/dashboard",
     *     summary="Retrieve daily, weekly, and monthly metrics",
     *     description="Fetch metrics for daily, weekly, and monthly views, including deposits, withdrawals, expenses, and balances.",
     *     @OA\Response(
     *         response=200,
     *         description="Metrics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="totalUsers", type="integer"),
     *             @OA\Property(property="totalExchanges", type="integer"),
     *             @OA\Property(property="totalBalanceDaily", type="number"),
     *             @OA\Property(property="totalBalanceWeekly", type="number"),
     *             @OA\Property(property="totalBalanceMonthly", type="number"),
     *             @OA\Property(property="totalBankBalance", type="number"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Daily Totals
        $totalDepositDaily = Cash::where('cash_type', 'deposit')->whereDate('created_at', $today)->sum('cash_amount');
        $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')->whereDate('created_at', $today)->where('approval', '1')->sum('cash_amount');
        $totalExpenseDaily = Cash::where('cash_type', 'expense')->whereDate('created_at', $today)->sum('cash_amount');
        $totalFreezAmountDaily = BankEntry::where('status', 'freeze')->whereDate('created_at', $today)->sum('cash_amount');
        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;

        // Weekly Totals
        $totalDepositWeekly = Cash::where('cash_type', 'deposit')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('cash_amount');
        $totalWithdrawalWeekly = Cash::where('cash_type', 'withdrawal')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('approval', '1')
            ->sum('cash_amount');
        $totalExpenseWeekly = Cash::where('cash_type', 'expense')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('cash_amount');
        $totalBalanceWeekly = $totalDepositWeekly - $totalWithdrawalWeekly - $totalExpenseWeekly;

        // Monthly Totals
        $totalDepositMonthly = Cash::where('cash_type', 'deposit')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
        $totalWithdrawalMonthly = Cash::where('cash_type', 'withdrawal')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->where('approval', '1')
            ->sum('cash_amount');
        $totalExpenseMonthly = Cash::where('cash_type', 'expense')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
        $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;

        // Bank Balances
        $totalAmountAdd = BankEntry::where('cash_type', 'add')->whereNull('status')->sum('cash_amount');
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')->whereNull('status')->sum('cash_amount');
        $totalBankBalance = $totalAmountAdd - $totalAmountSubtract;

        // General Metrics
        $totalUsers = User::count();
        $totalExchanges = Exchange::count();

        return response()->json(compact(
            'totalUsers', 'totalExchanges',
            'totalBalanceDaily', 'totalBalanceWeekly', 'totalBalanceMonthly',
            'totalBankBalance'
        ), 200);
    }

    /**
     * @OA\Get(
     *     path="/assistant-dashboard",
     *     summary="Retrieve assistant dashboard metrics",
     *     description="Fetch metrics specifically for assistant users.",
     *     @OA\Response(
     *         response=200,
     *         description="Metrics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized access. Please log in.'], 401);
        }

        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Daily Metrics
        $totalDepositDaily = Cash::where('cash_type', 'deposit')->whereDate('created_at', $today)->sum('cash_amount');
        $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')->whereDate('created_at', $today)->sum('cash_amount');
        $totalExpenseDaily = Cash::where('cash_type', 'expense')->whereDate('created_at', $today)->sum('cash_amount');
        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;

        return response()->json(compact('totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily', 'totalBalanceDaily'), 200);
    }
}
