<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Доступ запрещен.'], 403);
        }


        $users = User::all();
        return Inertia::render('Admin/Admin', [
            'users'=>$users
        ]);
    }

    public function assignRole(Request $request)
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Доступ запрещен.'], 403);
        }

        $user = User::find($request->user_id);
        $role = $request->role;

        if ($user) {
            $user->assignRole($role);
            return response()->json(['message' => "Роль '{$role}' назначена пользователю."], 200);
        }

        return response()->json(['error' => 'Пользователь не найден.'], 404);
    }


    public function checkPermissions(Request $request)
    {
        $user = $request->user();

        if ($user->can('edit articles')) {
            return response()->json(['message' => 'Пользователь может редактировать статьи.'], 200);
        }

        return response()->json(['message' => 'Недостаточно прав.'], 403);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user() || !$request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Доступ запрещен.'], 403);
        }

        $validatedData = $request->validate([
            'title'=>'required|string',
            'description'=>'required|string',
            'priority'=>'required',
            'status' => 'required|in:new,in_progress,completed',
            'start_date'=>'required|date',
            'end_date'=>'required|date',
            'user_id'=>'required|exists:users,id',
        ]);

        $task = Task::create($validatedData);

        return response()->json(['task' => $task], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json("Deleted");
    }
}
