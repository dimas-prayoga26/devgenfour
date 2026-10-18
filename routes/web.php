<?php

use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectIconPreviewController;
use App\Http\Controllers\Admin\SessionController;
use App\Http\Controllers\Admin\SettingController;
use App\Models\Project;
use App\Support\ContactDetails;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

    Route::middleware('auth')->group(function (): void {
        Route::redirect('/', '/admin/projects')->name('dashboard');
        Route::resource('projects', ProjectController::class)->except('show');
        Route::get('project-icon-preview', [ProjectIconPreviewController::class, 'show'])
            ->middleware('throttle:60,1')
            ->name('project-icon-preview.show');
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::post('logout', [SessionController::class, 'destroy'])->name('logout');
    });
});
