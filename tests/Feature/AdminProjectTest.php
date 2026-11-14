<?php

namespace Tests\Feature;

use App\Enums\ProjectTone;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProjectTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_project_list_includes_hidden_projects(): void
    {
        Project::factory()->create(['name' => 'Proyek Tampil']);
        Project::factory()->hidden()->create(['name' => 'Proyek Draf']);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/projects');

        $response->assertOk()
            ->assertSeeText('Proyek Tampil')
            ->assertSeeText('Proyek Draf')
            ->assertSeeText('Disembunyikan');
    }

    public function test_valid_submission_creates_the_project_and_stores_its_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'image' => UploadedFile::fake()->image('tampilan.jpg', 1200, 800),
        ]));

        $project = Project::query()->sole();
        $response->assertRedirectToRoute('admin.projects.index')
            ->assertSessionHas('status', 'Portofolio "Nusa Commerce" berhasil ditambahkan.');
        $this->assertSame('Toko Online', $project->category);
        $this->assertSame(['Katalog Produk', 'Pembayaran'], $project->tags->pluck('name')->all());
        $this->assertSame(ProjectTone::Mint, $project->tone);
        $this->assertTrue($project->is_published);
        Storage::disk('public')->assertExists($project->image_path);
    }

    public function test_empty_submission_lists_the_required_fields_in_indonesian(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', []);

        $response->assertSessionHasErrors([
            'name' => 'Nama proyek wajib diisi.',
            'category' => 'Kategori wajib diisi.',
            'description' => 'Deskripsi wajib diisi.',
            'tags' => 'Tag wajib diisi.',
            'image' => 'Gambar wajib diisi.',
        ]);
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_svg_upload_is_rejected(): void
    {
        Storage::fake('public');
        $svg = UploadedFile::fake()->createWithContent('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload(['image' => $svg]));

        $response->assertSessionHasErrors(['image' => 'Gambar harus berformat JPG, PNG, atau WebP.']);
        $this->assertDatabaseCount('projects', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_more_than_five_tags_are_rejected(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'tags' => 'Satu, Dua, Tiga, Empat, Lima, Enam',
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $response->assertSessionHasErrors(['tags' => 'Tag maksimal 5 buah.']);
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_updating_with_a_new_image_replaces_and_deletes_the_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/lama.jpg', 'gambar lama');
        $project = Project::factory()->create(['image_path' => 'projects/lama.jpg']);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'name' => 'Nama Baru',
            'image' => UploadedFile::fake()->image('baru.png', 1200, 800),
        ]));

        $project->refresh();
        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertSame('Nama Baru', $project->name);
        $this->assertNotSame('projects/lama.jpg', $project->image_path);
        Storage::disk('public')->assertExists($project->image_path);
        Storage::disk('public')->assertMissing('projects/lama.jpg');
    }

    public function test_updating_without_an_image_keeps_the_current_file_and_can_hide_the_project(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/tetap.jpg', 'gambar sekarang');
        $project = Project::factory()->create(['image_path' => 'projects/tetap.jpg']);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'is_published' => '0',
        ]));

        $project->refresh();
        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertSame('projects/tetap.jpg', $project->image_path);
        $this->assertFalse($project->is_published);
        Storage::disk('public')->assertExists('projects/tetap.jpg');
    }

    public function test_deleting_a_project_removes_the_record_and_its_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('projects/hapus.jpg', 'gambar');
        $project = Project::factory()->create(['image_path' => 'projects/hapus.jpg']);

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/admin/projects/{$project->id}");

        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertModelMissing($project);
        Storage::disk('public')->assertMissing('projects/hapus.jpg');
    }

    public function test_edit_form_shows_the_current_icon_without_letting_the_admin_pick_one(): void
    {
        $project = Project::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get("/admin/projects/{$project->id}/edit");

        $response->assertOk()
            ->assertSee('<code data-icon-name class="text-navy">circle</code>', false)
            ->assertDontSee('name="icon"', false);
    }

    public function test_saving_a_project_picks_its_icon_from_the_category(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'category' => 'Jasa Laundry',
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $project = Project::query()->sole();
        $this->assertSame('washing-machine', $project->icon);
        $this->assertStringContainsString('<rect width="18" height="20" x="3" y="2" rx="2" />', $project->icon_svg);
    }

    public function test_project_form_only_asks_for_indonesian_copy(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/projects/create');

        $response->assertOk()
            ->assertSee('name="description"', false)
            ->assertDontSee('name="description_en"', false)
            ->assertDontSee('name="category_en"', false)
            ->assertDontSee('name="tags_en"', false)
            ->assertDontSee('name="sort_order"', false);
    }

    public function test_tag_field_suggests_every_saved_tag_once(): void
    {
        Project::factory()->withTags(['Pembayaran' => null, 'Katalog Produk' => null])->create();
        Project::factory()->withTags(['pembayaran' => null, 'Reservasi' => null])->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/projects/create');

        $response->assertOk()
            ->assertSee('data-suggestions="[&quot;Katalog Produk&quot;,&quot;Pembayaran&quot;,&quot;Reservasi&quot;]"', false);
    }

    public function test_tags_from_the_chip_input_are_trimmed_and_deduplicated(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'tags' => ['Katalog Produk', '  Pembayaran ', 'katalog produk', ''],
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $this->assertSame(['Katalog Produk', 'Pembayaran'], Project::query()->sole()->tags->pluck('name')->all());
    }

    public function test_a_brand_new_tag_is_suggested_for_the_next_project(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/projects', $this->validPayload([
            'tags' => ['Pemesanan Tiket'],
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));
        $response = $this->actingAs($admin)->get('/admin/projects/create');

        $response->assertOk()->assertSee('&quot;Pemesanan Tiket&quot;', false);
    }

    public function test_new_projects_are_placed_after_the_existing_ones(): void
    {
        Storage::fake('public');
        Project::factory()->create(['sort_order' => 4]);

        $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'name' => 'Proyek Terbaru',
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $this->assertSame(5, Project::query()->where('name', 'Proyek Terbaru')->value('sort_order'));
    }

    public function test_updating_keeps_the_display_order_even_when_one_is_submitted(): void
    {
        Storage::fake('public');
        $project = Project::factory()->create(['sort_order' => 3]);

        $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'sort_order' => '99',
        ]));

        $this->assertSame(3, $project->refresh()->sort_order);
    }

    public function test_saving_a_new_project_stores_its_english_translation(): void
    {
        Storage::fake('public');
        $this->fakeDeepL([
            'Toko Online' => 'Online Store',
            'Contoh toko online dengan katalog produk dan pembayaran.' => 'A sample online store with a product catalog and payments.',
            'Katalog Produk' => 'Product Catalog',
            'Pembayaran' => 'Payment',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $project = Project::query()->sole();
        $response->assertRedirectToRoute('admin.projects.index')->assertSessionMissing('warning');
        $this->assertSame('Online Store', $project->category_en);
        $this->assertSame('A sample online store with a product catalog and payments.', $project->description_en);
        $this->assertSame(['Product Catalog', 'Payment'], $project->tags->pluck('name_en')->all());
    }

    public function test_unchanged_indonesian_copy_is_not_translated_again(): void
    {
        Storage::fake('public');
        $this->fakeDeepL([]);
        $project = Project::factory()->withEnglishCopy()->withTags(['Katalog Produk' => 'Product Catalog', 'Pembayaran' => 'Payment'])->create([
            'category' => 'Toko Online',
            'description' => 'Contoh toko online dengan katalog produk dan pembayaran.',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'name' => 'Nama Baru',
        ]));

        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertSame('Online Store', $project->refresh()->category_en);
        Http::assertNothingSent();
    }

    public function test_only_changed_indonesian_copy_is_translated_again(): void
    {
        Storage::fake('public');
        $this->fakeDeepL(['Deskripsi baru.' => 'A new description.']);
        $project = Project::factory()->withEnglishCopy()->withTags(['Katalog Produk' => 'Product Catalog', 'Pembayaran' => 'Payment'])->create([
            'category' => 'Toko Online',
            'description' => 'Deskripsi lama.',
        ]);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'description' => 'Deskripsi baru.',
        ]));

        $project->refresh();
        $response->assertRedirectToRoute('admin.projects.index');
        $this->assertSame('A new description.', $project->description_en);
        $this->assertSame('Online Store', $project->category_en);
        Http::assertSent(fn (Request $request): bool => $request['text'] === ['Deskripsi baru.']);
    }

    public function test_failed_translation_still_saves_the_project_and_warns_the_admin(): void
    {
        Storage::fake('public');
        config(['services.deepl.key' => 'kunci-uji:fx']);
        Http::fake(['api-free.deepl.com/v2/translate' => Http::response(['message' => 'Quota exceeded'], 456)]);
        $project = Project::factory()->withEnglishCopy()->create(['description' => 'Deskripsi lama.']);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/projects/{$project->id}", $this->validPayload([
            'description' => 'Deskripsi baru.',
        ]));

        $project->refresh();
        $response->assertRedirectToRoute('admin.projects.index')
            ->assertSessionHas('warning', 'Kuota terjemahan DeepL bulan ini sudah habis. Untuk sementara, versi Inggris website memakai teks Bahasa Indonesia.');
        $this->assertSame('Deskripsi baru.', $project->description);
        $this->assertNull($project->description_en);
        $this->assertSame(['Katalog Produk', 'Pembayaran'], $project->tags->pluck('name')->all());
        $this->assertSame([null, null], $project->tags->pluck('name_en')->all());
    }

    public function test_saving_without_an_api_key_warns_that_translation_is_disabled(): void
    {
        Storage::fake('public');
        config(['services.deepl.key' => null]);

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/projects', $this->validPayload([
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $response->assertRedirectToRoute('admin.projects.index')
            ->assertSessionHas('warning', 'Terjemahan otomatis belum aktif. Isi DEEPL_API_KEY di file .env. Untuk sementara, versi Inggris website memakai teks Bahasa Indonesia.');
        $this->assertNull(Project::query()->sole()->category_en);
    }

    public function test_guest_cannot_create_a_project(): void
    {
        Storage::fake('public');

        $response = $this->post('/admin/projects', $this->validPayload([
            'image' => UploadedFile::fake()->image('tampilan.jpg'),
        ]));

        $response->assertRedirectToRoute('admin.login');
        $this->assertDatabaseCount('projects', 0);
        Storage::disk('public')->assertEmpty();
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name' => 'Nusa Commerce',
            'category' => 'Toko Online',
            'description' => 'Contoh toko online dengan katalog produk dan pembayaran.',
            'tags' => 'Katalog Produk, Pembayaran',
            'tone' => ProjectTone::Mint->value,
            'is_published' => '1',
            ...$overrides,
        ];
    }
}
