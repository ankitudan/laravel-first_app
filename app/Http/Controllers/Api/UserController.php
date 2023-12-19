<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * @OA\Get(
     *     path="/api/my-details",
     *     tags={"User"},
     *     summary="Get logged-in user details",
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function myDetails(Request $request)
    {
        return response()->json($this->user);
    }

    /**
     * @OA\Get(
     *     path="/api/get-users",
     *     summary="Get users details",
     *     tags={"User"},
     *     @OA\Response(response="200", description="Success"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getUsers()
    {
        $user = User::get();
        return response()->json(['user' => $user], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/update-user",
     *     summary="Update user profile",
     *     tags={"User"},
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="User's name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="User's email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="file",
     *                     description="User's profile image",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     description="User's password",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="User profile updated successfully"),
     *     @OA\Response(response="404", description="User not found"),
     *     @OA\Response(response="401", description="Unauthorized"),
     * )
     */
    public function update(Request $request)
    {

        $id = $this->user->id;
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'image' => 'nullable|mimes:png,jpeg,jpg|max:2048',
        ]);

        $user = User::find($id);
        if ($request->hasFile('image')) {
            $fileName = time() . '.' . $request->image->extension();
            $request->image->storeAs('public/images', $fileName);
            $validatedData['image'] = $fileName;
        }

        $user->update($validatedData);

        return response()->json(['message' => 'User profile updated successfully']);
    }
}
