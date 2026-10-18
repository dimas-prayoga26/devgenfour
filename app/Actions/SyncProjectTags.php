<?php

namespace App\Actions;

use App\Exceptions\TranslationFailed;
use App\Models\Project;
use App\Models\Tag;
use App\Services\DeepLTranslator;
use Illuminate\Support\Collection;

class SyncProjectTags
{
    public function __construct(private DeepLTranslator $translator) {}

    /**
     * Attach the named tags to a saved project in the given order, creating tags that do not exist yet.
     *
     * Only tags without an English name are sent to DeepL. When translation fails the tags are still
     * saved and attached, then the failure is rethrown so the admin can be warned.
     *
     * @param  list<string>  $names
     *
     * @throws TranslationFailed
     */
    public function handle(Project $project, array $names): void
    {
        $tags = collect($names)->map(fn (string $name): Tag => Tag::query()->firstOrCreate(['name' => $name]));
        $failure = null;

        try {
            $this->translateMissing($tags->filter(fn (Tag $tag): bool => blank($tag->name_en))->values());
        } catch (TranslationFailed $exception) {
            $failure = $exception;
        }

        $project->tags()->sync(
            $tags->values()->mapWithKeys(fn (Tag $tag, int $position): array => [$tag->id => ['position' => $position]])->all(),
        );

        if ($failure !== null) {
            throw $failure;
        }
    }

    /**
     * @param  Collection<int, Tag>  $tags
     *
     * @throws TranslationFailed
     */
    private function translateMissing(Collection $tags): void
    {
        if ($tags->isEmpty()) {
            return;
        }

        if (! $this->translator->isConfigured()) {
            throw new TranslationFailed('Terjemahan otomatis belum aktif. Isi DEEPL_API_KEY di file .env.');
        }

        $translations = $this->translator->toEnglish($tags->pluck('name')->all());

        $tags->each(fn (Tag $tag, int $index) => $tag->update(['name_en' => $translations[$index]]));
    }
}
