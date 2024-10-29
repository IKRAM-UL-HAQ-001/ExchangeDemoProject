<?php

namespace App\Http\Controllers;

use App\Models\MasterSettling;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MasterSettlingMonthlyListExport;
use App\Exports\MasterSettlingWeeklyListExport;
use Carbon\Carbon;
use Auth;

class MasterSettlingController extends Controller
{
    public function masterSettlingListMonthlyExportExcel(Request $request)
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
            return Excel::download(new MasterSettlingMonthlyListExport($exchangeId), 'MonthlyMasterSettlingRecord.xlsx');
        }
    }

    public function masterSettlingListWeeklyExportExcel(Request $request)
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
            return Excel::download(new MasterSettlingWeeklyListExport($exchangeId), 'WeeklyMasterSettlingRecord.xlsx');
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();
            $masterSettlingRecords = MasterSettling::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->get();
    
            return view("admin.master_settling.list", compact('masterSettlingRecords'));
        }
    }

    public function indexAssistant()
    {

        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $startOfYear = Carbon::now()->startOfYear();
            $endOfYear = Carbon::now()->endOfYear();
            $masterSettlingRecords = MasterSettling::with(['exchange', 'user'])
                ->whereBetween('created_at', [$startOfYear, $endOfYear])
                ->get();

            return view("assistant.master_settling.list",compact('masterSettlingRecords'));
        }
    }

    public function exchangeIndex()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $exchangeId = auth()->user()->exchange_id; 
            $userId = auth()->user()->id;
            $masterSettlingRecords= MasterSettling::with(['exchange', 'user'])
                ->where('exchange_id', $exchangeId)
                ->where('user_id', $userId)
                ->get();
            return view("exchange.master_settling.list",compact('masterSettlingRecords'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        // Check if the user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'You need to be logged in to perform this action.'], 401);
        }
        else{
            $validatedData = $request->validate([
                'white_label' => 'nullable|string|max:255',
                'credit_reff' => 'nullable|string',
                'settling_point' => 'nullable|numeric',
                'price' => 'nullable|numeric',
            ]);        
            try {
                $exchangeId = auth()->user()->exchange_id;
                $userId = auth()->user()->id;
                $masterSettling = MasterSettling::create([
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
     * Display the specified resource.
     */
    public function show(MasterSettling $masterSettling)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterSettling $masterSettling)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:master_settlings,id',
            'white_label' => 'required|string',
            'credit_reff' => 'required|string',
            'settling_point' => 'required|numeric',
            'price' => 'required|numeric',
        ]);
        try{
            $masterSettling = MasterSettling::find($request->id);
            $masterSettling->white_label = $request->white_label;
            $masterSettling->credit_reff = $request->credit_reff;
            $masterSettling->settling_point = $request->settling_point;
            $masterSettling->price = $request->price;
            $masterSettling->save();
            return response()->json(['success' => true, 'message' => 'Master Settling updated successfully!']);
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
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
            $masterSettling = MasterSettling::find($request->id);
            if ($masterSettling) {
                $masterSettling->delete();
                return response()->json(['success' => true, 'message' => 'Master Settling deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Master Settling not found.'], 404);
        }
    }
}
