<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $emplayee = User::with('employee')->get()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'id_number' => $user->employee->id_number ?? null,
                'position' => $user->employee->position ?? null,
                'department' => $user->employee->department ?? null,
                'employ_status' => $user->employee->employ_status ?? null,
                'salary' => $user->employee->salary ?? null,
                'allowances' => $user->employee->allowances ?? null,
                'deductions' => $user->employee->deductions ?? null,
                'annual_leave_balance' => $user->employee->annual_leave_balance ?? null,
                'sick_leave_balance' => $user->employee->sick_leave_balance ?? null,
            ];
        });

        // return $emplayee;
        return Inertia::render('Employee/Index', [
            'users' => $emplayee,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'id_number' => 'required|string|unique:employees,id_number',
            'salary' => 'nullable|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'annual_leave_balance' => 'nullable|integer',
            'sick_leave_balance' => 'nullable|integer',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Employee::create([
            'user_id' => $user->id,
            'id_number' => $request->id_number,
            'position' => $request->position,
            'department' => $request->department,
            'employ_status' => $request->employ_status,
            'salary' => $request->salary ?? 0,
            'allowances' => $request->allowances ?? 0,
            'deductions' => $request->deductions ?? 0,
            'annual_leave_balance' => $request->annual_leave_balance ?? 0,
            'sick_leave_balance' => $request->sick_leave_balance ?? 0,
        ]);

        return redirect()->back()->with('success', 'Employee created successfully.');
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'id_number' => 'required|string|unique:employees,id_number,'.$employee->id,
            'salary' => 'nullable|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'annual_leave_balance' => 'nullable|integer',
            'sick_leave_balance' => 'nullable|integer',
        ]);

        $employee->update([
            'id_number' => $request->id_number,
            'position' => $request->position,
            'department' => $request->department,
            'employ_status' => $request->employ_status,
            'salary' => $request->salary ?? 0,
            'allowances' => $request->allowances ?? 0,
            'deductions' => $request->deductions ?? 0,
            'annual_leave_balance' => $request->annual_leave_balance ?? 0,
            'sick_leave_balance' => $request->sick_leave_balance ?? 0,
        ]);

        return redirect()->back()->with('success', 'Employee updated successfully.');
    }

   public function employeeProfile($id)
{
    $user = User::with('employee')->findOrFail($id);
    $employee = $user->employee;

    // Base user data
    $employeeData = [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'id_number' => $employee->id_number ?? null,
        'position' => $employee->position ?? null,
        'department' => $employee->department ?? null,
        'employ_status' => $employee->employ_status ?? null,
        'salary' => $employee->salary ?? null,
        'allowances' => $employee->allowances ?? null,
        'deductions' => $employee->deductions ?? null,
        'annual_leave_balance' => $employee->annual_leave_balance ?? 0,
        'sick_leave_balance' => $employee->sick_leave_balance ?? 0,
        'location' => $employee->location ?? null,
        'about' => $employee->about ?? null,
        'skills' => $employee->skills ?? null,
        'socials' => $employee->socials ?? null,
        'image' => $employee->image ?? null,
        'cover_photo' => $employee->cover_photo ?? null,
    ];

    return Inertia::render('Employee/Profile', [
        'employee' => $employeeData,
    ]);
}

}
