<?php

namespace App\Http\Controllers;
use Mapper;

use Illuminate\Http\Request;

class MapController extends Controller
{
    //
    public function index()
    {
        Mapper::map(53.381128999999990000, -1.470085000000040000);

        return view('home')
    }
}
