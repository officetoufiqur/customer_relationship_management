<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class VelzonRoutesController extends Controller
{
    //

    public function dashboard()
    {
        return Inertia::render('dashboard/crm/index');
    }

    public function dashboard_analytics()
    {
        return Inertia::render('dashboard/analytics/index');
    }

    public function dashboard_crm()
    {
        return Inertia::render('dashboard/crm/index');
    }

    public function dashboard_crypto()
    {
        return Inertia::render('dashboard/crypto/index');
    }
    public function dashboard_job()
    {
        return Inertia::render('dashboard/job/index');
    }
    public function dashboard_nft()
    {
        return Inertia::render('dashboard/nft/index');
    }
    public function dashboard_projects()
    {
        return Inertia::render('dashboard/projects/index');
    }

    public function dashboard_blog()
    {
        return Inertia::render('dashboard/blog/index');
    }

}
