<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreInternRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
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
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'certificate' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            'date' => ':attribute harus berupa tanggal yang valid.',
            'image' => ':attribute harus berupa file gambar.',
            'file' => ':attribute harus berupa file.',
            'mimes' => ':attribute harus berformat JPG, PNG, atau WebP.',
            'uploaded' => ':attribute gagal diunggah. Pastikan ukurannya tidak lebih dari 2 MB.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'end_date.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
            'certificate.mimes' => 'Sertifikat harus berformat PDF, JPG, atau PNG.',
            'certificate.max' => 'Ukuran sertifikat maksimal 5 MB.',
            'certificate.uploaded' => 'Sertifikat gagal diunggah. Pastikan ukurannya tidak lebih dari 5 MB.',
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
            'name' => 'Nama',
            'photo' => 'Foto Profile',
            'start_date' => 'Tanggal mulai',
            'end_date' => 'Tanggal selesai',
            'certificate' => 'Sertifikat',
        ];
    }
}
