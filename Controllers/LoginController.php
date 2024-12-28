<?php

namespace App\Http\Controllers;

use App\Models\Login;
use App\Models\User;
use App\Models\Exchange;
use App\Models\BankUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;


class LoginController extends Controller
{

    public function index()
    {
        $exchangeRecords = Exchange::all();
        return response()->json([
            'status' => 'success',
            'data' => $exchangeRecords,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     description="Authenticate a user and return an API token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="JohnDoe"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="role", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="role", type="string", example="admin"),
     *             @OA\Property(property="redirect_to", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="User not authorized by Admin.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="You are not authorized by Admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials.")
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required',
            'role' => 'required',
        ]);

        $userStatus = User::where('name', $request->name)->first();

        if ($userStatus && $userStatus->status === 'inactive') {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized by Admin',
            ], 403);
        }

        if (Auth::attempt($request->only('name', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            $response = [
                'status' => 'success',
                'message' => 'Login successful',
                'token' => $token,
                'role' => $user->role,
            ];

            switch ($user->role) {
                case 'admin':
                    $response['redirect_to'] = route('admin.dashboard');
                    break;
                case 'exchange':
                    $bankUser = BankUser::where('user_id', $user->id)->first();
                    $response['redirect_to'] = route('exchange.dashboard');
                    $response['bankUser'] = $bankUser;
                    break;
                case 'assistant':
                    $response['redirect_to'] = route('assistant.dashboard');
                    break;
                default:
                    Auth::logout();
                    return response()->json([
                        'status' => 'error',
                        'message' => 'The provided credentials do not match our records.',
                    ], 401);
            }

            return response()->json($response, 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid credentials.',
        ], 401);
    }

    /**
     * @OA\Put(
     *     path="/api/password/update",
     *     summary="Update password",
     *     description="Allows an authenticated user to update their password.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="currentPassword", type="string", example="oldpassword"),
     *             @OA\Property(property="newPassword", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Password updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or unauthorized action.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation error.")
     *         )
     *     )
     * )
     */
    public function update(Request $request)
    {
        $request->validate([
            'currentPassword' => 'required',
            'newPassword' => 'required|min:8',
        ]);

        $user = Auth::user();

        if ($user->role == "admin" || $user->role == "assistant") {
            if (!Hash::check($request->currentPassword, $user->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Current password is incorrect.',
                ], 422);
            }

            $user->password = Hash::make($request->newPassword);
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully.',
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'You are not eligible to perform this action.',
        ], 422);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     summary="User logout",
     *     description="Log out the authenticated user and invalidate the token.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Successfully logged out.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User is not logged in.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User is not logged in.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout-all",
     *     summary="Logout all users",
     *     description="Logs out all users and invalidates all sessions.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="All users logged out successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="All users have been logged out.")
     *         )
     *     )
     * )
     */
    public function logoutAll(Request $request)
    {
        Auth::logout();
        $this->invalidateAllSessions();

        return response()->json([
            'status' => 'success',
            'message' => 'All users have been logged out.',
        ]);
    }

    protected function invalidateAllSessions()
    {
        DB::table('sessions')->truncate();
    }
}
