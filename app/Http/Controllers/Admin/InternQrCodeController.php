<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Intern;
use App\Support\QrCode;
use Illuminate\Http\Response;

class InternQrCodeController extends Controller
{
    /**
     * Download the QR code that opens the intern's verification page, to place on their certificate design.
     */
    public function __invoke(Intern $intern): Response
    {
        return response(QrCode::svg($intern->verificationUrl()), headers: [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"QR-{$intern->number}.svg\"",
        ]);
    }
}
