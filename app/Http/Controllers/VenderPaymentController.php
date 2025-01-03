<?php

namespace App\Http\Controllers;

use App\Models\VenderPayment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenderPaymentListExport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * @OA\Info(
 *     title="Exchange API",
 *     version="1.0.0",
 *     description="API documentation for ExchangeProject",
 *     termsOfService="http://example.com/terms/",
 *     contact={
 *         "email": "support@example.com"
 *     },
 *     license={
 *         "name": "MIT",
 *         "url": "https://opensource.org/licenses/MIT"
 *     }
 * )
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local Development Server"
 * )
 */
class VenderPaymentController extends Controller
{
    /**
     * @OA\Post(
     *     path="/vender-payment/export",
     *     summary="Export vender payment records to Excel",
     *     description="Export a list of vender payment records.",
     *     @OA\Response(
     *         response=200,
     *         description="Vender payment records exported successfully.",
     *         @OA\MediaType(
     *             mediaType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
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
    public function venderPaymentExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
            return Excel::download(new VenderPaymentListExport(), 'venderPaymentRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/vender-payment",
     *     summary="List vender payment records for the year",
     *     description="Retrieve paginated vender payment records for the current year.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
     *     path="/vender-payment/assistant",
     *     summary="List vender payment records for the year (Assistant)",
     *     description="Retrieve paginated vender payment records for the current year, accessible by assistants.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
     *     path="/vender-payment",
     *     summary="Store a new vender payment",
     *     description="Store a new vender payment record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="paid_amount", type="number", example=500),
     *             @OA\Property(property="remaining_amount", type="number", example=1000),
     *             @OA\Property(property="payment_type", type="string", example="Credit"),
     *             @OA\Property(property="remarks", type="string", example="Payment for services")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Vender payment added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
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
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
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
     *     path="/vender-payment",
     *     summary="Delete a vender payment record",
     *     description="Delete a vender payment record by its ID.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Vender payment deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Vender payment not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        } else {
            $venderPayment = VenderPayment::find($request->id);

            if ($venderPayment) {
                $venderPayment->delete();
                return response()->json(['success' => true, 'message' => 'Vender payment deleted successfully!']);
            }

            return response()->json(['success' => false, 'message' => 'Vender payment not found.'], 404);
        }
    }
}
