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
        } else {
            $bankUserRecords = BankUser::all();
            $userRecords = User::whereNotIn('role', ['admin', 'assistant'])
                ->orderBy('created_at', 'desc')->get();
            $response = view("admin.bank_user.list", compact('bankUserRecords', 'userRecords'));
            return response($response);
        }
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            // Validate the incoming request
            $request->validate([
                'bank_user' => 'required|string|max:255',
            ]);

            // Create a new BankUser record
            BankUser::create([
                'user_id' => $request->bank_user,
            ]);

            return response()->json(['message' => 'Bank User added successfully!'], 201);
        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $bankUser = BankUser::find($request->id);
            if ($bankUser) {
                $bankUser->delete();
                return response()->json(['success' => true, 'message' => 'Bank User deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'Bank User not found.'], 404);
        }
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found']);
        }

        $user->name = $request->name;
        $user->type = $request->type;
        $user->exchange_id = $request->exchange;


        $user->save();

        return response()->json(['success' => true, 'message' => 'User updated successfully']);
    }
}
