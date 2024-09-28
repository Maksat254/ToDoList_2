<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
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

        return response()->json([ 'task' => $task]);
    }


    public function edit($id)
    {
        $task = Task::findOrFail($id);

        return response()->json($task);
    }
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|in:low,medium,high',
            'status' => 'required|string|in:new,in_progress,completed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $task->update($request->all());

        return response()->json(['message' => 'Задача обновлена ', 'task' => $task]);
    }



    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json([]);
    }
}
