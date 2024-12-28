<?php

namespace App\Http\Controllers;

use App\Models\Cash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ExcelFile;

class CashController extends Controller
{
    /**
     * @OA\Get(
     *     path="/cash",
     *     summary="List cash records",
     *     description="Retrieve paginated cash records for the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="view", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $user = Auth::user();
            $userId = $user->id;
            $exchangeId = $user->exchange_id;

            $cashRecords = Cash::where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => compact('cashRecords'),
                'view' => 'exchange.cash.list',
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/cash",
     *     summary="Store a new cash record",
     *     description="Create a new cash record with validation.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="reference_number", type="string", nullable=true),
     *             @OA\Property(property="customer_name", type="string", nullable=true),
     *             @OA\Property(property="cash_amount", type="number", nullable=true),
     *             @OA\Property(property="customer_phone", type="number", nullable=true),
     *             @OA\Property(property="cash_type", type="string", example="deposit"),
     *             @OA\Property(property="bonus_amount", type="number", nullable=true),
     *             @OA\Property(property="payment_type", type="string", nullable=true),
     *             @OA\Property(property="remarks", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction successfully added",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $validatedData = $request->validate([
                'reference_number' => 'nullable|string|max:255|unique:cashes,reference_number',
                'customer_name' => 'nullable|string|max:255|required_if:cash_type,deposit',
                'cash_amount' => 'nullable|numeric',
                'customer_phone' => 'nullable|numeric',
                'cash_type' => 'required|in:deposit,withdrawal,expense',
                'bonus_amount' => 'nullable|numeric|required_if:cash_type,deposit',
                'payment_type' => 'nullable|string|required_if:cash_type,deposit,withdrawal',
                'remarks' => 'nullable|string|max:255',
            ]);

            try {
                $user = Auth::user();
                if (!empty($validatedData['customer_phone'])) {
                    $normalizedPhone = preg_replace('/[^0-9]/', '', $validatedData['customer_phone']);
                    $existInExcel = ExcelFile::where('customer_phone', $normalizedPhone)->exists();

                    if (!$existInExcel) {
                        ExcelFile::insert([
                            'customer_name' => $validatedData['customer_name'],
                            'customer_phone' => $normalizedPhone,
                            'exchange_id' => Auth::user()->exchange_id,
                            'created_at' => now(),
                        ]);
                    }
                }

                Cash::create([
                    'reference_number' => $validatedData['reference_number'] ?? null,
                    'customer_name' => $validatedData['customer_name'] ?? null,
                    'cash_amount' => $validatedData['cash_amount'] ?? null,
                    'customer_phone' => $validatedData['customer_phone'] ?? null,
                    'cash_type' => $validatedData['cash_type'] ?? null,
                    'bonus_amount' => $validatedData['bonus_amount'] ?? 0,
                    'payment_type' => $validatedData['payment_type'] ?? null,
                    'remarks' => $validatedData['remarks'] ?? null,
                    'user_id' => $user->id,
                    'exchange_id' => $user->exchange_id,
                ]);

                return response()->json(['success' => true, 'message' => 'Transaction successfully added!'], 200);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Something went wrong: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Put(
     *     path="/cash/approval",
     *     summary="Update approval status",
     *     description="Update the approval status of a cash record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="approval", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Approval status updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Entry not found"
     *     )
     * )
     */
    public function approval(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{

            $approval = $request->approval;
            $id = $request->id;
            $cash = Cash::find($id);

            if ($cash) {
                $cash->approval = $approval;
                $cash->update();
                return response()->json(['success' => true, 'message' => 'Approval status updated successfully.'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Entry not found.'], 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/cash/{id}",
     *     summary="Delete a cash record",
     *     description="Delete a specific cash record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cash deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cash not found"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $cash = Cash::find($request->id);
            if ($cash) {
                $cash->delete();
                return response()->json(['success' => true, 'message' => 'Cash deleted successfully!'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Cash not found.'], 404);
        }
    }
}
