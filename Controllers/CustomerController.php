<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\CustomerListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/customers/export",
     *     summary="Export customer records to Excel",
     *     description="Generate and download an Excel file containing customer records.",
     *     @OA\Response(
     *         response=200,
     *         description="Excel file generated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error exporting Excel"
     *     )
     * )
     */
    public function customerExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{
            $exchangeId = (Auth::user()->role == "admin" || Auth::user()->role == "assistant") ? null : Auth::user()->exchange_id;
            try {
                return Excel::download(new CustomerListExport($exchangeId), 'customerRecord.xlsx');
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error exporting Excel: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/customers",
     *     summary="List customers",
     *     description="Retrieve a paginated list of customer records for the current week.",
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
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $customerRecords = Customer::whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'data' => compact('customerRecords'),
                'view' => 'admin.customer.list',
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/customers",
     *     summary="Add a new customer",
     *     description="Create a new customer record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="cash_amount", type="number"),
     *             @OA\Property(property="reference_number", type="string"),
     *             @OA\Property(property="remarks", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error adding customer"
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
                'name' => 'required|string|max:255',
                'cash_amount' => 'required|numeric',
                'reference_number' => 'required|string|max:255',
                'remarks' => 'required|string|max:255',
            ]);

            try {
                Customer::create([
                    'name' => $validatedData['name'],
                    'reference_number' => $validatedData['reference_number'],
                    'cash_amount' => $validatedData['cash_amount'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);

                return response()->json(['success' => true, 'message' => 'Customer added successfully!'], 201);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Error adding customer: ' . $e->getMessage()], 500);
            }
        }   
    }

    /**
     * @OA\Delete(
     *     path="/customers/{id}",
     *     summary="Delete a customer",
     *     description="Delete a specific customer record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{
            $customer = Customer::find($request->id);
            if ($customer) {
                $customer->delete();
                return response()->json(['success' => true, 'message' => 'Customer deleted successfully!'], 200);
            }
            return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange/customers",
     *     summary="List exchange customers",
     *     description="Retrieve a paginated list of customer records for the logged-in user's exchange.",
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
     *         description="Unauthorized"
     *     )
     * )
     */
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }else{

            $user = Auth::user();
            $customerRecords = Customer::where('exchange_id', $user->exchange_id)
                ->where('user_id', $user->id)
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => compact('customerRecords'),
                'view' => 'exchange.customer.list',
            ], 200);
        }
    }
}
