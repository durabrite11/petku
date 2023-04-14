<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $title = "Dashboard";

    public function index(Request $request){

        $data = [];
        $data["title"] = $this->title;
        return view("admin.dashboard.index", $data);
    }
}
