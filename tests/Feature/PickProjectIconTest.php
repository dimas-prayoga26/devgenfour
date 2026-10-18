<?php

namespace Tests\Feature;

use App\Actions\PickProjectIcon;
use App\Models\Project;
use App\Services\IconLibrary;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PickProjectIconTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @return array<string, array{string, ?string, string}>
     */
    public static function categories(): array
    {
        return [
            'curated phrase' => ['Toko Online', 'Online Store', 'shopping-cart'],
            'curated field' => ['Restoran Padang', null, 'utensils'],
            'curated service' => ['Jasa Laundry', null, 'washing-machine'],
            'specific field beats website' => ['Website Sekolah', null, 'school'],
            'lucide name from the english copy' => ['Penangkaran Ikan Hias', 'Ornamental Fish Breeding', 'fish'],
            'lucide beats the generic shop word' => ['Toko Bunga', 'Flower Shop', 'flower'],
            'lucide tag' => ['Penitipan Anak', 'Child Care', 'baby'],
            'generic word when nothing specific matches' => ['Aplikasi Internal', 'Internal Application', 'code'],
            'fallback' => ['Lainnya', 'Others', 'layout-grid'],
        ];
    }

    #[DataProvider('categories')]
    public function test_picks_an_icon_that_fits_the_category(string $category, ?string $englishCategory, string $expected): void
    {
        $icon = app(PickProjectIcon::class)->pick($category, $englishCategory);

        $this->assertSame($expected, $icon);
    }

    public function test_every_configured_icon_exists_in_the_library(): void
    {
        $library = app(IconLibrary::class);
        $icons = array_unique([
            ...array_values(config('project_icons.keywords')),
            ...array_values(config('project_icons.generic_keywords')),
            config('project_icons.fallback'),
        ]);

        foreach ($icons as $icon) {
            $this->assertTrue($library->has($icon), "Ikon [{$icon}] di config/project_icons.php tidak ada di pustaka Lucide.");
        }
    }

    public function test_changed_category_stores_the_new_icon_and_its_svg(): void
    {
        $project = Project::factory()->create(['category' => 'Lainnya']);
        $project->category = 'Jasa Laundry';

        app(PickProjectIcon::class)->handle($project);

        $this->assertSame('washing-machine', $project->icon);
        $this->assertStringContainsString('<rect width="18" height="20" x="3" y="2" rx="2" />', $project->icon_svg);
    }

    public function test_unchanged_category_keeps_the_current_icon(): void
    {
        $project = Project::factory()->create(['category' => 'Jasa Laundry']);
        $project->name = 'Nama Baru';

        app(PickProjectIcon::class)->handle($project);

        $this->assertSame('circle', $project->icon);
    }
}
