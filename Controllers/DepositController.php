<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use App\Models\Cash;
use App\Models\Bank;
use App\Models\ExcelFile;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DepositListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DepositController extends Controller
{
    /**
     * Export deposits to Excel.
     *
     * @OA\Post(
     *     path="/deposits/export",
     *     summary="Export deposits to Excel",
     *     description="Exports deposit records within the specified date range to an Excel file.",
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-01-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-01-07")
     *         )
     *     ),
     *     @OA\Response(response=200, description="File downloaded successfully."),
     *     @OA\Response(response=401, description="Unauthorized access."),
     *     @OA\Response(response=500, description="Error exporting Excel.")
     * )
     */
    public function depositExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $exchangeId = (Auth::user()->role == "admin" || Auth::user()->role == "assistant") ? null : Auth::user()->exchange_id;
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        try {
            return Excel::download(new DepositListExport($exchangeId, $startDate, $endDate), 'depositRecord.xlsx');
        } catch (\Exception $e) {
            \Log::error("Error exporting deposits to Excel: {$e->getMessage()}");
            return response()->json(['success' => false, 'message' => 'Error exporting Excel: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Fetch deposit records for the current week.
     *
     * @OA\Get(
     *     path="/deposits",
     *     summary="Fetch deposit records",
     *     description="Retrieve deposit records for the current week.",
     *     @OA\Response(response=200, description="Successful response."),
     *     @OA\Response(response=401, description="Unauthorized access."),
     *     @OA\Response(response=500, description="Error fetching deposits.")
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

                $query = Cash::with(['exchange', 'user']);
                if (Auth::user()->role !== 'admin') {
                    $query->where('exchange_id', Auth::user()->exchange_id)
                        ->where('user_id', Auth::user()->id);
                }
                $depositRecords = $query->whereBetween('created_at', [$startOfWeek, $endOfWeek])->paginate(20);

                $bankRecords = Bank::select('id', 'name')->get();
                $excelData = ExcelFile::select('id', 'customer_name', 'customer_phone')->get();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'depositRecords' => $depositRecords,
                        'bankRecords' => $bankRecords,
                        'excelData' => $excelData,
                    ],
                    'view' => 'exchange.deposit.list',
                ], 200);
            } catch (\Exception $e) {
                \Log::error("Error fetching deposits: {$e->getMessage()}");
                return response()->json(['success' => false, 'message' => 'Error fetching deposits: ' . $e->getMessage()], 500);
            }
        }
    }
}
