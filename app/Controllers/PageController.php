<?php

namespace App\Controllers;

class PageController extends BaseController
{
    public function dashboardEmploye()
    {
        return view('pages/dashboard');
    }

    public function dashboardAdmin()
    {
        return view('pages/dasboard_admin');
    }

    public function dashboardRh()
    {
        return view('pages/liste_rh');
    }
}
