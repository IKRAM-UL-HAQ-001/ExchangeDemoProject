<?php

namespace App\Http\Controllers;

use App\Models\MasterSettling;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterSettlingMonthlyListExport;
use App\Exports\MasterSettlingWeeklyListExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MasterSettlingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/master-settling/monthly/export",
     *     summary="Export Master Settling Monthly Records to Excel",
     *     description="Download an Excel file containing monthly master settling records.",
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
    public function masterSettlingListMonthlyExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }else{

            $exchangeId = (Auth::user()->role === "admin" || Auth::user()->role === "assistant") ? null : Auth::user()->exchange_id;
            
            return Excel::download(new MasterSettlingMonthlyListExport($exchangeId), 'MonthlyMasterSettlingRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/master-settling/weekly/export",
     *     summary="Export Master Settling Weekly Records to Excel",
     *     description="Download an Excel file containing weekly master settling records.",
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
    public function masterSettlingListWeeklyExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }else{

            $exchangeId = (Auth::user()->role === "admin" || Auth::user()->role === "assistant") ? null : Auth::user()->exchange_id;
            
            return Excel::download(new MasterSettlingWeeklyListExport($exchangeId), 'WeeklyMasterSettlingRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/master-settling",
     *     summary="List Master Settling Records for Admin",
     *     description="Retrieve a paginated list of master settling records for admin users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response."
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
            return redirect()->route('auth.login');
        }else{

            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $masterSettlingRecords = MasterSettling::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.master_settling.list', compact('masterSettlingRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant/master-settling",
     *     summary="List Master Settling Records for Assistant",
     *     description="Retrieve a paginated list of master settling records for assistant users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response."
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
            return redirect()->route('auth.login');
        }else{

            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();

            $masterSettlingRecords = MasterSettling::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->paginate(20);

            return view('assistant.master_settling.list', compact('masterSettlingRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange/master-settling",
     *     summary="List Master Settling Records for Exchange Users",
     *     description="Retrieve a paginated list of master settling records for exchange users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response."
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
            return redirect()->route('auth.login');
        }else{

            $exchangeId = auth()->user()->exchange_id;
            $userId = auth()->user()->id;

            $masterSettlingRecords = MasterSettling::with(['exchange', 'user'])
                ->where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->paginate(20);

            return view('exchange.master_settling.list', compact('masterSettlingRecords'));
        }
    }
    /**
     * @OA\Post(
     *     path="/master-settling",
     *     summary="Create a new Master Settling Record",
     *     description="Store a new master settling record in the database.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"white_label", "credit_reff", "settling_point", "price"},
     *             @OA\Property(property="white_label", type="string"),
     *             @OA\Property(property="credit_reff", type="string"),
     *             @OA\Property(property="settling_point", type="number"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Master Settling saved successfully."
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
            return response()->json(['error' => 'You need to be logged in to perform this action.'], 401);
        }else{

            $validatedData = $request->validate([
                'white_label' => 'nullable|string|max:255',
                'credit_reff' => 'nullable|string',
                'settling_point' => 'nullable|numeric',
                'price' => 'nullable|numeric',
            ]);

            try {
                $exchangeId = auth()->user()->exchange_id;
                $userId = auth()->user()->id;

                MasterSettling::create([
                    'white_label' => $validatedData['white_label'],
                    'credit_reff' => $validatedData['credit_reff'],
                    'settling_point' => $validatedData['settling_point'],
                    'price' => $validatedData['price'],
                    'exchange_id' => $exchangeId,
                    'user_id' => $userId,
                ]);

                return response()->json(['success' => true, 'message' => 'Master Settling saved successfully!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Put(
     *     path="/master-settling/{id}",
     *     summary="Update a Master Settling Record",
     *     description="Update an existing master settling record in the database.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"white_label", "credit_reff", "settling_point", "price"},
     *             @OA\Property(property="white_label", type="string"),
     *             @OA\Property(property="credit_reff", type="string"),
     *             @OA\Property(property="settling_point", type="number"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Master Settling updated successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Master Settling not found."
     *     )
     * )
     */
    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $validatedData = $request->validate([
                'id' => 'required|exists:master_settlings,id',
                'white_label' => 'required|string',
                'credit_reff' => 'required|string',
                'settling_point' => 'required|numeric',
                'price' => 'required|numeric',
            ]);

            try {
                $masterSettling = MasterSettling::find($validatedData['id']);
                $masterSettling->update($validatedData);

                return response()->json(['success' => true, 'message' => 'Master Settling updated successfully!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/master-settling/{id}",
     *     summary="Delete a Master Settling Record",
     *     description="Delete a specific master settling record by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Master Settling deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Master Settling not found."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }else{

            $validatedData = $request->validate([
                'id' => 'required|exists:master_settlings,id',
            ]);

            try {
                $masterSettling = MasterSettling::find($validatedData['id']);
                $masterSettling->delete();

                return response()->json(['success' => true, 'message' => 'Master Settling deleted successfully!']);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
            }
        }
    }
}
