<?php

namespace Tests\Feature;

use App\Support\ContactDetails;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContactDetailsTest extends TestCase
{
    /**
     * @return array<string, array{string, ?string}>
     */
    public static function whatsappNumbers(): array
    {
        return [
            'local number with leading zero' => ['0812-3456-7890', '6281234567890'],
            'international with plus and spaces' => ['+62 812 3456 7890', '6281234567890'],
            'local number without leading zero' => ['812 3456 7890', '6281234567890'],
            'foreign number keeps its country code' => ['+1 (415) 555-0100', '14155550100'],
            'no digits' => ['-', null],
        ];
    }

    #[DataProvider('whatsappNumbers')]
    public function test_whatsapp_numbers_use_the_international_format_wa_me_needs(string $typed, ?string $expected): void
    {
        $contact = new ContactDetails(['whatsapp' => $typed]);

        $this->assertSame($expected, $contact->whatsappNumber());
    }

    public function test_social_links_follow_the_display_order_and_skip_empty_networks(): void
    {
        $contact = new ContactDetails([
            'youtube' => 'https://youtube.com/@devgenfour',
            'instagram' => 'https://instagram.com/devgenfour',
        ]);

        $this->assertSame([
            ['network' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/devgenfour'],
            ['network' => 'youtube', 'label' => 'YouTube', 'url' => 'https://youtube.com/@devgenfour'],
        ], $contact->socialLinks());
    }

    public function test_map_is_searched_from_the_address(): void
    {
        $contact = new ContactDetails(['address' => 'Jl. Merdeka 1, Indramayu']);

        $this->assertSame('https://www.google.com/maps?q=Jl.%20Merdeka%201%2C%20Indramayu&output=embed', $contact->mapEmbedUrl());
        $this->assertSame('https://www.google.com/maps/search/?api=1&query=Jl.%20Merdeka%201%2C%20Indramayu', $contact->mapUrl());
    }

    public function test_pasted_google_maps_embed_takes_priority_over_the_address(): void
    {
        $contact = new ContactDetails([
            'address' => 'Indramayu',
            'maps_embed' => 'https://www.google.com/maps/embed?pb=!1m18!1m12',
        ]);

        $this->assertSame('https://www.google.com/maps/embed?pb=!1m18!1m12', $contact->mapEmbedUrl());
    }

    public function test_there_is_no_map_without_an_address_or_embed(): void
    {
        $contact = new ContactDetails(['email' => 'halo@devgenfour.com']);

        $this->assertNull($contact->mapEmbedUrl());
        $this->assertNull($contact->mapUrl());
    }

    public function test_only_email_or_whatsapp_count_as_direct_contact(): void
    {
        $this->assertFalse((new ContactDetails(['address' => 'Indramayu', 'instagram' => 'https://instagram.com/x']))->hasDirectContact());
        $this->assertTrue((new ContactDetails(['email' => 'halo@devgenfour.com']))->hasDirectContact());
    }
}
