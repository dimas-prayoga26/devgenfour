<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

abstract class TestCase extends BaseTestCase
{
    /**
     * Render views without a compiled Vite manifest and never let a test reach a real external API.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Http::preventStrayRequests();
    }

    /**
     * Fake DeepL so every request translates its texts with the given dictionary.
     *
     * @param  array<string, string>  $dictionary  Indonesian texts mapped to their English translations
     */
    protected function fakeDeepL(array $dictionary): void
    {
        config(['services.deepl.key' => 'kunci-uji:fx']);

        Http::fake(['api-free.deepl.com/v2/translate' => fn (Request $request) => Http::response([
            'translations' => array_map(
                fn (string $text): array => ['detected_source_language' => 'ID', 'text' => $dictionary[$text] ?? "{$text} (EN)"],
                $request['text'],
            ),
        ])]);
    }
}
