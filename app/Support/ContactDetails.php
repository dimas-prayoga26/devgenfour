<?php

namespace App\Support;

use App\Models\Setting;

/**
 * The company contact details and social media links that the admin manages from the settings page.
 */
final readonly class ContactDetails
{
    /**
     * Social networks in display order, keyed by their settings key.
     */
    public const SOCIAL_NETWORKS = [
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'x' => 'X',
    ];

    /**
     * @param  array<string, string>  $values
     */
    public function __construct(private array $values) {}

    /**
     * Get every settings key that holds a contact detail.
     *
     * @return list<string>
     */
    public static function keys(): array
    {
        return ['email', 'whatsapp', 'address', 'maps_embed', ...array_keys(self::SOCIAL_NETWORKS)];
    }

    /**
     * Load the contact details saved in the settings table.
     */
    public static function fromSettings(): self
    {
        return new self(Setting::values(self::keys()));
    }

    public function email(): ?string
    {
        return $this->values['email'] ?? null;
    }

    public function address(): ?string
    {
        return $this->values['address'] ?? null;
    }

    /**
     * Get the map to embed: the Google Maps embed link the admin pasted, or a map searched from the address.
     */
    public function mapEmbedUrl(): ?string
    {
        if (isset($this->values['maps_embed'])) {
            return $this->values['maps_embed'];
        }

        return $this->address() === null
            ? null
            : 'https://www.google.com/maps?q='.rawurlencode($this->address()).'&output=embed';
    }

    /**
     * Get a link that opens the address in Google Maps, for directions.
     */
    public function mapUrl(): ?string
    {
        return $this->address() === null
            ? null
            : 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($this->address());
    }

    /**
     * Get the WhatsApp number as the admin typed it, for display.
     */
    public function whatsapp(): ?string
    {
        return $this->values['whatsapp'] ?? null;
    }

    /**
     * Get the WhatsApp number in the international format wa.me needs, assuming Indonesia (62) for local numbers.
     */
    public function whatsappNumber(): ?string
    {
        $digits = preg_replace('/\D/', '', $this->whatsapp() ?? '');

        return match (true) {
            $digits === '' => null,
            str_starts_with($digits, '0') => '62'.substr($digits, 1),
            str_starts_with($digits, '8') => '62'.$digits,
            default => $digits,
        };
    }

    /**
     * Get a link that opens a WhatsApp chat with the given message already typed.
     */
    public function whatsappUrl(string $message): ?string
    {
        $number = $this->whatsappNumber();

        return $number === null ? null : "https://wa.me/{$number}?text=".rawurlencode($message);
    }

    /**
     * Determine whether visitors have a way to reach the company directly.
     */
    public function hasDirectContact(): bool
    {
        return $this->email() !== null || $this->whatsappNumber() !== null;
    }

    /**
     * Get the filled social media links in display order.
     *
     * @return list<array{network: string, label: string, url: string}>
     */
    public function socialLinks(): array
    {
        $links = [];

        foreach (self::SOCIAL_NETWORKS as $network => $label) {
            if (isset($this->values[$network])) {
                $links[] = ['network' => $network, 'label' => $label, 'url' => $this->values[$network]];
            }
        }

        return $links;
    }
}
