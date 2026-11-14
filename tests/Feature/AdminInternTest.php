<?php

namespace Tests\Feature;

use App\Models\Intern;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminInternTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_intern_list_shows_each_intern_with_their_number_and_period(): void
    {
        Intern::factory()->create(['name' => 'Rina Kartika', 'year' => 2026, 'sequence' => 1, 'start_date' => '2026-07-01', 'end_date' => '2026-09-30']);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/interns');

        $response->assertOk()
            ->assertSeeText('Rina Kartika')
            ->assertSeeText('00012026')
            ->assertSeeText('1 Juli 2026 – 30 September 2026');
    }

    public function test_valid_submission_creates_the_intern_with_the_first_number_of_their_start_year(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', $this->validPayload());

        $intern = Intern::query()->sole();
        $response->assertRedirectToRoute('admin.interns.index')
            ->assertSessionHas('status', 'Magang "Rina Kartika" berhasil ditambahkan dengan nomor 00012026.');
        $this->assertSame('00012026', $intern->number);
        $this->assertSame('2026-07-01', $intern->start_date->toDateString());
        $this->assertSame('2026-09-30', $intern->end_date->toDateString());
        $this->assertTrue(str()->isUuid($intern->id));
        Storage::disk('public')->assertExists($intern->photo_path);
    }

    public function test_numbers_continue_within_a_year_and_restart_in_the_next_one(): void
    {
        Storage::fake('public');
        Intern::factory()->create(['year' => 2026, 'sequence' => 1]);
        Intern::factory()->create(['year' => 2026, 'sequence' => 2]);
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/interns', $this->validPayload(['name' => 'Peserta 2026', 'start_date' => '2026-11-01', 'end_date' => '2027-01-31']));
        $this->actingAs($admin)->post('/admin/interns', $this->validPayload(['name' => 'Peserta 2027', 'start_date' => '2027-02-01', 'end_date' => '2027-04-30']));

        $this->assertSame('00032026', Intern::query()->where('name', 'Peserta 2026')->sole()->number);
        $this->assertSame('00012027', Intern::query()->where('name', 'Peserta 2027')->sole()->number);
    }

    public function test_empty_submission_lists_the_required_fields_in_indonesian(): void
    {
        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', []);

        $response->assertSessionHasErrors([
            'name' => 'Nama wajib diisi.',
            'photo' => 'Foto profil wajib diisi.',
            'start_date' => 'Tanggal mulai wajib diisi.',
            'end_date' => 'Tanggal selesai wajib diisi.',
        ]);
        $this->assertDatabaseCount('interns', 0);
    }

    public function test_end_date_before_the_start_date_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', $this->validPayload([
            'start_date' => '2026-07-01',
            'end_date' => '2026-06-30',
        ]));

        $response->assertSessionHasErrors(['end_date' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.']);
        $this->assertDatabaseCount('interns', 0);
    }

    public function test_svg_photo_is_rejected(): void
    {
        Storage::fake('public');
        $svg = UploadedFile::fake()->createWithContent('foto.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', $this->validPayload(['photo' => $svg]));

        $response->assertSessionHasErrors(['photo' => 'Foto profil harus berformat JPG, PNG, atau WebP.']);
        $this->assertDatabaseCount('interns', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_updating_keeps_the_number_even_when_the_start_year_changes(): void
    {
        Storage::fake('public');
        $intern = Intern::factory()->create(['year' => 2026, 'sequence' => 5]);

        $response = $this->actingAs(User::factory()->admin()->create())->put("/admin/interns/{$intern->id}", $this->validPayload([
            'name' => 'Nama Baru',
            'start_date' => '2027-01-02',
            'end_date' => '2027-03-31',
        ]));

        $intern->refresh();
        $response->assertRedirectToRoute('admin.interns.index');
        $this->assertSame('Nama Baru', $intern->name);
        $this->assertSame('2027-01-02', $intern->start_date->toDateString());
        $this->assertSame('00052026', $intern->number);
    }

    public function test_updating_with_a_new_photo_replaces_and_deletes_the_old_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/lama.jpg', 'foto lama');
        $intern = Intern::factory()->create(['photo_path' => 'interns/lama.jpg']);

        $this->actingAs(User::factory()->admin()->create())->put("/admin/interns/{$intern->id}", $this->validPayload([
            'photo' => UploadedFile::fake()->image('baru.png', 600, 600),
        ]));

        $intern->refresh();
        $this->assertNotSame('interns/lama.jpg', $intern->photo_path);
        Storage::disk('public')->assertExists($intern->photo_path);
        Storage::disk('public')->assertMissing('interns/lama.jpg');
    }

    public function test_updating_without_a_photo_keeps_the_current_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/tetap.jpg', 'foto sekarang');
        $intern = Intern::factory()->create(['photo_path' => 'interns/tetap.jpg']);

        $this->actingAs(User::factory()->admin()->create())->put("/admin/interns/{$intern->id}", Arr::except($this->validPayload(), 'photo'));

        $this->assertSame('interns/tetap.jpg', $intern->refresh()->photo_path);
        Storage::disk('public')->assertExists('interns/tetap.jpg');
    }

    public function test_deleting_an_intern_removes_the_record_and_their_photo(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/hapus.jpg', 'foto');
        $intern = Intern::factory()->create(['photo_path' => 'interns/hapus.jpg']);

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/admin/interns/{$intern->id}");

        $response->assertRedirectToRoute('admin.interns.index');
        $this->assertModelMissing($intern);
        Storage::disk('public')->assertMissing('interns/hapus.jpg');
    }

    public function test_certificate_shows_the_intern_and_a_qr_code_to_their_verification_page(): void
    {
        $intern = Intern::factory()->create(['name' => 'Rina Kartika', 'year' => 2026, 'sequence' => 1, 'start_date' => '2026-07-01', 'end_date' => '2026-09-30']);

        $response = $this->actingAs(User::factory()->admin()->create())->get("/admin/interns/{$intern->id}/certificate");

        $response->assertOk()
            ->assertSeeText('Sertifikat Magang')
            ->assertSeeText('Rina Kartika')
            ->assertSeeText('00012026')
            ->assertSeeText('1 Juli 2026')
            ->assertSeeText('30 September 2026')
            ->assertSeeText(url("/verify/{$intern->id}"))
            ->assertSee('<svg xmlns="http://www.w3.org/2000/svg"', false)
            ->assertDontSee('<?xml', false);
    }

    public function test_guest_cannot_add_an_intern(): void
    {
        Storage::fake('public');

        $response = $this->post('/admin/interns', $this->validPayload());

        $response->assertRedirectToRoute('admin.login');
        $this->assertDatabaseCount('interns', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_account_without_the_admin_role_cannot_add_an_intern(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->create())->post('/admin/interns', $this->validPayload());

        $response->assertForbidden();
        $this->assertDatabaseCount('interns', 0);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name' => 'Rina Kartika',
            'photo' => UploadedFile::fake()->image('foto.jpg', 600, 600),
            'start_date' => '2026-07-01',
            'end_date' => '2026-09-30',
            ...$overrides,
        ];
    }
}
