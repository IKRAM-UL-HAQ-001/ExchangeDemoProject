<?php

namespace App\Http\Controllers;

use App\Models\Withdrawal;
use App\Models\Cash;
use App\Models\Bank;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\WithdrawalListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    /**
     * @OA\Post(
     *     path="/withdrawals/export",
     *     summary="Export Withdrawal records to Excel",
     *     description="Export withdrawal records based on the provided date range to an Excel file.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", example="2023-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2023-12-31")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="File exported successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error exporting data."
     *     )
     * )
     */
    public function withdrawalExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }

            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            try {
                return Excel::download(new WithdrawalListExport($exchangeId, $startDate, $endDate), 'withdrawalRecord.xlsx');
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error exporting data: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/withdrawals",
     *     summary="Display Withdrawal records for the current week",
     *     description="Retrieve withdrawal records for the authenticated user's exchange for the current week.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="withdrawalRecords", type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(property="bankRecords", type="array",
     *                 @OA\Items(type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error fetching data."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            try {
                $startOfWeek = Carbon::now()->startOfWeek();
                $endOfWeek = Carbon::now()->endOfWeek();
                $exchangeId = auth()->user()->exchange_id;
                $userId = auth()->user()->id;

                $withdrawalRecords = Cash::with(['exchange', 'user'])
                    ->where('exchange_id', $exchangeId)
                    ->where('user_id', $userId)
                    ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->paginate(20);

                $bankRecords = Bank::all();

                return response()->json(['success' => true, 'withdrawalRecords' => $withdrawalRecords, 'bankRecords' => $bankRecords]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()], 500);
            }
        }
    }
}
