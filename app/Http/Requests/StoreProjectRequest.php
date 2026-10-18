<?php

namespace App\Http\Requests;

use App\Enums\ProjectTone;
use App\Models\Project;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Turn the comma-separated tag input into a list before validating it.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'tags' => $this->splitTags($this->input('tags')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:500'],
            'tags' => ['required', 'array', 'max:5'],
            'tags.*' => ['string', 'max:30'],
            'tone' => ['required', Rule::enum(ProjectTone::class)],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['required', 'boolean'],
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
            'required' => ':attribute wajib diisi.',
            'max' => ':attribute maksimal :max karakter.',
            'boolean' => ':attribute tidak valid.',
            'enum' => ':attribute yang dipilih tidak valid.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat JPG, PNG, atau WebP.',
            'uploaded' => ':attribute gagal diunggah. Pastikan ukurannya tidak lebih dari 2 MB.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'tags.max' => 'Tag maksimal :max buah.',
            'tags.*.max' => 'Setiap tag maksimal :max karakter.',
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
            'name' => 'Nama proyek',
            'category' => 'Kategori',
            'description' => 'Deskripsi',
            'tags' => 'Tag',
            'tone' => 'Warna',
            'image' => 'Gambar',
            'is_published' => 'Status tampil',
        ];
    }

    /**
     * Accept tags from the chip input (an array) or the plain text fallback (comma-separated), without blanks or repeats.
     *
     * @return array<array-key, mixed>
     */
    private function splitTags(mixed $value): array
    {
        $tags = is_array($value) ? $value : Project::parseTags((string) $value);

        return collect($tags)
            ->map(fn (mixed $tag): mixed => is_string($tag) ? trim($tag) : $tag)
            ->reject(fn (mixed $tag): bool => $tag === '' || $tag === null)
            ->unique(fn (mixed $tag): mixed => is_string($tag) ? mb_strtolower($tag) : $tag)
            ->values()
            ->all();
    }
}
