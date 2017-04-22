<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Frontend Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for resolving frontend pages.
    |
    */

    public function home()
    {
        return view('home');
    }

}
