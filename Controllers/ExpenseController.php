<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Cash;
use App\Exports\ExpenseListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/expenses/export",
     *     summary="Export expenses to Excel",
     *     description="Export all expenses to an Excel file.",
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
    public function expenseExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }else{
            $exchangeId = (Auth::user()->role === "admin" || Auth::user()->role === "assistant")
                ? null
                : Auth::user()->exchange_id;
            return Excel::download(new ExpenseListExport($exchangeId), 'expenseRecord.xlsx');
        }
    }

    /**
     * @OA\Get(
     *     path="/expenses",
     *     summary="List all expenses for admin",
     *     description="Retrieve a paginated list of expenses for admin users.",
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

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $expenseRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return view('admin.expense.list', compact('expenseRecords'));
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant/expenses",
     *     summary="List all expenses for assistant",
     *     description="Retrieve a paginated list of expenses for assistant users.",
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

            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $expenseRecords = Cash::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->paginate(20);

            return view('assistant.expense.list', compact('expenseRecords'));
        }
    }

    /**
     * @OA\Delete(
     *     path="/expenses/{id}",
     *     summary="Delete an expense record",
     *     description="Delete a specific expense record by its ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Expense deleted successfully."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Expense not found."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }else{

            $validated = $request->validate([
                'id' => 'required|integer|exists:cash,id',
            ]);

            $expense = Cash::find($validated['id']);
            if ($expense) {
                $expense->delete();
                return response()->json(['success' => true, 'message' => 'Expense deleted successfully!']);
            }

            return response()->json(['success' => false, 'message' => 'Expense not found.'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/exchange/expenses",
     *     summary="List all expenses for exchange users",
     *     description="Retrieve a paginated list of expenses for exchange users.",
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

            $expenseRecords = Cash::with(['exchange', 'user'])
                ->where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->where('cash_type', 'expense')
                ->paginate(20);

            return view('exchange.expense.list', compact('expenseRecords'));
        }
    }
}