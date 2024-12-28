<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\User;
use App\Models\Exchange;
use App\Models\BankUser;
use Illuminate\Http\Request;
use Hash;
use Illuminate\Support\Facades\Auth;
use DB;
class LoginController extends Controller
{
    public function index()
    {
        $exchangeRecords = Exchange::all();
        return view("auth.login", compact('exchangeRecords'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required',
            'role' => 'required',
        ]);

        // Check if the user exists and is active
        $userStatus = User::where('name', $request->name)->first();

        if ($userStatus && $userStatus->status === 'inactive') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized by Admin',
            ], 403);
        }

        if (Auth::attempt($request->only('name', 'password'))) {
            $user = Auth::user();
            
            $token = $user->createToken('api_token');
            // dd($user->createToken('api_token'));
            switch ($user->role) {
                case 'admin':
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Login successful',
                        'token' => $token,
                        'role' => 'admin',
                        'redirect_to' => route('admin.dashboard'),
                    ]);
                case 'exchange':
                    $bankUser = BankUser::where('user_id', $user->id)->first();
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Login successful',
                        'token' => $token,
                        'role' => 'exchange',
                        'redirect_to' => route('exchange.dashboard'),
                        'bankUser' => $bankUser,
                    ]);
                case 'assistant':
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Login successful',
                        'token' => $token,
                        'role' => 'assistant',
                        'redirect_to' => route('assistant.dashboard'),
                    ]);
                default:
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The provided credentials do not match our records.',
                    ], 401);
            }
        }
    }
    
    public function update(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        } else {
            $request->validate([
                'currentPassword' => 'required',
                'newPassword' => 'required|min:8',
            ]);
            
            $user = Auth::user();
            if ($user->role == "admin" ||$user->role == "assistant") {
                if (!Hash::check($request->currentPassword, $user->password)) {
                    return response()->json(['message' => 'Current password is incorrect.'], 422);
                } else {
                    $user->password = Hash::make($request->newPassword);
                    $user->save();
                    return response()->json(['message' => 'Password updated successfully.']);
                }
            }
        }
        return response()->json(['message' => 'You are not eligible to perform this action.'], 422);
    }
    
    public function logout(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'User is not logged in.',
            ], 400); // Bad request if not authenticated
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.',
        ], 200);
    }


    public function logoutAll(Request $request)
    {
        $admin = Auth::user();
        Auth::logout();
        $this->invalidateAllSessions();
        
        return redirect()->route('auth.login')->with('status', 'All users have been logged out.');
    }
    
    protected function invalidateAllSessions()
    {
        \DB::table('sessions')->truncate();
    }

}
