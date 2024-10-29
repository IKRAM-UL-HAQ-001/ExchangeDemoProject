<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\User;
use App\Models\Exchange;
use App\Models\BankUser;
use Illuminate\Http\Request;
use Auth;
class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exchangeRecords =  Exchange::all();
        return view("auth.login",compact('exchangeRecords'));
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
        return back()->withErrors([
            'name' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('name'));
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
    public function update(Request $request, Auth $auth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Auth $auth)
    {
        //
    }

    public function logout(Request $request){
        if (!auth()->check()) {
            return redirect()->route('auth.login');
        }
        elseif (auth()->check()) {
            Auth::logout();
            return redirect()->route('auth.login');
        }
    }

    public function logoutAll(Request $request)
    {
        // $users = User::all();
        $users = User::with('tokens')->get();
        foreach ($users as $user) {
            $user->tokens()->delete();
        }
        return redirect()->route("auth.login")->with('status', 'All users have been logged out.');
    }
}
