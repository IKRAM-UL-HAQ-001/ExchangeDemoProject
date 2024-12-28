<?php

namespace App\Http\Controllers;

use App\Models\BankBalance;
use App\Models\BankEntry;
Use App\Exports\BankBalanceListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; 

/**
 * @OA\Info(
 *     title="Bank Balance API",
 *     version="1.0.0",
 *     description="API documentation for managing bank balances."
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local Development Server"
 * )
 */
class BankBalanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/bank-balances/export",
     *     summary="Export bank balance list",
     *     description="Export the bank balance list to an Excel file.",
     *     @OA\Response(
     *         response=200,
     *         description="Bank balance exported successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function bankBalanceListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        } else {
            if (Auth::user()->role == "admin" || Auth::user()->role == "assistant") {
                $exchangeId = null;
            } else {
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new BankBalanceListExport($exchangeId), 'bankBalanceRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/bank-balances",
     *     summary="List bank balances",
     *     description="Retrieve a paginated list of bank balances for the current week.",
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
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);
            
            return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant-bank-balances",
     *     summary="List bank balances for assistant",
     *     description="Retrieve a paginated list of bank balances for the current week accessible by assistants.",
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
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);
            
            return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
        }
    }

    /**
     * @OA\Delete(
     *     path="/bank-balances/{id}",
     *     summary="Delete a bank balance entry",
     *     description="Delete a specific bank balance entry by ID.",
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
        else{
            $bankBalance = BankEntry::find($request->id);
            if ($bankBalance) {
                $bankBalance->delete();
                return response()->json(['success' => true, 'message' => 'Bank Balance deleted successfully!'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Bank Balance not found.'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant-freez-bank-balances",
     *     summary="List frozen bank balances for assistant",
     *     description="Retrieve a paginated list of frozen bank balances for the current week accessible by assistants.",
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
    public function assistantFreezBankIndex(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $bankBalanceRecords = BankEntry::with(['user', 'exchange'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);
            
            return response()->json(['success' => true, 'data' => $bankBalanceRecords], 200);
        }
    }
}