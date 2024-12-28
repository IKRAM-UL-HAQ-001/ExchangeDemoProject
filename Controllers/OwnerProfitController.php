<?php

namespace App\Http\Controllers;

use App\Models\OwnerProfit;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OwnerProfitListExport;
use Illuminate\Support\Facades\Auth;


class OwnerProfitController extends Controller
{
    /**
     * @OA\Get(
     *     path="/owner-profits/export",
     *     summary="Export Owner Profit records to Excel",
     *     description="Exports the list of Owner Profit records as an Excel file.",
     *     tags={"Owner Profit"},
     *     @OA\Response(
     *         response=200,
     *         description="Excel file generated successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function ownerProfitListExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $exchangeId = (Auth::user()->role == "admin" || Auth::user()->role == "assistant")
                ? null
                : Auth::user()->exchange_id;

            return Excel::download(new OwnerProfitListExport($exchangeId), 'ownerProfitRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/owner-profits",
     *     summary="List Owner Profit records",
     *     description="Retrieve a paginated list of Owner Profit records for the admin view.",
     *     tags={"Owner Profit"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items())
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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $ownerProfitRecords = OwnerProfit::whereBetween('created_at', [$startOfYear, $endOfYear])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $ownerProfitRecords]);
        }
    }

    /**
     * @OA\Post(
     *     path="/owner-profits",
     *     summary="Add a new Owner Profit record",
     *     description="Create a new Owner Profit record.",
     *     tags={"Owner Profit"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="cash_amount", type="number", description="The amount of cash."),
     *             @OA\Property(property="remarks", type="string", description="Remarks for the record.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Owner Profit added successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Transaction successfully added!")
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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{
            $user = Auth::user();
            $exchangeId = $user->exchange_id;
            $userId = $user->id;

            $validatedData = $request->validate([
                'cash_amount' => 'required|numeric',
                'remarks' => 'required|string|max:255',
            ]);

            try {
                OwnerProfit::create([
                    'cash_amount' => $validatedData['cash_amount'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);

                return response()->json(['success' => true, 'message' => 'Transaction successfully added!'], 201);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/owner-profits/{id}",
     *     summary="Delete an Owner Profit record",
     *     description="Delete a specific Owner Profit record by ID.",
     *     tags={"Owner Profit"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Owner Profit deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Owner Profit deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Owner Profit not found."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $ownerProfit = OwnerProfit::find($request->id);

            if ($ownerProfit) {
                $ownerProfit->delete();
                return response()->json(['success' => true, 'message' => 'Owner Profit deleted successfully!'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Owner Profit not found.'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange-owner-profits",
     *     summary="List Owner Profit records for exchange",
     *     description="Retrieve a paginated list of Owner Profit records for the logged-in user's exchange.",
     *     tags={"Owner Profit"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $user = Auth::user();

            $ownerProfitRecords = OwnerProfit::where('exchange_id', $user->exchange_id)
                ->where('user_id', $user->id)
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $ownerProfitRecords]);
        }
    }
}
