<?php

namespace App\Http\Controllers;

use App\Models\BankUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankUserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/bank-users",
     *     summary="List all bank users",
     *     description="Retrieve a paginated list of bank users and associated user records.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function index()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $bankUserRecords = BankUser::paginate(20);
        $userRecords = User::whereNotIn('role', ['admin', 'assistant'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'bankUserRecords' => $bankUserRecords,
                'userRecords' => $userRecords
            ]
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/assistant-bank-users",
     *     summary="List all bank users for assistant",
     *     description="Retrieve a paginated list of bank users and associated user records for assistants.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function assistantIndex()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $bankUserRecords = BankUser::paginate(20);
        $userRecords = User::whereNotIn('role', ['admin', 'assistant'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'bankUserRecords' => $bankUserRecords,
                'userRecords' => $userRecords
            ]
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/bank-users",
     *     summary="Create a new bank user",
     *     description="Add a new bank user to the system.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="bank_user", type="string", example="12345")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Bank user created successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bank User added successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $request->validate([
            'bank_user' => 'required|string|max:255',
        ]);

        BankUser::create([
            'user_id' => $request->bank_user,
        ]);

        return response()->json(['success' => true, 'message' => 'Bank User added successfully!'], 201);
    }

    /**
     * @OA\Delete(
     *     path="/bank-users/{id}",
     *     summary="Delete a bank user",
     *     description="Remove a bank user from the system.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bank user deleted successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Bank User deleted successfully!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Bank user not found."
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated."
     *     )
     * )
     */
    public function destroy(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
        }

        $bankUser = BankUser::find($request->id);
        if ($bankUser) {
            $bankUser->delete();
            return response()->json(['success' => true, 'message' => 'Bank User deleted successfully!'], 200);
        }
        return response()->json(['success' => false, 'message' => 'Bank User not found.'], 404);
    }

    /**
     * @OA\Put(
     *     path="/bank-users/{id}",
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
     *             @OA\Property(property="exchange", type="integer", example=123)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User updated successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found."
     *     )
     * )
     */
    public function update(Request $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        }

        $user->name = $request->name;
        $user->type = $request->type;
        $user->exchange_id = $request->exchange;

        $user->save();

        return response()->json(['success' => true, 'message' => 'User updated successfully.'], 200);
    }
}