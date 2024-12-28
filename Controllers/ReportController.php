<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Exchange;
use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;


class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reports/exchange",
     *     summary="Load exchange report page",
     *     description="Returns a success message if the user is authenticated.",
     *     @OA\Response(
     *         response=200,
     *         description="Exchange report page loaded."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     )
     * )
     */
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
            return response()->json(['success' => true, 'message' => 'Exchange report page loaded.']);
        }
    }

    /**
     * @OA\Get(
     *     path="/reports/admin",
     *     summary="Load admin report page",
     *     description="Returns a list of exchange records if the user is authenticated.",
     *     @OA\Response(
     *         response=200,
     *         description="Exchange records retrieved successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
            $exchangeRecords = Exchange::all();
            return response()->json(['success' => true, 'data' => $exchangeRecords]);
        }
    }

    /**
     * @OA\Get(
     *     path="/reports/assistant",
     *     summary="Load assistant report page",
     *     description="Returns a list of exchange records if the user is authenticated.",
     *     @OA\Response(
     *         response=200,
     *         description="Exchange records retrieved successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
            $exchangeRecords = Exchange::all();
            return response()->json(['success' => true, 'data' => $exchangeRecords]);
        }
    }

    /**
     * @OA\Post(
     *     path="/reports/generate",
     *     summary="Generate a report",
     *     description="Generate a report based on date range and exchange ID.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", description="Start date of the report."),
     *             @OA\Property(property="end_date", type="string", format="date", description="End date of the report."),
     *             @OA\Property(property="exchange_id", type="integer", description="Exchange ID for the report.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Report generated successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized."
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error."
     *     )
     * )
     */
    public function report(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }else{

                $validated = $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                    'exchange_id' => 'required|exists:exchanges,id',
                ]);

                $start_date = Carbon::parse($validated['start_date'])->startOfDay();
                $end_date = Carbon::parse($validated['end_date'])->endOfDay();
                $exchangeId = $validated['exchange_id'];

                $deposit = Cash::whereBetween('created_at', [$start_date, $end_date])
                    ->where('exchange_id', $exchangeId)
                    ->where('cash_type', 'deposit')
                    ->sum('cash_amount');

                $withdrawal = Cash::whereBetween('created_at', [$start_date, $end_date])
                    ->where('exchange_id', $exchangeId)
                    ->where('approval', 1)
                    ->where('cash_type', 'withdrawal')
                    ->sum('cash_amount');

                $expense = Cash::whereBetween('created_at', [$start_date, $end_date])
                    ->where('exchange_id', $exchangeId)
                    ->where('cash_type', 'expense')
                    ->sum('cash_amount');

                $bonus = Cash::whereBetween('created_at', [$start_date, $end_date])
                    ->where('exchange_id', $exchangeId)
                    ->where('cash_type', 'deposit')
                    ->sum('bonus_amount');

                $latestBalance = $deposit - $withdrawal - $expense;
                $formattedBalance = ($latestBalance > 0) ? '+' . $latestBalance : ($latestBalance < 0 ? $latestBalance : '0');

                $response = [
                    'success' => true,
                    'data' => [
                        'deposit' => $deposit,
                        'withdrawal' => $withdrawal,
                        'expense' => $expense,
                        'bonus' => $bonus,
                        'latestBalance' => $formattedBalance,
                        'date_range' => [
                            'start' => $validated['start_date'],
                            'end' => $validated['end_date'],
                        ],
                    ]
                ];

                return response()->json($response, 200);

            }
        } 
        catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to generate report. Please try again later.'], 500);
        }
    }
}