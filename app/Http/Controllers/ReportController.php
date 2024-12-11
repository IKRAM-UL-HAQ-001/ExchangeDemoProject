<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Exchange;
use App\Models\Cash;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
class ReportController extends Controller
{
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            return response()->view("exchange.report.list")->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            $exchangeRecords = Exchange::all();
            return response()->view('admin.report.list', compact('exchangeRecords'))->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
    }

    public function report(Request $request)
    {
        try {
            // Ensure the user is authenticated
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401)->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            }

            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'exchange_id' => 'required|exists:exchanges,id',
            ]);

            $start_date = Carbon::parse($validated['start_date'])->startOfDay();
            $end_date = Carbon::parse($validated['end_date'])->endOfDay();
            $exchangeId = $validated['exchange_id'];

            // Calculate sums
            $deposit = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit')
                ->sum('cash_amount');

            $withdrawal = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'withdrawal')
                ->sum('cash_amount');

            $expense = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'expense')
                ->sum('cash_amount');

            $bonus = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit') // Confirm if 'deposit' is correct for bonuses
                ->sum('bonus_amount');

            // Calculate latest balance
            $latestBalance = $deposit - $withdrawal - $expense;
            $latestBalance = $deposit - $withdrawal - $expense;

            if ($latestBalance > 0) {
                $formattedBalance = '+' . $latestBalance;
            } elseif ($latestBalance < 0) {
                $formattedBalance = $latestBalance; // Negative sign is automatically added
            } else {
                $formattedBalance = '0'; // For zero balance
            }

            $response = [
                'deposit' => $deposit,
                'withdrawal' => $withdrawal,
                'expense' => $expense,
                'bonus' => $bonus,
                'latestBalance' => $formattedBalance,
                'date_range' => [
                    'start' => $validated['start_date'],
                    'end' => $validated['end_date'],
                ],
            ];
            return response()->json($response, 200)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to generate report. Please try again later.'], 500)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
            ]);
        }
    }

    public function exchangeReport(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401)->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            }

            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'exchange_id' => 'required|exists:exchanges,id',
            ]);

            $start_date = Carbon::parse($validated['start_date'])->startOfDay();
            $end_date = Carbon::parse($validated['end_date'])->endOfDay();
            $exchangeId = $validated['exchange_id'];

            // Calculate sums
            $deposit = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit')
                ->sum('cash_amount');

            $withdrawal = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'withdrawal')
                ->sum('cash_amount');

            $expense = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'expense')
                ->sum('cash_amount');

            $bonus = Cash::whereBetween('created_at', [$start_date, $end_date])
                ->where('exchange_id', $exchangeId)
                ->where('cash_type', 'deposit') // Confirm if 'deposit' is correct for bonuses
                ->sum('bonus_amount');

            // Calculate latest balance
            $latestBalance = $deposit - $withdrawal - $expense;

            // Prepare response data
            $response = [
                'deposit' => $deposit,
                'withdrawal' => $withdrawal,
                'expense' => $expense,
                'bonus' => $bonus,
                'latestBalance' => $latestBalance,
                'date_range' => [
                    'start' => $validated['start_date'],
                    'end' => $validated['end_date'],
                ],
            ];

            return response()->json($response, 200)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } catch (\Exception $e) {
            
            // Return a generic error message
            return response()->json(['error' => 'Failed to generate report. Please try again later.'], 500)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
    }  
}
