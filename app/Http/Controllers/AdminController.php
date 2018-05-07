<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Show the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Show the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function chart($type)
    {
        switch($type){
            case "product":
                return view('admin.dashboard');
            default:
                abort(404);
                
        }
    }

    /**
     * Show the card config.
     *
     * @return \Illuminate\Http\Response
     */
    public function card()
    {
        return view('admin.card');
    }

}
