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
     * The uploaded files of an intern, mapped to the attribute holding each file's path.
     */
    private const FILES = [
        'photo' => 'photo_path',
        'certificate' => 'certificate_path',
    ];

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
     * Store the intern with their files and the next intern number of their start year.
     */
    public function store(StoreInternRequest $request): RedirectResponse
    {
        $intern = new Intern($request->safe()->except(array_keys(self::FILES)));
        $this->storeUploadedFiles($request, $intern);

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
     * Update the intern, replacing the files that were uploaded again; the intern number stays the same.
     */
    public function update(UpdateInternRequest $request, Intern $intern): RedirectResponse
    {
        $intern->fill($request->safe()->except(array_keys(self::FILES)));
        $replacedFiles = $this->storeUploadedFiles($request, $intern);
        $intern->save();

        Storage::disk('public')->delete($replacedFiles);

        return to_route('admin.interns.index')
            ->with('status', "Magang \"{$intern->name}\" berhasil diperbarui.");
    }

    /**
     * Delete the intern with their photo and certificate; their QR code stops verifying.
     */
    public function destroy(Intern $intern): RedirectResponse
    {
        $intern->delete();
        Storage::disk('public')->delete(array_filter([$intern->photo_path, $intern->certificate_path]));

        return to_route('admin.interns.index')
            ->with('status', "Magang \"{$intern->name}\" berhasil dihapus.");
    }

    /**
     * Store each uploaded file on the intern, returning the paths of the files they replace.
     *
     * @return list<string>
     */
    private function storeUploadedFiles(StoreInternRequest $request, Intern $intern): array
    {
        $replacedFiles = [];

        foreach (self::FILES as $input => $attribute) {
            if (! $request->hasFile($input)) {
                continue;
            }

            $replacedFiles[] = $intern->getAttribute($attribute);
            $intern->setAttribute($attribute, $request->file($input)->store('interns', 'public'));
        }

        return array_values(array_filter($replacedFiles));
    }
}
