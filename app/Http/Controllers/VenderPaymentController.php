<?php

namespace App\Http\Controllers;

use App\Models\VenderPayment;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VenderPaymentListExport;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
class VenderPaymentController extends Controller
{

    public function venderPaymentExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            return Excel::download(new VenderPaymentListExport(), 'venderPaymentRecord.xlsx');
        }
    }
    
    public function index()
    {
        $startOfYear = Carbon::now()->startOfYear(); 
        $endOfYear = Carbon::now()->endOfYear();
        $venderPaymentRecords = VenderPayment::whereBetween('created_at', [$startOfYear, $endOfYear])
        ->orderBy('created_at', 'desc')->paginate(20);
        return response()->view('admin.vender_payment.list', compact('venderPaymentRecords'));
    }
    public function assistantIndex()
    {
        $startOfYear = Carbon::now()->startOfYear(); 
        $endOfYear = Carbon::now()->endOfYear();
        $venderPaymentRecords = VenderPayment::whereBetween('created_at', [$startOfYear, $endOfYear])
        ->orderBy('created_at', 'desc')->paginate(20);
        return response()->view('assistant.vender_payment.list', compact('venderPaymentRecords'));
    }
    
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
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
                return response()->json(['message' => 'Vender Payment added successfully!', 'data' => $venderPayment], 201);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error adding vender payment: ' . $e->getMessage()], 500);
            }
        }
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
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
