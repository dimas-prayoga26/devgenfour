<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\InternController;
use App\Http\Controllers\Admin\InternQrCodeController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectIconPreviewController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\InternCertificateDownloadController;
use App\Http\Controllers\InternVerificationController;
use App\Models\Project;
use App\Support\ContactDetails;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::get('/', function (Request $request): View {
    $locale = $request->query('lang', $request->session()->get('locale', 'id'));

    if (! in_array($locale, ['id', 'en'], true)) {
        $locale = 'id';
    }

    app()->setLocale($locale);
    $request->session()->put('locale', $locale);

    return view('home', [
        'projects' => Project::query()->published()->ordered()->with('tags')->get(),
        'contact' => ContactDetails::fromSettings(),
    ]);
})->name('home');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('login', [SessionController::class, 'create'])->name('login');
        Route::post('login', [SessionController::class, 'store'])->middleware('throttle:admin-login')->name('login.store');
    });

    Route::middleware(['auth', RoleMiddleware::using(UserRole::Admin)])->group(function (): void {
        Route::redirect('/', '/admin/projects')->name('dashboard');
        Route::resource('projects', ProjectController::class)->except('show');
        Route::get('project-icon-preview', [ProjectIconPreviewController::class, 'show'])
            ->middleware('throttle:60,1')
            ->name('project-icon-preview.show');
        Route::resource('interns', InternController::class)->except('show');
        Route::get('interns/{intern}/qr-code', InternQrCodeController::class)->name('interns.qr-code');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    Route::post('logout', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');
});

Route::get('verify/{intern}', InternVerificationController::class)
    ->missing(fn (): Response => response()->view('interns.verify', ['intern' => null], 404))
    ->name('interns.verify');
Route::get('verify/{intern}/certificate', InternCertificateDownloadController::class)->name('interns.certificate.download');
