<?php

namespace App\Actions;

use App\Exceptions\TranslationFailed;
use App\Models\Project;
use App\Services\DeepLTranslator;

class TranslateProject
{
    public function __construct(private DeepLTranslator $translator) {}

    /**
     * Fill the English category and description of an unsaved project from its Indonesian copy.
     *
     * Only fields whose Indonesian text changed, or that have no English text yet, are sent to DeepL.
     * When translation fails, those English fields are cleared so the website falls back to the
     * current Indonesian text instead of showing an outdated translation. Tags are translated
     * separately, once per tag, by SyncProjectTags.
     *
     * @throws TranslationFailed
     */
    public function handle(Project $project): void
    {
        $fields = collect(['category', 'description'])
            ->filter(fn (string $field): bool => filled($project->getAttribute($field))
                && ($project->isDirty($field) || blank($project->getAttribute($field.'_en'))))
            ->values();

        if ($fields->isEmpty()) {
            return;
        }

        try {
            if (! $this->translator->isConfigured()) {
                throw new TranslationFailed('Terjemahan otomatis belum aktif. Isi DEEPL_API_KEY di file .env.');
            }

            $translations = $this->translator->toEnglish($fields->map(fn (string $field): string => $project->getAttribute($field))->all());
        } catch (TranslationFailed $exception) {
            $fields->each(fn (string $field) => $project->setAttribute($field.'_en', null));

            throw $exception;
        }

        $fields->each(fn (string $field, int $index) => $project->setAttribute($field.'_en', $translations[$index]));
    }
}
