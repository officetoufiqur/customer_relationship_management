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

    public function icons_boxicons() {
        return Inertia::render('icons/boxicons');
    }

    public function icons_materialdesign() {
        return Inertia::render('icons/materialdesign');
    }

    public function icons_feather() {
        return Inertia::render('icons/feather');
    }

    public function icons_lineawesome() {
        return Inertia::render('icons/lineawesome');
    }

    public function icons_remix() {
        return Inertia::render('icons/remix');
    }

    public function icons_crypto() {
        return Inertia::render('icons/crypto');
    }

}
