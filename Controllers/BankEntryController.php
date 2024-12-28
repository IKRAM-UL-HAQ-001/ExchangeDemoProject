<?php

namespace App\Http\Controllers;

use App\Models\BankEntry;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class BankEntryController extends Controller
{
    /**
     * @OA\Get(
     *     path="/bank-entries",
     *     summary="List all bank entries",
     *     description="Retrieve a paginated list of bank entries for the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $user = auth()->user();
            $exchangeId = $user->exchange_id;
            $userId = $user->id;

            $bankEntryRecords = BankEntry::with('bank')
                ->where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->whereNull('status')
                ->paginate(20);

            $bankRecords = Bank::whereNull('status')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bankEntryRecords' => $bankEntryRecords,
                    'bankRecords' => $bankRecords
                ]
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/freez-bank-entries",
     *     summary="List frozen bank entries",
     *     description="Retrieve a paginated list of frozen bank entries for the authenticated user.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function freezBankIndex(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $exchangeId = Auth::user()->exchange_id;
            $userId = Auth::user()->id;

            $bankEntryRecords = BankEntry::where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->where('status', "freez")
                ->paginate(20);

            $bankRecords = Bank::whereNull('status')->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'bankEntryRecords' => $bankEntryRecords,
                    'bankRecords' => $bankRecords
                ]
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/bank-entries",
     *     summary="Create a bank entry",
     *     description="Add a new bank entry to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="account_number", type="string", example="123456789"),
     *             @OA\Property(property="cash_type", type="string", example="add"),
     *             @OA\Property(property="cash_amount", type="number", example=1000),
     *             @OA\Property(property="remarks", type="string", example="Deposit"),
     *             @OA\Property(property="status", type="string", example="freez"),
     *             @OA\Property(property="bank_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank entry created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bank Entry Data saved successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An error occurred while saving bank entry data."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $validatedData = $request->validate([
                'account_number' => [
                    Rule::requiredIf($request->input('status') !== 'freez'),
                    'string',
                    'max:255',
                ],
                'cash_type' => 'required|string|max:255',
                'cash_amount' => 'required|numeric',
                'remarks' => 'required|string',
                'status' => 'nullable|string',
                'bank_id' => 'required|numeric',
            ]);

            $bankExists = Bank::find($request->bank_id);

            if ($request->status == 'freez') {
                $bankExists->status = 'freez';
                $bankExists->save();
            }
            try {
                $user = Auth::user();

                if ($user->role == "exchange") {
                    BankEntry::create([
                        'account_number' => $validatedData['account_number'] ?? 0,
                        'bank_id' => $bankExists->id,
                        'cash_amount' => (int) $validatedData['cash_amount'],
                        'cash_type' => $validatedData['cash_type'],
                        'remarks' => $validatedData['remarks'],
                        'status' => $validatedData['status'],
                        'exchange_id' => $user->exchange_id,
                        'user_id' => $user->id,
                    ]);

                    return response()->json(['success' => true, 'message' => 'Bank Entry Data saved successfully!'], 200);
                }
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'An error occurred while saving Bank Entry Data: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * @OA\Delete(
     *     path="/bank-entries/unfreeze/{id}",
     *     summary="Unfreeze a bank entry",
     *     description="Unfreeze a bank entry and remove its frozen status.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank entry unfrozen successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bank entry unfrozen successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank entry not found."
     *     )
     * )
     */
    public function unFreeze(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $bankEntry = BankEntry::find($request->id);

            if (!$bankEntry) {
                return response()->json(['success' => false, 'message' => 'Bank entry not found.'], 404);
            }

            $bankRec = Bank::find($bankEntry->bank_id);

            if ($bankRec) {
                $bankRec->status = null;
                $bankRec->save();
            }

            $bankEntry->delete();

            return response()->json(['success' => true, 'message' => 'Bank entry unfrozen successfully!'], 200);
        }    
    }

    /**
     * @OA\Post(
     *     path="/bank-entries/balance",
     *     summary="Get bank balance",
     *     description="Retrieve the current balance of a specific bank entry.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="bank_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank balance retrieved successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="balance", type="number", example=1000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error."
     *     )
     * )
     */
    public function getBankBalance(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }else{
            $request->validate(['bank_id' => 'required']);

            $sumBalance = BankEntry::where('bank_id', $request->bank_id)
                ->selectRaw('SUM(CASE WHEN cash_type = "add" THEN cash_amount WHEN cash_type = "minus" THEN -cash_amount END) as balance')
                ->value('balance');

            return response()->json(['success' => true, 'balance' => $sumBalance ?? 0], 200);
        }
    }
}
