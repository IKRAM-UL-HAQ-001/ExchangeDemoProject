<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/users",
     *     summary="List all users",
     *     description="Retrieve a paginated list of users excluding admin users.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $userRecords = User::with('exchange')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            $exchangeRecords = Exchange::all();
            return response()->json([
                'data' => compact('userRecords', 'exchangeRecords'),
            ],200);
        }
    }

    /**
     * @OA\Get(
     *     path="/assistant-users",
     *     summary="List all users for assistants",
     *     description="Retrieve a paginated list of users excluding admin users, accessible by assistants.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $userRecords = User::with('exchange')
            ->where('role', '!=', 'admin')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            $exchangeRecords = Exchange::all();
            return response()->json([
                'data' => compact('userRecords', 'exchangeRecords'),
            ],200);
        }
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     description="Add a new user to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="type", type="string", example="exchange"),
     *             @OA\Property(property="password", type="string", example="password123"),
     *             @OA\Property(property="exchange", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User added successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User added successfully!"),
     *             @OA\Property(property="exchange_name", type="string", example="Exchange Name")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'password' => 'required|string|min:8',
                'exchange' => 'required|exists:exchanges,id',
            ]);
            User::create([
                'name' => $request->name,
                'type' => $request->type,
                'password' => Hash::make($request->password), 
                'exchange_id' => $request->exchange,
                'status' => 'inactive',
                'role' => "exchange",
            ]);
            $exchangeName = Exchange::find($request->exchange)->name;
            return response()->json([
                'message' => 'User added successfully!',
                'exchange_name' => $exchangeName,
            ], 200);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/status",
     *     summary="Update user status",
     *     description="Update the status of a specific user.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="userId", type="integer", example=1),
     *             @OA\Property(property="status", type="string", example="active")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Redirecting back to the previous page.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function userStatus(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $user = User::find($request->userId);
            $user->status = $request->status;
            $user->save();
            return response()->json([
                'message' => 'Redirecting back to the previous page.',
            ], 200);
        }
    }

    /**
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a user",
     *     description="Update the details of a user.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="type", type="string", example="regular"),
     *             @OA\Property(property="exchange", type="integer", example=123),
     *             @OA\Property(property="password", type="string", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function update(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $user = User::findOrFail($request->id);    
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'exchange' => 'nullable|exists:exchanges,id',
                'password' => 'nullable|string|min:8',
            ]);
            $user->name = $request->name;
            $user->type = $request->type;
            $user->exchange_id = $request->exchange;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();    
            return response()->json([
                'message' => 'User updated successfully.',
            ], 200);
        } 
    }

    /**
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a user",
     *     description="Delete a specific user by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized access."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'message' => 'Unauthorized access. Please log in.',
            ], 401);
        } else {
            $user = User::find($request->id);
            if ($user) {
                $user->delete();
                return response()->json([
                    'success' => true,
                     'message' => 'User deleted successfully!'
                    ],200);
            }
            return response()->json([
                'success' => false, 
                'message' => 'User not found.'
            ], 404);
        }
    }
}
