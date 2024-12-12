<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\User;
use App\Models\Exchange;
use App\Models\BankUser;
use Illuminate\Http\Request;
use Hash;
use Auth;
class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exchangeRecords = Exchange::all();

        // Create the view response
        return view("auth.login", compact('exchangeRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required',
            'role' => 'required',
            'exchange' => 'nullable|required_if:role,exchange',
        ]);
        $userStatus = User::where('name',$request->name)->first();
        if($userStatus->status != 'active'){
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
        return response()->view('auth.login')
            ->withErrors([
                'name' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('name'));
        }
    }
    
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Auth $auth)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Auth $auth)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
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
            
            if ($user->role == "admin") {
                if (!Hash::check($request->currentPassword, $user->password)) {
                    return response()->json(['message' => 'Current password is incorrect.'], 422);
                } else {
                    $user->password = Hash::make($request->newPassword);
                    $user->save();
                    
                    // Return response with security headers
                    return response()->json(['message' => 'Password updated successfully.']);
                }
            }
        }
        return response()->json(['message' => 'You are not eligible to perform this action.'], 422);
    }
    
    public function destroy(Auth $auth)
    {
        //
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
