<?php

namespace App\Http\Controllers;

use App\Helpers\FileUpload;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::with('user')->get();

        // return $leaves;

        return Inertia::render('Leave/Index', [
            'leaves' => $leaves,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,emergency',
            'reason' => 'required|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_medical' => 'nullable|boolean',
            'medical_excuse_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filePath = FileUpload::storeFile($request->file('medical_excuse_file'), 'uploads/leave');

        $employee = Auth::user();

        Leave::create([
            'user_id' => $employee->id,
            'leave_type' => $validated['leave_type'],
            'reason' => $validated['reason'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_medical' => $validated['is_medical'] ?? false,
            'medical_excuse_file' => $filePath,
        ]);

        return redirect()->back()->with('success', 'Leave request submitted successfully.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'leave_type' => 'required|in:annual,sick,emergency',
            'reason' => 'required|string|max:500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_medical' => 'nullable|boolean',
            'medical_excuse_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $leave = Leave::findOrFail($id);

        if ($request->hasFile('medical_excuse_file')) {
            if ($leave->medical_excuse_file) {
                unlink(public_path($leave->medical_excuse_file));
            }
            $imagePath = FileUpload::storeFile($request->file('medical_excuse_file'), 'uploads/leave');
            $validated['medical_excuse_file'] = $imagePath;
        }

        $leave->update($validated);

        return redirect()->back()->with('success', 'Leave request updated successfully.');
    }


    public function updateStatus(Request $request, $id)
    {
        $leave = Leave::findOrFail($id);

        $leave->status = $request->status;
        $leave->save();

        return redirect()->back()->with('success', 'Leave status updated successfully.');
    }
}
