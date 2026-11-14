<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternRequest;
use App\Http\Requests\UpdateInternRequest;
use App\Models\Intern;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InternController extends Controller
{
    /**
     * Display every intern, newest number first.
     */
    public function index(): View
    {
        return view('admin.interns.index', [
            'interns' => Intern::query()->latestNumber()->get(),
        ]);
    }

    /**
     * Show the form for adding an intern.
     */
    public function create(): View
    {
        return view('admin.interns.create', [
            'intern' => new Intern,
        ]);
    }

    /**
     * Store the intern with their photo and the next intern number of their start year.
     */
    public function store(StoreInternRequest $request): RedirectResponse
    {
        $intern = new Intern($request->safe()->except('photo'));
        $intern->photo_path = $request->file('photo')->store('interns', 'public');

        DB::transaction(function () use ($intern): void {
            $intern->assignNumber();
            $intern->save();
        });

        return to_route('admin.interns.index')
            ->with('status', "Magang \"{$intern->name}\" berhasil ditambahkan dengan nomor {$intern->number}.");
    }

    /**
     * Show the form for editing the intern.
     */
    public function edit(Intern $intern): View
    {
        return view('admin.interns.edit', [
            'intern' => $intern,
        ]);
    }

    /**
     * Update the intern, replacing their photo when a new one is uploaded; the intern number stays the same.
     */
    public function update(UpdateInternRequest $request, Intern $intern): RedirectResponse
    {
        $intern->fill($request->safe()->except('photo'));
        $replacedPhoto = null;

        if ($request->hasFile('photo')) {
            $replacedPhoto = $intern->photo_path;
            $intern->photo_path = $request->file('photo')->store('interns', 'public');
        }

        $intern->save();

        if ($replacedPhoto !== null) {
            Storage::disk('public')->delete($replacedPhoto);
        }

        return to_route('admin.interns.index')
            ->with('status', "Magang \"{$intern->name}\" berhasil diperbarui.");
    }

    /**
     * Delete the intern and their photo; their certificate stops verifying.
     */
    public function destroy(Intern $intern): RedirectResponse
    {
        $intern->delete();
        Storage::disk('public')->delete($intern->photo_path);

        return to_route('admin.interns.index')
            ->with('status', "Magang \"{$intern->name}\" berhasil dihapus.");
    }
}
