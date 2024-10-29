<?php

namespace App\Http\Controllers;

use App\Models\BankUser;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class BankUserController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $bankUserRecords = BankUser::all();
            $userRecords = User::whereNotIn('role', ['admin', 'assistant'])->get();
            return view("admin.bank_user.list",compact('bankUserRecords','userRecords'));
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $request->validate([
                'bank_user' => 'required|string|max:255',
            ]);

            BankUser::create([
                'user_id' => $request->bank_user,
            ]);
            return response()->json(['message' => 'Bank User added successfully!'], 201);
        }
    }

    public function show(BankUser $bankUser)
    {
        //
    }

    public function edit(BankUser $bankUser)
    {
        //
    }

    public function update(Request $request, BankUser $bankUser)
    {
        //
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $bankUser = BankUser::find($request->id);
            if ($bankUser) {
                $bankUser->delete();
                return response()->json(['success' => true, 'message' => 'Bank User deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Bank User not found.'], 404);
        }
    }
}
