<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $userRecords = User::with('exchange')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();
            $exchangeRecords = Exchange::all();
            return view("admin.user.list", compact('userRecords', 'exchangeRecords'));
        }
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $request->validate([
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'exchange' => 'required|exists:exchanges,id',
            ]);
            User::create([
                'name' => $request->name,
                'password' => Hash::make($request->password), 
                'exchange_id' => $request->exchange,
                'role' => "exchange",
            ]);
            $exchangeName = Exchange::find($request->exchange)->name;
            return response()->json([
                'message' => 'User added successfully!',
                'exchange_name' => $exchangeName,
            ], 201);
        }
    }
    public function userStatus(Request $request)
    {
        // dd($request->userId);
        $user = User::find($request->userId);

        $user->status = $request->status;
        $user->save();

        return redirect()->back();
    }
    
    public function update(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{
            $user = User::findOrFail($request->id);    
            $request->validate([
                'name' => 'required|string|max:255',
                'exchange' => 'nullable|exists:exchanges,id',
                'password' => 'nullable|string|min:8', // Password is optional
            ]);
            $user->name = $request->name;
            $user->exchange_id = $request->exchange;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password); // Hash the new password
            }
            $user->save();    
            return response()->json(['message' => 'User updated successfully.', 'exchange_name' => $user->exchange->name]);
        } 
    }
    
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        else{    
            $user = User::find($request->id);
            if ($user) {
                $user->delete();
                return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
            }
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }
    }
}
