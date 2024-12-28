<?php

namespace App\Http\Controllers;

use App\Models\OpenCloseBalance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OpenCloseBalanceListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OpenCloseBalanceController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/open-close-balance/export",
     *     summary="Export Open/Close Balance to Excel",
     *     description="Exports the Open/Close Balance records to an Excel file.",
     *     tags={"Open/Close Balance"},
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
    public function openCloseBalanceExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $exchangeId = (Auth::user()->role === "admin" || Auth::user()->role === "assistant")
                ? null
                : Auth::user()->exchange_id;

            return Excel::download(new OpenCloseBalanceListExport($exchangeId), 'openingClosingBalanceRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/open-close-balance",
     *     summary="List Open/Close Balance records (admin view)",
     *     description="Retrieve a paginated list of Open/Close Balance records for admin users.",
     *     tags={"Open/Close Balance"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
            $openingClosingBalanceRecords = OpenCloseBalance::where('created_at', '>=', $startOfWeek)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.open_close_balance.list', compact('openingClosingBalanceRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/api/exchange/open-close-balance",
     *     summary="List Open/Close Balance records (exchange view)",
     *     description="Retrieve a paginated list of Open/Close Balance records for the logged-in user's exchange.",
     *     tags={"Open/Close Balance"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
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
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $exchangeId = Auth::user()->exchange_id;

            $openingClosingBalanceRecords = OpenCloseBalance::where('exchange_id', $exchangeId)
                ->where('created_at', '>=', $startOfWeek)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('exchange.open_close_balance.list', compact('openingClosingBalanceRecords'));
        }
    }

    /**
     * @OA\Post(
     *     path="/api/open-close-balance",
     *     summary="Add a new Open/Close Balance record",
     *     description="Store a new Open/Close Balance record.",
     *     tags={"Open/Close Balance"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="open_balance", type="number", example=1000),
     *             @OA\Property(property="remarks", type="string", example="Opening balance for the week.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record saved successfully."
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
            return response()->json(['message' => 'Unauthorized'], 401);
        } else {
            $validatedData = $request->validate([
                'open_balance' => 'required|numeric',
                'remarks' => 'nullable|string|max:255',
            ]);

            try {
                $user = Auth::user();
                OpenCloseBalance::create([
                    'open_balance' => $validatedData['open_balance'],
                    'remarks' => $validatedData['remarks'],
                    'exchange_id' => $user->exchange_id,
                    'user_id' => $user->id,
                ]);

                return response()->json(['success' => true, 'message' => 'Record saved successfully!'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/open-close-balance/{id}",
     *     summary="Delete an Open/Close Balance record",
     *     description="Delete a specific Open/Close Balance record by its ID.",
     *     tags={"Open/Close Balance"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $validatedData = $request->validate([
                'id' => 'required|exists:open_close_balances,id',
            ]);

            try {
                $openCloseBalance = OpenCloseBalance::find($validatedData['id']);
                $openCloseBalance->delete();

                return response()->json(['success' => true, 'message' => 'Deleted successfully!'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => true, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }
}
