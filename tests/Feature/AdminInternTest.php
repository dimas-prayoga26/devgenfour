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

    public function test_intern_table_shows_each_intern_with_their_number_dates_and_certificate_status(): void
    {
        Intern::factory()->create(['name' => 'Rina Kartika', 'year' => 2026, 'sequence' => 1, 'start_date' => '2026-07-01', 'end_date' => '2026-09-30']);
        Intern::factory()->withCertificate()->create(['name' => 'Budi Santoso']);

        $response = $this->actingAs(User::factory()->admin()->create())->get('/admin/interns');

        $response->assertOk()
            ->assertSeeTextInOrder(['Foto Profile', 'Nama', 'Nomor Magang', 'Tanggal Mulai', 'Tanggal Selesai', 'Sertifikat', 'Aksi'])
            ->assertSeeText('Rina Kartika')
            ->assertSeeText('00012026')
            ->assertSeeText('1 Juli 2026')
            ->assertSeeText('30 September 2026')
            ->assertSeeText('Belum ada')
            ->assertSeeText('Sudah diunggah');
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
        $this->assertNull($intern->certificate_path);
        Storage::disk('public')->assertExists($intern->photo_path);
    }

    public function test_certificate_can_be_uploaded_together_with_the_new_intern(): void
    {
        Storage::fake('public');

        $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', $this->validPayload([
            'certificate' => UploadedFile::fake()->create('sertifikat.pdf', 300, 'application/pdf'),
        ]));

        $intern = Intern::query()->sole();
        $this->assertTrue($intern->hasCertificate());
        Storage::disk('public')->assertExists($intern->certificate_path);
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
            'photo' => 'Foto Profile wajib diisi.',
            'start_date' => 'Tanggal mulai wajib diisi.',
            'end_date' => 'Tanggal selesai wajib diisi.',
        ]);
        $response->assertSessionDoesntHaveErrors('certificate');
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

        $response->assertSessionHasErrors(['photo' => 'Foto Profile harus berformat JPG, PNG, atau WebP.']);
        $this->assertDatabaseCount('interns', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_certificate_that_is_not_a_pdf_or_image_is_rejected(): void
    {
        Storage::fake('public');

        $response = $this->actingAs(User::factory()->admin()->create())->post('/admin/interns', $this->validPayload([
            'certificate' => UploadedFile::fake()->create('sertifikat.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
        ]));

        $response->assertSessionHasErrors(['certificate' => 'Sertifikat harus berformat PDF, JPG, atau PNG.']);
        $this->assertDatabaseCount('interns', 0);
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

    public function test_uploading_new_files_replaces_and_deletes_the_old_ones(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/foto-lama.jpg', 'foto lama');
        Storage::disk('public')->put('interns/sertifikat-lama.pdf', 'sertifikat lama');
        $intern = Intern::factory()->withCertificate('interns/sertifikat-lama.pdf')->create(['photo_path' => 'interns/foto-lama.jpg']);

        $this->actingAs(User::factory()->admin()->create())->put("/admin/interns/{$intern->id}", $this->validPayload([
            'photo' => UploadedFile::fake()->image('baru.png', 600, 800),
            'certificate' => UploadedFile::fake()->create('sertifikat-baru.pdf', 300, 'application/pdf'),
        ]));

        $intern->refresh();
        Storage::disk('public')->assertExists([$intern->photo_path, $intern->certificate_path]);
        Storage::disk('public')->assertMissing(['interns/foto-lama.jpg', 'interns/sertifikat-lama.pdf']);
    }

    public function test_updating_without_files_keeps_the_current_ones(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/tetap.jpg', 'foto sekarang');
        Storage::disk('public')->put('interns/sertifikat.pdf', 'sertifikat sekarang');
        $intern = Intern::factory()->withCertificate('interns/sertifikat.pdf')->create(['photo_path' => 'interns/tetap.jpg']);

        $this->actingAs(User::factory()->admin()->create())->put("/admin/interns/{$intern->id}", Arr::except($this->validPayload(), 'photo'));

        $intern->refresh();
        $this->assertSame('interns/tetap.jpg', $intern->photo_path);
        $this->assertSame('interns/sertifikat.pdf', $intern->certificate_path);
        Storage::disk('public')->assertExists(['interns/tetap.jpg', 'interns/sertifikat.pdf']);
    }

    public function test_deleting_an_intern_removes_the_record_and_their_files(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/hapus.jpg', 'foto');
        Storage::disk('public')->put('interns/sertifikat.pdf', 'sertifikat');
        $intern = Intern::factory()->withCertificate('interns/sertifikat.pdf')->create(['photo_path' => 'interns/hapus.jpg']);

        $response = $this->actingAs(User::factory()->admin()->create())->delete("/admin/interns/{$intern->id}");

        $response->assertRedirectToRoute('admin.interns.index');
        $this->assertModelMissing($intern);
        Storage::disk('public')->assertMissing(['interns/hapus.jpg', 'interns/sertifikat.pdf']);
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

    public function test_edit_form_shows_the_verification_link_to_put_on_the_certificate(): void
    {
        $intern = Intern::factory()->create();

        $response = $this->actingAs(User::factory()->admin()->create())->get("/admin/interns/{$intern->id}/edit");

        $response->assertOk()->assertSeeText(url("/verify/{$intern->id}"));
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return [
            'name' => 'Rina Kartika',
            'photo' => UploadedFile::fake()->image('foto.jpg', 600, 800),
            'start_date' => '2026-07-01',
            'end_date' => '2026-09-30',
            ...$overrides,
        ];
    }
}
