<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
Use App\Exports\CustomerListExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
class CustomerController extends Controller
{

    public function customerExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $exchangeId = (Auth::user()->role == "admin" || Auth::user()->role == "assistant") ? null : Auth::user()->exchange_id;

        // Generate the Excel download response with security headers
        return Excel::download(new CustomerListExport($exchangeId), 'customerRecord.xlsx');
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $customerRecords = Customer::whereBetween('created_at', [$startOfWeek, $endOfWeek])
        ->orderBy('created_at', 'desc')->paginate(20);

        return view("admin.customer.list", compact('customerRecords'));
    }


    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

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
            // Create a new customer entry
            $customer = Customer::create([
                'name' => $validatedData['name'],
                'reference_number' => $validatedData['reference_number'],
                'cash_amount' => $validatedData['cash_amount'],
                'remarks' => $validatedData['remarks'],
                'exchange_id' => $exchangeId,
                'user_id' => $userId,
            ]);
            return response()->json(['message' => 'Customer added successfully!'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error adding customer: ' . $e->getMessage()], 500);
        }
    }


    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
    
        $customer = Customer::find($request->id);
        if ($customer) {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
        }
    
        return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
    }
    
    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }

        $user = Auth::user();
        $customerRecords = Customer::where('exchange_id', $user->exchange_id)
            ->where('user_id', $user->id)
            ->paginate(20);

        return view("exchange.customer.list", compact('customerRecords'));
    }

}
