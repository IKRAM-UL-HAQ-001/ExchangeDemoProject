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
use Illuminate\Http\Request;


class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/dashboard",
     *     summary="Fetch admin dashboard statistics",
     *     description="Retrieve daily, weekly, and monthly statistics for the admin dashboard.",
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard statistics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="totalUsers", type="integer"),
     *             @OA\Property(property="totalExchanges", type="integer"),
     *             @OA\Property(property="totalBalanceWeekly", type="float"),
     *             @OA\Property(property="totalDepositWeekly", type="float"),
     *             @OA\Property(property="totalWithdrawalWeekly", type="float"),
     *             @OA\Property(property="totalExpenseWeekly", type="float"),
     *             @OA\Property(property="totalFreezAmountWeekly", type="float"),
     *             @OA\Property(property="totalCustomersWeekly", type="integer"),
     *             @OA\Property(property="totalBalanceMonthly", type="float"),
     *             @OA\Property(property="totalDepositMonthly", type="float"),
     *             @OA\Property(property="totalWithdrawalMonthly", type="float"),
     *             @OA\Property(property="totalFreezAmountMonthly", type="float"),
     *             @OA\Property(property="totalCustomersMonthly", type="integer"),
     *             @OA\Property(property="totalBalanceDaily", type="float"),
     *             @OA\Property(property="totalDepositDaily", type="float"),
     *             @OA\Property(property="totalWithdrawalDaily", type="float"),
     *             @OA\Property(property="totalFreezAmountDaily", type="float"),
     *             @OA\Property(property="totalCustomersDaily", type="integer"),
     *             @OA\Property(property="totalBankBalance", type="float")
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

        // Calculate Daily Totals
        $totalDepositDaily = Cash::where('cash_type', 'deposit')->whereDate('created_at', $today)->sum('cash_amount');
        $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')->whereDate('created_at', $today)->where('approval', '1')->sum('cash_amount');
        $totalExpenseDaily = Cash::where('cash_type', 'expense')->whereDate('created_at', $today)->sum('cash_amount');
        $totalFreezAmountDaily = BankEntry::where('status', 'freeze')->whereDate('created_at', $today)->sum('cash_amount');
        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;

        // Weekly Totals
        $totalDepositWeekly = Cash::where('cash_type', 'deposit')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('cash_amount');
        $totalWithdrawalWeekly = Cash::where('cash_type', 'withdrawal')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->where('approval', '1')->sum('cash_amount');
        $totalExpenseWeekly = Cash::where('cash_type', 'expense')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('cash_amount');
        $totalFreezAmountWeekly = BankEntry::where('status', 'freez')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('cash_amount');
        $totalBalanceWeekly = $totalDepositWeekly - $totalWithdrawalWeekly - $totalExpenseWeekly;

        // Monthly Totals
        $totalDepositMonthly = Cash::where('cash_type', 'deposit')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalWithdrawalMonthly = Cash::where('cash_type', 'withdrawal')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('approval', '1')->sum('cash_amount');
        $totalExpenseMonthly = Cash::where('cash_type', 'expense')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalFreezAmountMonthly = BankEntry::where('status', 'freeze')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;

        // Bank Data and Balances
        $totalAmountAdd = BankEntry::where('cash_type', 'add')->where('status', null)->sum('cash_amount');
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')->where('status', null)->sum('cash_amount');
        $totalBankBalance = $totalAmountAdd - $totalAmountSubtract - $totalFreezAmountMonthly;

        // General Data
        $totalUsers = User::count();
        $totalExchanges = Exchange::count();

        return response()->json(compact(
            'totalUsers', 'totalExchanges',
            'totalBalanceWeekly', 'totalDepositWeekly', 'totalWithdrawalWeekly', 'totalExpenseWeekly', 'totalFreezAmountWeekly',
            'totalBalanceMonthly', 'totalDepositMonthly', 'totalWithdrawalMonthly', 'totalExpenseMonthly', 'totalFreezAmountMonthly',
            'totalBalanceDaily', 'totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily', 'totalFreezAmountDaily',
            'totalBankBalance'
        ), 200);
    }
}
