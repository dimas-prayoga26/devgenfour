<?php

namespace App\Http\Requests;

use App\Support\ContactDetails;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Accept the whole iframe code Google Maps gives out by keeping only its src link.
     */
    protected function prepareForValidation(): void
    {
        $embed = trim((string) $this->input('maps_embed'));

        if (preg_match('/<iframe[^>]*\ssrc="([^"]+)"/i', $embed, $match) === 1) {
            $embed = html_entity_decode($match[1]);
        }

        $this->merge(['maps_embed' => $embed === '' ? null : $embed]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $socialRules = array_fill_keys(
            array_keys(ContactDetails::SOCIAL_NETWORKS),
            ['nullable', 'url:https,http', 'max:255'],
        );

        return [
            'email' => ['nullable', 'email', 'max:100'],
            'whatsapp' => ['nullable', 'string', 'max:20', 'regex:/^\+?[0-9\s\-()]{8,20}$/'],
            'address' => ['nullable', 'string', 'max:200'],
            'maps_embed' => ['nullable', 'url:https', 'max:2000', 'starts_with:https://www.google.com/maps/embed?'],
            ...$socialRules,
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email' => 'Format email tidak valid.',
            'whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, tanda +, tanda -, atau tanda kurung.',
            'url' => ':attribute harus berupa link lengkap yang diawali https://.',
            'maps_embed.url' => 'Kode sematan harus berasal dari Google Maps (Bagikan → Sematkan peta).',
            'maps_embed.starts_with' => 'Kode sematan harus berasal dari Google Maps (Bagikan → Sematkan peta).',
            'max' => ':attribute maksimal :max karakter.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email',
            'whatsapp' => 'Nomor WhatsApp',
            'address' => 'Alamat',
            'maps_embed' => 'Kode sematan Google Maps',
            ...array_map(fn (string $label): string => "Link {$label}", ContactDetails::SOCIAL_NETWORKS),
        ];
    }
}
