<?php

namespace App\Http\Controllers;

use App\Models\VenderPayment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenderPaymentListExport;
use Auth;
use Carbon\Carbon;
class VenderPaymentController extends Controller
{

    public function venderPaymentExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            return Excel::download(new VenderPaymentListExport(), 'venderPaymentRecord.xlsx');
        }
    }
    
    public function index()
    {
        $startOfYear = Carbon::now()->startOfYear(); 
        $endOfYear = Carbon::now()->endOfYear();
        $venderPaymentRecords = VenderPayment::whereBetween('created_at', [$startOfYear, $endOfYear])
        ->orderBy('created_at', 'desc')->get();
        return response()->view('admin.vender_payment.list', compact('venderPaymentRecords'))->withHeaders([
            'X-Frame-Options' => 'DENY', // Prevents framing
            // 'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
        ]);
    }
    
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            $validatedData = $request->validate([
                'paid_amount' => 'required|numeric',
                'remaining_amount' => 'required|numeric',
                'payment_type' => 'required|string',
                'remarks' => 'required|string|max:255',
            ]);
    
            try {
                // Create a new customer entry
                $venderPayment = VenderPayment::create([
                    'paid_amount' => $validatedData['paid_amount'],
                    'remaining_amount' => $validatedData['remaining_amount'],
                    'payment_type' => $validatedData['payment_type'],
                    'remarks' => $validatedData['remarks'],
                ]);
    
                // Return a JSON response
                return response()->json(['message' => 'Vender Payment added successfully!', 'data' => $venderPayment], 201)->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error adding vender payment: ' . $e->getMessage()], 500)->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            }
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login')->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        } else {
            $venderPayment = VenderPayment::find($request->id);
            if ($venderPayment) {
                $venderPayment->delete();
                return response()->json(['success' => true, 'message' => 'Vender payment deleted successfully!'])->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Vender payment not found.'], 404)->withHeaders([
                'X-Frame-Options' => 'DENY', // Prevents framing
                'Content-Security-Policy' => "default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:;"
            ]);
        }
    }    
}
