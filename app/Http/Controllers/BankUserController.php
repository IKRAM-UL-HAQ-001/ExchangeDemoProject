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
            $userRecords = User::whereNotIn('role', ['admin', 'assistant'])
            ->orderBy('created_at', 'desc')->get();
            $response = view("admin.bank_user.list", compact('bankUserRecords', 'userRecords'));
            return response($response)
            ->header('X-Frame-Options', 'DENY') // Prevents framing
            ->header('Content-Security-Policy', "frame-ancestors 'self'") // Allows framing only from the same origin
            ->header('Referrer-Policy', 'no-referrer');
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
            
            return response()->json(['message' => 'Bank User added successfully!'], 201)
                ->withHeaders([
                    'X-Frame-Options' => 'DENY', // Prevents framing
                    'Content-Security-Policy' => "frame-ancestors 'self'", // Allows framing only from the same origin
                ]);
        }
    }

    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            // Find the BankUser record by ID
            $bankUser = BankUser::find($request->id);
            if ($bankUser) {
                $bankUser->delete();
                return response()->json(['success' => true, 'message' => 'Bank User deleted successfully!'])
                    ->withHeaders([
                        'X-Frame-Options' => 'DENY', // Prevents framing
                        'Content-Security-Policy' => "frame-ancestors 'self'", // Allows framing only from the same origin
                    ]);
            }
            return response()->json(['success' => false, 'message' => 'Bank User not found.'], 404);
        }
    }

}
