<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Models\Submission\Submission;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex()
    {
        return view('admin.index', [
            'submissionCount' => Submission::where('status', 'Pending')->count()
        ]);
    }
}
