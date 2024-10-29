<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
Use App\Exports\CustomerListExport;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
class CustomerController extends Controller
{

    public function customerExportExcel(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            if(Auth::user()->role == "admin" || Auth::user()->role == "assistant"){
                $exchangeId = null;
            }
            else{
                $exchangeId = Auth::user()->exchange_id;
            }
            return Excel::download(new CustomerListExport($exchangeId), 'customerRecord.xlsx');
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $customerRecords = Customer::whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();
    
            return view("admin.customer.list", compact('customerRecords'));
        }
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        else{
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
    
                // Return a JSON response
                return response()->json(['message' => 'Customer added successfully!'], 201);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error adding customer: ' . $e->getMessage()], 500);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $customer = Customer::find($request->id);
            if ($customer) {
                $customer->delete();
                return response()->json(['success' => true, 'message' => 'Customer deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Customer not found.'], 404);
        }
    }

    public function exchangeIndex(){
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $user = Auth::user();
            $customerRecords = Customer::where('exchange_id', $user->exchange_id)
                ->where('user_id', $user->id)
                ->get();
            return view("exchange.customer.list",compact('customerRecords'));
        }
    }
}
