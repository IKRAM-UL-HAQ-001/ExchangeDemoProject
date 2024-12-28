<?php

namespace App\Http\Controllers;

use App\Models\DepositWithdrawal;
use App\Exports\WithdrawalListExport;
use App\Exports\DepositListExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DepositWithdrawalController extends Controller
{
    /**
     * @OA\Get(
     *     path="/deposit-withdrawal",
     *     summary="List deposit and withdrawal records for admin",
     *     description="Retrieve weekly deposit and withdrawal records accessible by admins.",
     *     @OA\Response(
     *         response=200,
     *         description="Records retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="view", type="string", example="admin.deposit_withdrawal.list"),
     *             @OA\Property(property="data", type="object")
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
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'view' => 'admin.deposit_withdrawal.list',
                'data' => compact('depositWithdrawalRecords'),
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant/deposit-withdrawal",
     *     summary="List deposit and withdrawal records for assistant",
     *     description="Retrieve weekly deposit and withdrawal records accessible by assistants.",
     *     @OA\Response(
     *         response=200,
     *         description="Records retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="view", type="string", example="assistant.deposit_withdrawal.list"),
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
            return response()->json([
                'success' => false,
                'redirect' => route('auth.login'),
                'message' => 'Unauthorized access. Redirecting to login.',
            ], 401);
        }else{

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $depositWithdrawalRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);

            return response()->json([
                'success' => true,
                'view' => 'assistant.deposit_withdrawal.list',
                'data' => compact('depositWithdrawalRecords'),
            ], 200);
        }
    }

    /**
     * @OA\Delete(
     *     path="/deposit-withdrawal/{id}",
     *     summary="Delete a deposit or withdrawal record",
     *     description="Delete a specific deposit or withdrawal record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deposit/Withdrawal deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found."
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
            return response()->json([
                'success' => false,
                'redirect' => route('auth.login'),
                'message' => 'Unauthorized access. Redirecting to login.',
            ], 401);
        }else{

            $depositWithdrawal = Cash::find($request->id);
            if ($depositWithdrawal) {
                $depositWithdrawal->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Deposit/Withdrawal deleted successfully!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Deposit/Withdrawal not found.',
            ], 404);
        }
    }
}
