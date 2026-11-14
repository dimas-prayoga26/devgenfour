<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use Illuminate\Contracts\View\View;

class InternVerificationController extends Controller
{
    /**
     * Show the details of the intern a scanned certificate belongs to.
     *
     * An unknown UUID shows the same page with a "not found" message (see the route's missing handler).
     */
    public function __invoke(Intern $intern): View
    {
        return view('interns.verify', [
            'intern' => $intern,
        ]);
    }
}
