<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
$role = Auth::user()->Role;


if($role == "Member"){

      return view('Main');

}
elseif($role == "Admin"){

      return view('index');

}
elseif($role == "Committee"){

      return redirect()->route('comt');

}




    }
}
