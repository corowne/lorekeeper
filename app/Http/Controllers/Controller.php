<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\Support\Facades\View;
use App\Models\Character\Sublist;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Creates a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        View::share('navsublists', Sublist::orderBy('sort', 'DESC')->get());
    }
}
