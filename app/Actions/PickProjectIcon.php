<?php

namespace App\Actions;

use App\Models\Project;
use App\Services\IconLibrary;
use Illuminate\Container\Attributes\Config;
use Illuminate\Support\Str;

class PickProjectIcon
{
    /**
     * Words that describe the kind of product rather than its field, so they say nothing about the icon.
     */
    private const STOP_WORDS = [
        'aplikasi', 'sistem', 'informasi', 'layanan', 'jasa', 'digital', 'platform', 'online', 'berbasis', 'manajemen', 'dan', 'untuk', 'yang', 'dengan', 'toko', 'usaha',
        'app', 'application', 'system', 'information', 'service', 'management', 'solution', 'and', 'for', 'the', 'of', 'with', 'shop', 'store', 'business', 'website', 'web',
    ];

    /**
     * @param  array<string, string>  $keywords
     * @param  array<string, string>  $genericKeywords
     */
    public function __construct(
        private IconLibrary $library,
        #[Config('project_icons.keywords')] private array $keywords,
        #[Config('project_icons.generic_keywords')] private array $genericKeywords,
        #[Config('project_icons.fallback')] private string $fallback,
    ) {}

    /**
     * Choose the icon of an unsaved project when its category changed or it has no icon yet.
     */
    public function handle(Project $project, bool $force = false): void
    {
        if (! $force && filled($project->icon) && ! $project->isDirty(['category', 'category_en'])) {
            return;
        }

        $icon = $this->pick($project->category, $project->category_en);

        $project->icon = $icon;
        $project->icon_svg = $this->library->svg($icon);
    }

    /**
     * Match curated keywords (Indonesian first), then Lucide names and tags, then broad generic words, then the fallback.
     */
    public function pick(?string $category, ?string $englishCategory = null): string
    {
        $words = [
            ...$this->words($category),
            ...array_map(Str::singular(...), $this->words($englishCategory)),
        ];

        return $this->curatedIcon($category)
            ?? $this->matchKeyword($this->keywords, (string) $englishCategory)
            ?? $this->library->bestMatch(array_values(array_unique($words)))
            ?? $this->matchKeyword($this->genericKeywords, trim($category.' '.$englishCategory))
            ?? $this->fallback;
    }

    /**
     * Get the curated icon for an Indonesian category, which needs no translation to decide.
     */
    public function curatedIcon(?string $category): ?string
    {
        return $this->matchKeyword($this->keywords, (string) $category);
    }

    /**
     * @param  array<string, string>  $keywords
     */
    private function matchKeyword(array $keywords, string $text): ?string
    {
        foreach ($keywords as $keyword => $icon) {
            if (preg_match('/(?<![\p{L}\p{N}])'.preg_quote($keyword, '/').'(?![\p{L}\p{N}])/iu', $text) === 1) {
                return $icon;
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function words(?string $text): array
    {
        preg_match_all('/[\p{L}\p{N}]+/u', mb_strtolower((string) $text), $matches);

        return array_values(array_filter(
            $matches[0],
            fn (string $word): bool => mb_strlen($word) > 1 && ! in_array($word, self::STOP_WORDS, true),
        ));
    }
}
