<?php

namespace App\Http\Controllers;

use App\Models\BankBalance;
use App\Models\BankEntry;
use App\Exports\BankBalanceListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Bank Balance",
 *     description="Operations related to bank balances."
 * )
 */
class BankBalanceController extends Controller
{
   /**
 * @OA\Get(
 *     path="/bank-balances/export",
 *     summary="Export Bank Balance List",
 *     description="Exports the bank balance list as an Excel file.",
 *     tags={"Bank Balance"},
 *     @OA\Response(
 *         response=200,
 *         description="File downloaded successfully."
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized access."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */

    public function bankBalanceListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $exchangeId = Auth::user()->role === "admin" || Auth::user()->role === "assistant"
            ? null
            : Auth::user()->exchange_id;

        return Excel::download(new BankBalanceListExport($exchangeId), 'bankBalanceRecord.xlsx');
    }

    /**
     * @OA\Get(
     *     path="/bank-balances",
     *     summary="List bank balances",
     *     description="Retrieve a paginated list of bank balances for the current week.",
     *     tags={"Bank Balance"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
    }

    /**
     * @OA\Get(
     *     path="/assistant-bank-balances",
     *     summary="List bank balances for assistant",
     *     description="Retrieve a paginated list of bank balances for the current week accessible by assistants.",
     *     tags={"Bank Balance"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
    }

    /**
     * @OA\Delete(
     *     path="/bank-balances/{id}",
     *     summary="Delete a bank balance entry",
     *     description="Delete a specific bank balance entry by ID.",
     *     tags={"Bank Balance"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank balance deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bank Balance deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank balance not found."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $bankBalance = BankEntry::find($request->id);
        if ($bankBalance) {
            $bankBalance->delete();
            return response()->json(['success' => true, 'message' => 'Bank Balance deleted successfully!'], 200);
        }

        return response()->json(['success' => false, 'message' => 'Bank Balance not found.'], 404);
    }
}
