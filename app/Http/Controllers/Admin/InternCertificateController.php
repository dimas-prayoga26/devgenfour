<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Support\QrCode;
use Illuminate\Contracts\View\View;

class InternCertificateController extends Controller
{
    /**
     * Show the printable certificate, whose QR code opens the intern's verification page.
     */
    public function __invoke(Intern $intern): View
    {
        return view('admin.interns.certificate', [
            'intern' => $intern,
            'qrCode' => QrCode::svg($intern->verificationUrl()),
        ]);
    }
}
