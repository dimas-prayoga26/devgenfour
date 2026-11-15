<?php

namespace Database\Factories;

use App\Models\Intern;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends Factory<Intern>
 */
class InternFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = Carbon::instance(fake()->dateTimeBetween('-1 year'))->startOfDay();

        return [
            'year' => $startDate->year,
            'sequence' => fake()->unique()->numberBetween(1, 9999),
            'name' => fake()->name(),
            'photo_path' => 'interns/'.fake()->uuid().'.jpg',
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addMonths(3),
            'certificate_path' => null,
        ];
    }

    /**
     * Indicate that the admin has uploaded the intern's certificate.
     */
    public function withCertificate(string $path = 'interns/sertifikat.pdf'): static
    {
        return $this->state(fn (array $attributes): array => [
            'certificate_path' => $path,
        ]);
    }
}
