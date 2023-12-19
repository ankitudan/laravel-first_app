<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TodoController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = auth()->user();
    }

    /**
     * @OA\Get(
     *     path="/api/todos",
     *     summary="Get a list of Todos",
     *     tags={"Todo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response="200", description="List of Todos"),
     * )
     */
    public function index()
    {
        $todos = Todo::where('user_id', $this->user->id)->get();

        return response()->json($todos);
    }

    /**
     * @OA\Post(
     *     path="/api/todos",
     *     summary="Create a new Todo",
     *     tags={"Todo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Todo's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="201", description="Todo created successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     * )
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Todo::create([
            'name' => $validatedData['name'],
            'user_id' => $this->user->id,
        ]);

        return response()->json(['message' => 'Todo created successfully'], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/todos/{id}",
     *     summary="Get a specific Todo by ID",
     *     tags={"Todo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Todo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Todo details"),
     *     @OA\Response(response="404", description="Todo not found"),
     * )
     */
    public function show(string $id)
    {
        $todo = Todo::where('user_id', $this->user->id)->find($id);

        return response()->json($todo);
    }

    /**
     * @OA\Put(
     *     path="/api/todos/{id}",
     *     summary="Update a Todo by ID",
     *     tags={"Todo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Todo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Todo's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Todo's status",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Todo updated successfully"),
     *     @OA\Response(response="404", description="Todo not found"),
     * )
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:0,1'
        ]);

        $todo = Todo::where('user_id', $this->user->id)
            ->find($id);

        $todo->update($validatedData);

        return response()->json(['message' => 'Todo updated successfully'], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/todos/{id}",
     *     summary="Delete a Todo by ID",
     *     tags={"Todo"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the Todo",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="204", description="Todo deleted successfully"),
     *     @OA\Response(response="404", description="Todo not found"),
     * )
     */
    public function destroy(string $id)
    {
        Todo::where('user_id', $this->user->id)
            ->find($id)
            ->delete();

        return response()->json(['message' => 'Todo deleted successfully'], 201);
    }
}
