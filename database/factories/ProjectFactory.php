<?php

namespace Database\Factories;

use App\Enums\ProjectTone;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'category' => fake()->randomElement(['Toko Online', 'Pendidikan', 'Laporan Bisnis']),
            'category_en' => null,
            'description' => fake()->sentence(16),
            'description_en' => null,
            'image_path' => 'projects/'.fake()->uuid().'.jpg',
            'icon' => 'circle',
            'icon_svg' => '<circle cx="12" cy="12" r="10"/>',
            'tone' => fake()->randomElement(ProjectTone::cases()),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    /**
     * Indicate that the project is hidden from the website.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_published' => false,
        ]);
    }

    /**
     * Indicate that the project has its own English copy.
     */
    public function withEnglishCopy(): static
    {
        return $this->state(fn (array $attributes): array => [
            'category_en' => 'Online Store',
            'description_en' => 'An English description of the project.',
        ]);
    }

    /**
     * Attach tags in the given order, reusing tags that already exist.
     *
     * @param  array<string, ?string>  $tags  Indonesian names mapped to their English names
     */
    public function withTags(array $tags): static
    {
        return $this->afterCreating(function (Project $project) use ($tags): void {
            $position = 0;

            foreach ($tags as $name => $englishName) {
                $tag = Tag::query()->firstOrCreate(['name' => $name], ['name_en' => $englishName]);
                $project->tags()->attach($tag, ['position' => $position++]);
            }
        });
    }
}
