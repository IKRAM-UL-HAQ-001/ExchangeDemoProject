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
            'exchange' => 'nullable|required_if:role,exchange',
        ]);
        $userStatus = User::where('name',$request->name)->first();
        
        if ($userStatus && $userStatus->status === 'inactive') {
            return redirect()->back()->withErrors(['error' => 'You are not authorized by Admin']);
        }
        else{
        if (Auth::attempt($request->only('name', 'password'))) {
            
            $request->session()->regenerate();
    
            $user = Auth::user();
    
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'exchange':
                    $bankUser = BankUser::where('user_id', $user->id)->first();
                    session(['bankUser' => $bankUser]);
                    return redirect()->route('exchange.dashboard');
                case 'assistant':
                    return redirect()->route('assistant.dashboard');
                default:
                    Auth::logout(); 
                    return back()->withErrors([
                        'name' => 'The provided credentials do not match our records.',
                    ]);
            }
        }
        return redirect()->route('auth.login')
            ->withErrors([
                'error' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('name'));
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
            return redirect()->route('auth.login');
        } elseif (auth()->check()) {
            Auth::logout();
            return redirect()->route('auth.login');
        }
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
