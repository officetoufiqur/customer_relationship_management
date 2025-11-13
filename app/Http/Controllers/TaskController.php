<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function tasksList()
    {
        $tasks = Task::with('taskUsers.assignedTo')->get();
        $users = User::select('id', 'name', 'profile_photo_path')->get();

        // return $tasks;
        return Inertia::render('Tasks/Index', compact('tasks', 'users'));
    }

    public function tasksStore(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'assigned_to' => 'required|array',
            'deadline' => 'required|date',
            'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePaths = [];

        if ($request->hasFile('attachment')) {
            foreach ($request->file('attachment') as $file) {
                $filePaths[] = FileUpload::storeFile($file, '/uploads/tasks');
            }
        }

        $filePath = implode(',', $filePaths);

        $task = new Task;
        $task->assigned_by = Auth::user()->id;
        $task->title = $request->title;
        $task->description = $request->description;
        $task->deadline = $request->deadline;
        $task->attachment = $filePath;
        $task->save();

        foreach ($request->assigned_to as $userId) {
            TaskUser::create([
                'task_id' => $task->id,
                'assigned_to' => $userId,
            ]);
        }

        return redirect()->back()->with('success', 'Task created successfully.');
    }
}
