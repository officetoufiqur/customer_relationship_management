<?php

namespace App\Http\Controllers;

use App\Models\CommercialAddres;
use App\Models\Company;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $empolyes = User::with('employee')->where('id', '!=', 1)->count();
        $companys = Company::count();
        $address = CommercialAddres::count();
        // return $companys;

        return Inertia::render('dashboard/crm/index',compact('empolyes', 'companys', 'address'));
    }
}
