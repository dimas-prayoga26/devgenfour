<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class IconCommandsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_builds_the_icon_library_from_lucide_static(): void
    {
        $outputDirectory = storage_path('framework/testing/icon-library');
        File::deleteDirectory($outputDirectory);

        $this->artisan('app:build-icon-library', [
            '--source' => base_path('tests/Fixtures/lucide-static'),
            '--output' => $outputDirectory.'/lucide.json',
        ])->expectsOutputToContain('1 ikon disimpan')->assertSuccessful();

        $this->assertSame(
            ['sample-star' => ['tags' => ['favourite', 'rating'], 'svg' => '<path d="M1 1h2" /><circle cx="12" cy="12" r="3" />']],
            File::json($outputDirectory.'/lucide.json'),
        );
        $this->assertFileExists($outputDirectory.'/LICENSE-lucide.txt');

        File::deleteDirectory($outputDirectory);
    }

    public function test_building_fails_when_lucide_static_is_missing(): void
    {
        $this->artisan('app:build-icon-library', ['--source' => base_path('tests/Fixtures/tidak-ada')])
            ->expectsOutputToContain('tags.json tidak ditemukan')
            ->assertFailed();
    }

    public function test_refresh_command_picks_every_project_icon_again(): void
    {
        $project = Project::factory()->create(['category' => 'Jasa Laundry']);

        $this->artisan('app:refresh-project-icons')
            ->expectsOutputToContain('1 ikon portofolio diperbarui.')
            ->assertSuccessful();

        $this->assertSame('washing-machine', $project->refresh()->icon);
    }
}
