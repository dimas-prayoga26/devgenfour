<?php

namespace App\Services;

use Illuminate\Container\Attributes\Config;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;

#[Singleton]
class IconLibrary
{
    /**
     * Name parts of status variants such as "fish-off" that should lose to the plain icon.
     */
    private const VARIANT_PARTS = ['off', 'x', 'plus', 'minus', 'check', 'slash', 'question', 'mark', 'alert', 'cog', 'lock', 'dashed', 'pen', 'search'];

    private const MINIMUM_SCORE = 3;

    /** @var array<string, array{tags: list<string>, svg: string}>|null */
    private ?array $icons = null;

    public function __construct(#[Config('project_icons.library')] private string $path) {}

    /**
     * Determine whether the library contains the icon.
     */
    public function has(string $name): bool
    {
        return isset($this->icons()[$name]);
    }

    /**
     * Get the inner SVG markup of an icon.
     */
    public function svg(string $name): string
    {
        return $this->icons()[$name]['svg'] ?? throw new InvalidArgumentException("Icon [{$name}] is not in the library.");
    }

    /**
     * Find the icon whose name or tags best match the given lowercase words.
     *
     * @param  list<string>  $words
     */
    public function bestMatch(array $words): ?string
    {
        $bestName = null;
        $bestScore = 0;

        foreach ($this->icons() as $name => $icon) {
            $score = $this->score($name, $icon['tags'], $words);

            if ($score > $bestScore || ($score === $bestScore && $bestName !== null && $this->isSimpler($name, $bestName))) {
                [$bestName, $bestScore] = [$name, $score];
            }
        }

        return $bestScore >= self::MINIMUM_SCORE ? $bestName : null;
    }

    /**
     * An exact name beats a name part, which beats a tag; earlier tags are the more typical meanings.
     *
     * @param  list<string>  $tags
     * @param  list<string>  $words
     */
    private function score(string $name, array $tags, array $words): int
    {
        $parts = explode('-', $name);
        $score = 0;

        foreach ($words as $word) {
            $tagPosition = array_search($word, $tags, true);

            $score += match (true) {
                $name === $word => 10,
                in_array($word, $parts, true) => 6,
                $tagPosition !== false => max(1, 4 - $tagPosition),
                default => 0,
            };
        }

        if ($score > 0 && array_intersect($parts, self::VARIANT_PARTS) !== []) {
            $score -= 3;
        }

        return $score;
    }

    private function isSimpler(string $name, string $than): bool
    {
        return [substr_count($name, '-'), strlen($name), $name] < [substr_count($than, '-'), strlen($than), $than];
    }

    /**
     * @return array<string, array{tags: list<string>, svg: string}>
     */
    private function icons(): array
    {
        return $this->icons ??= File::json($this->path, JSON_THROW_ON_ERROR);
    }
}
