<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Employee;
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

        $totalTasks = Task::count();
        $pendingTasks = Task::where('status', 'pending')->count();
        $completedTasks = Task::where('status', 'completed')->count();
        // return $totalTasks;
        return Inertia::render('Tasks/Index', compact('tasks', 'users', 'totalTasks', 'pendingTasks', 'completedTasks'));
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

    public function tasksUpdate(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'assigned_to' => 'required|array',
            'deadline' => 'required|date',
            'attachment.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $task = Task::find($id);
        $task->title = $request->title;
        $task->description = $request->description;
        $task->deadline = $request->deadline;

        // remove old files and upload new files
        if ($request->hasFile('attachment')) {
            if ($task->attachment) {
                $oldFiles = explode(',', $task->attachment);
                foreach ($oldFiles as $file) {
                    if (file_exists(public_path($file))) {
                        @unlink(public_path($file));
                    }
                }
            }

            $filePaths = [];
            foreach ($request->file('attachment') as $file) {
                $filePaths[] = FileUpload::storeFile($file, '/uploads/tasks');
            }
            $task->attachment = implode(',', $filePaths);
        }

        $task->save();

        TaskUser::where('task_id', $task->id)->delete();

        foreach ($request->assigned_to as $userId) {
            TaskUser::create([
                'task_id' => $task->id,
                'assigned_to' => $userId,
            ]);
        }

        return redirect()->back()->with('success', 'Task updated successfully.');
    }

    public function tasksView($id)
    {
        $task = Task::with('taskUsers.assignedTo')->find($id);

        $cleanTask = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'deadline' => $task->deadline,
            'status' => $task->status,

            // attachment -> array
            'attachments' => collect(explode(',', $task->attachment))->map(function ($path) {
                $bytes = file_exists(public_path($path)) ? filesize(public_path($path)) : 0;

                if ($bytes >= 1024 * 1024) {
                    $size = round($bytes / 1024 / 1024, 2).' MB';
                } elseif ($bytes >= 1024) {
                    $size = round($bytes / 1024, 2).' KB';
                } else {
                    $size = $bytes.' B';
                }

                return [
                    'path' => $path,
                    'size' => $size,
                ];
            }),

            // users clean
            'users' => $task->taskUsers->map(function ($user) {
                $employee = Employee::where('user_id', $user->assignedTo->id)->select('id', 'position', 'image')->first();

                return [
                    'id' => $user->assignedTo->id,
                    'name' => $user->assignedTo->name,
                    'profile_photo_path' => $user->assignedTo->profile_photo_path,
                    'employee' => $employee,
                ];
            }),
        ];

        // return $cleanTask;

        return Inertia::render('Tasks/View', [
            'task' => $cleanTask,
        ]);
    }

    public function reassign(Request $request, $id)
    {
        $task = TaskUser::where('task_id', $id)
            ->where('assigned_to', $request->user_id)
            ->first();

        if (! $task) {
            return back()->with('error', 'Original task not found.');
        }

        if ($request->optionData === 'delay') {
            $task->delay_reason = $request->delay_reason;
            $task->save();
        }

        if ($request->optionData === 'reassign') {
            $task->delete();

            foreach ($request->transferred_to as $userId) {
                TaskUser::create([
                    'task_id' => $request->task_id,
                    'assigned_to' => $userId,
                    'delay_reason' => null,
                    'transferred_note' => $request->transferred_note,
                    'status' => 'pending',
                    'transferred_to' => $request->user_id,
                ]);
            }
        }

        return back()->with('success', 'Task updated successfully.');
    }

    public function tasksDestroy($id)
    {
        $task = Task::find($id);
        // remove old files
        if ($task->attachment) {
            $oldFiles = explode(',', $task->attachment);
            foreach ($oldFiles as $file) {
                if (file_exists(public_path($file))) {
                    @unlink(public_path($file));
                }
            }
        }
        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully.');
    }
}
