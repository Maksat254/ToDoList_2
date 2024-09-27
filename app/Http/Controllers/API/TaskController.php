<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function Termwind\render;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();

        return response()->json($tasks);
    }


    public function create()
    {
        $users = User::all();
        return response()->json($users);

    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'priority' => 'required',
            'status' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
        ]);

        $task = Task::create($validatedData);

        return response()->json(['message' => 'Task added successfully!', 'task' => $task], 201);
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);

        return response()->json($task);
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in-progress,completed',
            'end_date' => 'required|date',
        ]);

        $task = Task::findOrFail($id);

        $task->update($validatedData);

        return response()->json(['massage'=> 'Задача обновлена']);
    }


//    public function showTaskForUser()
//    {
//        $user = auth()->user();
//        $task = Task::where('user_id',$user->id)->get();
//        $notifications = $user->notifications;
//
//        return inertia('Task/AddTask');
//    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json("Deleted");
    }
}
