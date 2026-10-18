<?php

namespace App\Http\Controllers\Admin;

use App\Actions\PickProjectIcon;
use App\Exceptions\TranslationFailed;
use App\Http\Controllers\Controller;
use App\Services\DeepLTranslator;
use App\Services\IconLibrary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProjectIconPreviewController extends Controller
{
    /**
     * Show the icon a category would get, so the admin sees it change while typing.
     *
     * The category is translated the same way as on save, so the preview matches the saved icon.
     */
    public function show(Request $request, PickProjectIcon $pickProjectIcon, DeepLTranslator $translator, IconLibrary $library): JsonResponse
    {
        $category = trim((string) $request->validate([
            'category' => ['nullable', 'string', 'max:60'],
        ])['category'] ?? '');

        if ($category === '') {
            return response()->json(['icon' => null, 'svg' => null]);
        }

        $icon = $pickProjectIcon->curatedIcon($category)
            ?? $pickProjectIcon->pick($category, $this->englishCategory($category, $translator));

        return response()->json(['icon' => $icon, 'svg' => $library->svg($icon)]);
    }

    /**
     * Translate the category, caching the result so typing the same words again costs no quota.
     */
    private function englishCategory(string $category, DeepLTranslator $translator): ?string
    {
        if (! $translator->isConfigured()) {
            return null;
        }

        try {
            return Cache::remember(
                'project-icon-preview:'.sha1(mb_strtolower($category)),
                now()->addWeek(),
                fn (): string => $translator->toEnglish([$category])[0],
            );
        } catch (TranslationFailed) {
            return null;
        }
    }
}
