<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UnittestController extends Controller
{
    
    private $title = "Dashboard";

    public function maps(Request $request){

        $data = [];
        $data["title"] = $this->title;
        return view("maps", $data);
    }
}
