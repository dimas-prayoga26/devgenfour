<?php

namespace Tests\Feature;

use App\Models\Intern;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class InternVerificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_anyone_scanning_a_certificate_sees_the_intern_details(): void
    {
        $intern = Intern::factory()->create([
            'name' => 'Rina Kartika',
            'year' => 2026,
            'sequence' => 1,
            'photo_path' => 'interns/rina.jpg',
            'start_date' => '2026-07-01',
            'end_date' => '2026-09-30',
        ]);

        $response = $this->get("/verify/{$intern->id}");

        $response->assertOk()
            ->assertSeeText('Terverifikasi')
            ->assertSeeText('Verifikasi Sertifikat Magang')
            ->assertSeeTextInOrder(['Nomor Magang', '00012026', 'Nama', 'Rina Kartika', 'Tanggal Mulai', '1 Juli 2026', 'Tanggal Selesai', '30 September 2026'])
            ->assertSee(asset('storage/interns/rina.jpg'), false)
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_download_button_appears_only_once_a_certificate_is_uploaded(): void
    {
        $withCertificate = Intern::factory()->withCertificate()->create();
        $withoutCertificate = Intern::factory()->create();

        $this->get("/verify/{$withCertificate->id}")
            ->assertSeeText('Download Sertifikat')
            ->assertSee(route('interns.certificate.download', $withCertificate), false);
        $this->get("/verify/{$withoutCertificate->id}")
            ->assertOk()
            ->assertDontSeeText('Download Sertifikat');
    }

    public function test_uploaded_certificate_downloads_with_a_readable_name(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('interns/acak.pdf', 'isi sertifikat');
        $intern = Intern::factory()->withCertificate('interns/acak.pdf')->create(['name' => 'Rina Kartika', 'year' => 2026, 'sequence' => 1]);

        $response = $this->get("/verify/{$intern->id}/certificate");

        $response->assertOk()->assertDownload('Sertifikat-00012026-rina-kartika.pdf');
        $this->assertSame('isi sertifikat', $response->streamedContent());
    }

    public function test_downloading_a_certificate_that_was_never_uploaded_is_not_found(): void
    {
        $intern = Intern::factory()->create();

        $this->get("/verify/{$intern->id}/certificate")->assertNotFound();
    }

    public function test_unknown_certificate_shows_a_not_found_message(): void
    {
        $response = $this->get('/verify/'.Str::uuid7());

        $response->assertNotFound()
            ->assertSeeText('Sertifikat tidak ditemukan')
            ->assertSeeText('Tidak terverifikasi')
            ->assertDontSeeText('Verifikasi Sertifikat Magang');
    }

    public function test_malformed_code_shows_the_same_not_found_message(): void
    {
        $response = $this->get('/verify/bukan-uuid');

        $response->assertNotFound()->assertSeeText('Sertifikat tidak ditemukan');
    }
}
