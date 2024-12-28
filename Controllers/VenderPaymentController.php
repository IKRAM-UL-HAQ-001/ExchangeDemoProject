<?php

namespace App\Http\Controllers;

use App\Models\VenderPayment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenderPaymentListExport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VenderPaymentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/vender-payments/export",
     *     summary="Export Vender Payment records to Excel",
     *     description="Download all Vender Payment records as an Excel file.",
     *     @OA\Response(
     *         response=200,
     *         description="Excel file downloaded successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function venderPaymentExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            return Excel::download(new VenderPaymentListExport(), 'venderPaymentRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/vender-payments",
     *     summary="List Vender Payment records",
     *     description="Retrieve a paginated list of Vender Payment records for the current year.",
     *     @OA\Response(
     *         response=200,
     *         description="Vender Payment records retrieved successfully."
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
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $venderPaymentRecords = VenderPayment::whereBetween('created_at', [$startOfYear, $endOfYear])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $venderPaymentRecords]);
            }
        }

    /**
     * @OA\Get(
     *     path="/assistant/vender-payments",
     *     summary="List Vender Payment records for assistant",
     *     description="Retrieve a paginated list of Vender Payment records for the current year (assistant view).",
     *     @OA\Response(
     *         response=200,
     *         description="Vender Payment records retrieved successfully."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $venderPaymentRecords = VenderPayment::whereBetween('created_at', [$startOfYear, $endOfYear])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json(['success' => true, 'data' => $venderPaymentRecords]);
        }
    }

    /**
     * @OA\Post(
     *     path="/vender-payments",
     *     summary="Store a new Vender Payment record",
     *     description="Add a new Vender Payment record to the database.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="paid_amount", type="number", description="Amount paid."),
     *             @OA\Property(property="remaining_amount", type="number", description="Remaining amount."),
     *             @OA\Property(property="payment_type", type="string", description="Type of payment."),
     *             @OA\Property(property="remarks", type="string", description="Remarks for the payment.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vender Payment record added successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $validatedData = $request->validate([
                'paid_amount' => 'required|numeric',
                'remaining_amount' => 'required|numeric',
                'payment_type' => 'required|string',
                'remarks' => 'required|string|max:255',
            ]);

            try {
                $venderPayment = VenderPayment::create([
                    'paid_amount' => $validatedData['paid_amount'],
                    'remaining_amount' => $validatedData['remaining_amount'],
                    'payment_type' => $validatedData['payment_type'],
                    'remarks' => $validatedData['remarks'],
                ]);

                return response()->json(['success' => true, 'message' => 'Vender payment added successfully!', 'data' => $venderPayment], 201);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error adding vender payment: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/vender-payments/{id}",
     *     summary="Delete a Vender Payment record",
     *     description="Remove a Vender Payment record from the database.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the Vender Payment record to delete."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vender Payment deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vender Payment not found."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $venderPayment = VenderPayment::find($request->id);

            if ($venderPayment) {
                $venderPayment->delete();
                return response()->json(['success' => true, 'message' => 'Vender payment deleted successfully!']);
            }

            return response()->json(['success' => false, 'message' => 'Vender payment not found.'], 404);
        }
    }
}