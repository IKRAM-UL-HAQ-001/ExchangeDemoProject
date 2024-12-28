<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankEntry;
use Illuminate\Http\Request;
use App\Exports\BankListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 *     name="Bank",
 *     description="Operations related to bank records."
 * )
 */
class BankController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/banks/export",
     *     summary="Export Bank Records to Excel",
     *     description="Generate and download an Excel file containing bank records.",
     *     @OA\Response(
     *         response=200,
     *         description="Excel file generated successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function bankExportExcel()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            return Excel::download(new BankListExport, 'BankList.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/api/banks",
     *     summary="List all banks",
     *     description="Retrieve a paginated list of all bank records.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
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
            return redirect()->route('auth.login');
        } else {
            $bankRecords = Bank::orderBy('created_at', 'desc')->paginate(20);
            return view("admin.bank.list", compact('bankRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/api/assistant/banks",
     *     summary="List all banks for assistants",
     *     description="Retrieve a paginated list of all bank records accessible by assistants.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bankRecords = Bank::orderBy('created_at', 'desc')->paginate(20);
            return view("assistant.bank.list", compact('bankRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/api/banks/freeze",
     *     summary="List frozen bank records",
     *     description="Retrieve a paginated list of frozen bank records.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function freezBankIndex(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bankEntryRecords = BankEntry::where('status', "freez")
                ->paginate(20);
            $bankRecords = Bank::all();

            return view('admin.bank_freez.list', compact('bankEntryRecords', 'bankRecords'));
        }
    }

    /**
     * @OA\Post(
     *     path="/api/banks",
     *     summary="Add a new bank",
     *     description="Create a new bank record.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bank added successfully"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            Bank::create([
                'name' => $request->name
            ]);
            return response()->json(['message' => 'Bank added successfully!'], 201);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/banks/{id}",
     *     summary="Delete a bank",
     *     description="Delete a specific bank record by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bank = Bank::find($request->id);
            if ($bank) {
                $bank->delete();
                return response()->json(['success' => true, 'message' => 'Bank deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Bank not found.'], 404);
        }
    }
}