<?php

namespace App\Http\Controllers\Admin;

use App\Actions\PickProjectIcon;
use App\Actions\SyncProjectTags;
use App\Actions\TranslateProject;
use App\Exceptions\TranslationFailed;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\Tag;
use App\Services\DeepLTranslator;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display every project, including hidden ones.
     */
    public function index(): View
    {
        return view('admin.projects.index', [
            'projects' => Project::query()->ordered()->with('tags')->get(),
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(DeepLTranslator $translator): View
    {
        return view('admin.projects.create', [
            'project' => new Project(['is_published' => true]),
            'canTranslate' => $translator->isConfigured(),
            'tagSuggestions' => Tag::suggestions(),
        ]);
    }

    /**
     * Store a newly created project with its image, tags, automatic English copy, and automatic icon.
     */
    public function store(StoreProjectRequest $request, TranslateProject $translateProject, PickProjectIcon $pickProjectIcon, SyncProjectTags $syncProjectTags): RedirectResponse
    {
        $project = new Project($request->safe()->except(['image', 'tags']));
        $project->sort_order = (int) Project::query()->max('sort_order') + 1;
        $project->image_path = $request->file('image')->store('projects', 'public');

        $failures = [$this->attempt(fn () => $translateProject->handle($project))];
        $pickProjectIcon->handle($project);
        $project->save();
        $failures[] = $this->attempt(fn () => $syncProjectTags->handle($project, $request->validated('tags')));

        return to_route('admin.projects.index')
            ->with('status', "Portofolio \"{$project->name}\" berhasil ditambahkan.")
            ->with('warning', $this->translationWarning($failures));
    }

    /**
     * Show the form for editing the project.
     */
    public function edit(Project $project, DeepLTranslator $translator): View
    {
        return view('admin.projects.edit', [
            'project' => $project,
            'canTranslate' => $translator->isConfigured(),
            'tagSuggestions' => Tag::suggestions(),
        ]);
    }

    /**
     * Update the project, replacing its image when a new one is uploaded and refreshing changed copy, tags, and icon.
     */
    public function update(UpdateProjectRequest $request, Project $project, TranslateProject $translateProject, PickProjectIcon $pickProjectIcon, SyncProjectTags $syncProjectTags): RedirectResponse
    {
        $project->fill($request->safe()->except(['image', 'tags']));
        $replacedImage = null;

        if ($request->hasFile('image')) {
            $replacedImage = $project->image_path;
            $project->image_path = $request->file('image')->store('projects', 'public');
        }

        $failures = [$this->attempt(fn () => $translateProject->handle($project))];
        $pickProjectIcon->handle($project);
        $project->save();
        $failures[] = $this->attempt(fn () => $syncProjectTags->handle($project, $request->validated('tags')));

        if ($replacedImage !== null) {
            Storage::disk('public')->delete($replacedImage);
        }

        return to_route('admin.projects.index')
            ->with('status', "Portofolio \"{$project->name}\" berhasil diperbarui.")
            ->with('warning', $this->translationWarning($failures));
    }

    /**
     * Delete the project and its image; its tags stay available for other projects.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        Storage::disk('public')->delete($project->image_path);

        return to_route('admin.projects.index')
            ->with('status', "Portofolio \"{$project->name}\" berhasil dihapus.");
    }

    /**
     * Run a translating step, returning the failure message instead of letting it stop the save.
     */
    private function attempt(Closure $step): ?string
    {
        try {
            $step();
        } catch (TranslationFailed $exception) {
            return $exception->getMessage();
        }

        return null;
    }

    /**
     * Combine translation failures into one warning, so a single DeepL problem is reported once.
     *
     * @param  list<?string>  $failures
     */
    private function translationWarning(array $failures): ?string
    {
        $messages = array_unique(array_filter($failures));

        return $messages === []
            ? null
            : implode(' ', $messages).' Untuk sementara, versi Inggris website memakai teks Bahasa Indonesia.';
    }
}
