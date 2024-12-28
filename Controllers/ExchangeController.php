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
use Illuminate\Http\Request;


class ExchangeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/exchange/dashboard",
     *     summary="Fetch exchange dashboard metrics",
     *     description="Retrieve metrics and statistics for the logged-in user's associated exchange.",
     *     @OA\Response(
     *         response=200,
     *         description="Dashboard metrics fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="exchange_name", type="string"),
     *             @OA\Property(property="userCount", type="integer"),
     *             @OA\Property(property="totalBankBalance", type="float"),
     *             @OA\Property(property="totalVendorPaymentsDaily", type="float"),
     *             @OA\Property(property="totalDepositDaily", type="float"),
     *             @OA\Property(property="totalWithdrawalDaily", type="float"),
     *             @OA\Property(property="totalExpenseDaily", type="float"),
     *             @OA\Property(property="totalFreezAmountDaily", type="float"),
     *             @OA\Property(property="totalNewCustomerDaily", type="integer")
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
            return response()->json(['message' => 'Unauthorized access. Please log in.'], 401);
        }else{

            $today = Carbon::today();
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $user = Auth::user();
            $exchangeId = $user->exchange_id;
            if (!$exchangeId) {
                return response()->json(['message' => 'No associated exchange found for the user.'], 404);
            }

            $exchange = Exchange::find($exchangeId);
            $exchange_name = $exchange ? $exchange->name : 'Unknown Exchange';

            // Metrics calculations
            $totalBankBalance = $this->calculateBankBalance($exchangeId);
            $totalVendorPaymentsDaily = VenderPayment::where('exchange_id', $exchangeId)->whereDate('created_at', $today)->sum('paid_amount');
            $totalDepositDaily = Cash::where('exchange_id', $exchangeId)->where('cash_type', 'deposit')->whereDate('created_at', $today)->sum('cash_amount');
            $totalWithdrawalDaily = Cash::where('exchange_id', $exchangeId)->where('cash_type', 'withdrawal')->where('approval', '1')->whereDate('created_at', $today)->sum('cash_amount');
            $totalExpenseDaily = Cash::where('exchange_id', $exchangeId)->where('cash_type', 'expense')->whereDate('created_at', $today)->sum('cash_amount');
            $totalFreezAmountDaily = BankEntry::where('status', 'freez')->where('exchange_id', $exchangeId)->whereDate('created_at', $today)->sum('cash_amount');
            $totalNewCustomerDaily = Customer::where('exchange_id', $exchangeId)->whereDate('created_at', $today)->distinct('id')->count('id');

            return response()->json(compact(
                'exchange_name',
                'totalBankBalance',
                'totalVendorPaymentsDaily',
                'totalDepositDaily',
                'totalWithdrawalDaily',
                'totalExpenseDaily',
                'totalFreezAmountDaily',
                'totalNewCustomerDaily'
            ), 200);
        }
    }

    /**
     * Helper method to calculate bank balance.
     */
    private function calculateBankBalance($exchangeId)
    {
        $totalAmountAdd = BankEntry::where('cash_type', 'add')->where('exchange_id', $exchangeId)->where('status', null)->sum('cash_amount');
        $totalAmountSubtract = BankEntry::where('cash_type', 'minus')->where('exchange_id', $exchangeId)->where('status', null)->sum('cash_amount');
        $totalFreezAmount = BankEntry::where('status', 'freez')->where('exchange_id', $exchangeId)->sum('cash_amount');
        return $totalAmountAdd - $totalAmountSubtract - $totalFreezAmount;
    }

    /**
     * @OA\Get(
     *     path="/exchanges",
     *     summary="List all exchanges",
     *     description="Retrieve a paginated list of all exchanges.",
     *     @OA\Response(
     *         response=200,
     *         description="Exchanges retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function exchangeList()
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized access. Please log in.'], 401);
        }else{

            $exchangeRecords = Exchange::orderBy('created_at', 'desc')->paginate(20);
            return response()->json(['data' => compact('exchangeRecords')], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/exchanges",
     *     summary="Create a new exchange",
     *     description="Add a new exchange to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="New Exchange")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exchange created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Exchange added successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized access. Please log in.'], 401);
        }else{

            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            Exchange::create(['name' => $request->name]);

            return response()->json(['message' => 'Exchange added successfully!'], 200);
        }
    }

    /**
     * @OA\Delete(
     *     path="/exchanges/{id}",
     *     summary="Delete an exchange",
     *     description="Delete a specific exchange by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Exchange deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Exchange deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Exchange not found."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized access. Please log in.'], 401);
        }else{

            $exchange = Exchange::find($request->id);
            if ($exchange) {
                $exchange->delete();
                return response()->json(['success' => true, 'message' => 'Exchange deleted successfully!'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Exchange not found.'], 404);
    
        }
    }
}
