<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\InternFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * An intern whose certificate links to a public verification page by the intern's UUID.
 *
 * The intern number is the sequence within the start year followed by that year, e.g. 00012026. It is
 * assigned once when the intern is added and never changes, because it is printed on the certificate;
 * that is why `year` and `sequence` are left out of the fillable attributes. The admin designs the certificate
 * elsewhere with the intern's QR code on it and uploads it afterwards, so `certificate_path` is optional.
 */
#[Fillable(['name', 'start_date', 'end_date'])]
class Intern extends Model
{
    /** @use HasFactory<InternFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'sequence' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Get the intern number, e.g. 00012026 for the first intern starting in 2026.
     *
     * @return Attribute<string, never>
     */
    protected function number(): Attribute
    {
        return Attribute::get(fn (): string => sprintf('%04d%d', $this->sequence, $this->year));
    }

    /**
     * Give the intern the next number of the year their internship starts in.
     *
     * Call this inside a transaction: the lock keeps two admins saving at once from taking the same number.
     */
    public function assignNumber(): void
    {
        $this->year = $this->start_date->year;
        $this->sequence = (int) static::query()->where('year', $this->year)->lockForUpdate()->max('sequence') + 1;
    }

    /**
     * Order the newest interns first.
     */
    #[Scope]
    protected function latestNumber(Builder $query): void
    {
        $query->orderByDesc('year')->orderByDesc('sequence');
    }

    /**
     * Get the public URL of the profile photo.
     */
    public function photoUrl(): string
    {
        return asset('storage/'.$this->photo_path);
    }

    /**
     * Get the public page the certificate's QR code opens.
     */
    public function verificationUrl(): string
    {
        return route('interns.verify', $this);
    }

    /**
     * Determine whether the admin has uploaded the intern's certificate.
     */
    public function hasCertificate(): bool
    {
        return $this->certificate_path !== null;
    }

    /**
     * Get the name the certificate is downloaded as, e.g. Sertifikat-00012026-rina-kartika.pdf.
     */
    public function certificateDownloadName(): string
    {
        return sprintf('Sertifikat-%s-%s.%s', $this->number, Str::slug($this->name), pathinfo($this->certificate_path, PATHINFO_EXTENSION));
    }

    /**
     * Format a date in Indonesian, e.g. 1 Juli 2026.
     */
    public static function formatDate(CarbonInterface $date): string
    {
        return $date->locale('id')->translatedFormat('j F Y');
    }
}
