<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\EmployeePermit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function companyList()
    {
        $companys = Company::with('employees')->get();

        return Inertia::render('Company/Index', compact('companys'));
    }

    public function companysStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'license_type' => 'required|string|max:255',
            'expiry_date' => 'required|date',
            'services_provided' => 'required|string|max:255',
            'renewal_status' => 'required|string|in:active,expiring_soon,expired,renewed',
            'employee_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
            'permit_expiry' => 'required|date',
            'permit_type' => 'required|string|max:255',
        ]);

        $company = new Company;
        $company->name = $request->name;
        $company->registration_number = $request->registration_number;
        $company->license_type = $request->license_type;
        $company->expiry_date = $request->expiry_date;
        $company->services_provided = $request->services_provided;
        $company->renewal_status = $request->renewal_status;
        $company->save();

        EmployeePermit::create([
            'company_id' => $company->id,
            'employee_name' => $request->employee_name,
            'passport_number' => $request->passport_number,
            'permit_expiry' => $request->permit_expiry,
            'permit_type' => $request->permit_type,
        ]);

        return redirect()->back()->with('success', 'Company created successfully.');
    }

    public function companysUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'license_type' => 'required|string|max:255',
            'expiry_date' => 'required|date',
            'services_provided' => 'required|string|max:255',
            'renewal_status' => 'required|string|in:active,expiring_soon,expired,renewed',
            'employee_name' => 'required|string|max:255',
            'passport_number' => 'required|string|max:255',
            'permit_expiry' => 'required|date',
            'permit_type' => 'required|string|max:255',
        ]);

        $company = Company::find($id);
        $company->name = $request->name;
        $company->registration_number = $request->registration_number;
        $company->license_type = $request->license_type;
        $company->expiry_date = $request->expiry_date;
        $company->services_provided = $request->services_provided;
        $company->renewal_status = $request->renewal_status;
        $company->save();

        $employeePermit = EmployeePermit::where('company_id', $company->id)->first();
        $employeePermit->employee_name = $request->employee_name;
        $employeePermit->passport_number = $request->passport_number;
        $employeePermit->permit_expiry = $request->permit_expiry;
        $employeePermit->permit_type = $request->permit_type;
        $employeePermit->save();

        return redirect()->back()->with('success', 'Company updated successfully.');
    }

    public function companysDestroy($id)
    {
        Company::find($id)->delete();
        return redirect()->back()->with('success', 'Company deleted successfully.');
    }
}
