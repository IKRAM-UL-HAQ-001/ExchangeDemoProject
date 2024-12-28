<?php

namespace App\Http\Controllers;

use App\Models\Assistant;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class AssistantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/assistant/dashboard",
     *     summary="Fetch assistant dashboard metrics",
     *     description="Retrieve metrics for assistant dashboards including daily, weekly, and monthly summaries.",
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard metrics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="totalUsers", type="integer"),
     *             @OA\Property(property="totalExchanges", type="integer"),
     *             @OA\Property(property="totalBalanceDaily", type="float"),
     *             @OA\Property(property="totalBankBalance", type="float"),
     *             @OA\Property(property="totalFreezAmountDaily", type="float"),
     *             @OA\Property(property="totalDepositWeekly", type="float"),
     *             @OA\Property(property="totalBonusMonthly", type="float")
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
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        }

        $today = Carbon::today();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentWeek = Carbon::now()->weekOfYear;

        // Daily totals
        $totalOpenCloseBalance = OpenCloseBalance::whereDate('created_at', $today)->sum('open_balance');
        $totalPaidAmountDaily = VenderPayment::whereDate('created_at', $today)->sum('paid_amount');
        $totalDepositDaily = Cash::where('cash_type', 'deposit')->whereDate('created_at', $today)->sum('cash_amount');
        $totalWithdrawalDaily = Cash::where('cash_type', 'withdrawal')->whereDate('created_at', $today)->where('approval', 1)->sum('cash_amount');
        $totalExpenseDaily = Cash::where('cash_type', 'expense')->whereDate('created_at', $today)->sum('cash_amount');
        $totalBonusDaily = Cash::whereDate('created_at', $today)->sum('bonus_amount');
        $totalOldCustomersDaily = Cash::whereDate('created_at', $today)->distinct('reference_number')->count('reference_number');
        $totalOwnerProfitDaily = OwnerProfit::whereDate('created_at', $today)->sum('cash_amount');
        $totalCustomersDaily = Customer::whereDate('created_at', $today)->distinct('id')->count('id');
        $totalBalanceDaily = $totalDepositDaily - $totalWithdrawalDaily - $totalExpenseDaily;
        $totalOpenCloseBalanceDaily = $totalOpenCloseBalance + $totalBalanceDaily;

        // Weekly totals
        $totalDepositWeekly = Cash::where('cash_type', 'deposit')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
        $totalWithdrawalWeekly = Cash::where('cash_type', 'withdrawal')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->where('approval', '1')
            ->sum('cash_amount');
        $totalExpenseWeekly = Cash::where('cash_type', 'expense')
            ->where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
        $totalBonusWeekly = Cash::where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('bonus_amount');
        $totalOldCustomersWeekly = Cash::where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('reference_number')
            ->count('reference_number');
        $totalOwnerProfitWeekly = OwnerProfit::where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->sum('cash_amount');
        $totalCustomersWeekly = Customer::where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('id')
            ->count('id');
        $totalMasterSettlingWeekly = MasterSettling::where(DB::raw("WEEK(created_at)"), $currentWeek)
            ->whereYear('created_at', $currentYear)
            ->distinct('settling_point')
            ->sum('settling_point');

        // Monthly totals
        $totalDepositMonthly = Cash::where('cash_type', 'deposit')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalWithdrawalMonthly = Cash::where('cash_type', 'withdrawal')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->where('approval', '1')->sum('cash_amount');
        $totalPaidAmountMonthly = VenderPayment::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('paid_amount');
        $totalExpenseMonthly = Cash::where('cash_type', 'expense')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalBonusMonthly = Cash::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('bonus_amount');
        $totalOldCustomersMonthly = Cash::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->distinct('reference_number')->count('reference_number');
        $totalMasterSettlingMonthly = MasterSettling::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->distinct('settling_point')->sum('settling_point');
        $totalOwnerProfitMonthly = OwnerProfit::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('cash_amount');
        $totalCustomersMonthly = Customer::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->distinct('id')->count('id');
        $totalBalanceMonthly = $totalDepositMonthly - $totalWithdrawalMonthly - $totalExpenseMonthly;
        $totalOpenCloseBalanceMonthly = $totalOpenCloseBalance + $totalBalanceMonthly;

        // Bank data and balances
        $totalAmountAdd = BankEntry::where('cash_type', 'add')->where('status', null)->sum('cash_amount');
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')->where('status', null)->sum('cash_amount');
        $totalFreezAmount = BankEntry::where('status', 'freez')->sum('cash_amount');
        $totalBankBalance = $totalAmountAdd - $totalAmountSubtract - $totalFreezAmount;

        $viewData = compact(
            'totalUsers', 'totalExchanges',
            'totalBalanceWeekly', 'totalDepositWeekly', 'totalWithdrawalWeekly', 'totalExpenseWeekly', 'totalBonusWeekly',
            'totalOldCustomersWeekly', 'totalOwnerProfitWeekly', 'totalCustomersWeekly', 'totalFreezAmountWeekly', 'totalMasterSettlingWeekly',
            'totalBalanceMonthly', 'totalDepositMonthly', 'totalWithdrawalMonthly', 'totalExpenseMonthly', 'totalMasterSettlingMonthly',
            'totalPaidAmountMonthly', 'totalBonusMonthly', 'totalOldCustomersMonthly', 'totalOwnerProfitMonthly', 'totalCustomersMonthly',
            'totalFreezAmountMonthly', 'totalBalanceDaily', 'totalDepositDaily', 'totalWithdrawalDaily', 'totalExpenseDaily', 'totalBonusDaily',
            'totalOldCustomersDaily', 'totalOwnerProfitDaily', 'totalCustomersDaily', 'totalBankBalance', 'totalOpenCloseBalanceDaily', 'totalPaidAmountDaily',
            'totalFreezAmountDaily'
        );

        return response()->json($viewData, 200);
    }
}
