<?php

namespace App\Http\Controllers;

use App\Models\Intern;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InternCertificateDownloadController extends Controller
{
    /**
     * Download the certificate the admin uploaded for the intern.
     */
    public function __invoke(Intern $intern): StreamedResponse
    {
        abort_unless($intern->hasCertificate(), 404);

        return Storage::disk('public')->download($intern->certificate_path, $intern->certificateDownloadName());
    }
}
