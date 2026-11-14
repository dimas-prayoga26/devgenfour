<?php

namespace Tests\Feature;

use App\Models\Intern;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
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
            ->assertSeeText('Sertifikat terverifikasi')
            ->assertSeeText('Rina Kartika')
            ->assertSeeText('00012026')
            ->assertSeeText('1 Juli 2026')
            ->assertSeeText('30 September 2026')
            ->assertSee(asset('storage/interns/rina.jpg'), false)
            ->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }

    public function test_unknown_certificate_shows_a_not_found_message(): void
    {
        $response = $this->get('/verify/'.Str::uuid7());

        $response->assertNotFound()
            ->assertSeeText('Sertifikat tidak ditemukan')
            ->assertDontSeeText('Sertifikat terverifikasi');
    }

    public function test_malformed_code_shows_the_same_not_found_message(): void
    {
        $response = $this->get('/verify/bukan-uuid');

        $response->assertNotFound()->assertSeeText('Sertifikat tidak ditemukan');
    }
}
