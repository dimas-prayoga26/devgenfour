<?php

namespace Database\Seeders;

use App\Actions\PickProjectIcon;
use App\Enums\ProjectTone;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProjectSeeder extends Seeder
{
    /**
     * Seed the design samples that were shown before the portfolio moved to the database.
     */
    public function run(PickProjectIcon $pickProjectIcon): void
    {
        foreach ($this->samples() as $order => $sample) {
            $imagePath = 'projects/'.$sample['image'];

            Storage::disk('public')->put(
                $imagePath,
                file_get_contents(public_path('assets/company/portfolio/'.$sample['image'])),
            );

            $project = Project::query()->firstOrNew(['name' => $sample['name']]);
            $project->fill([
                ...collect($sample)->except(['image', 'tags', 'tags_en'])->all(),
                'image_path' => $imagePath,
                'is_published' => true,
                'sort_order' => $order + 1,
            ]);
            $pickProjectIcon->handle($project, force: true);
            $project->save();

            $project->tags()->sync(collect($sample['tags'])->mapWithKeys(fn (string $name, int $position): array => [
                Tag::query()->firstOrCreate(['name' => $name], ['name_en' => $sample['tags_en'][$position]])->id => ['position' => $position],
            ])->all());
        }
    }

    /**
     * @return list<array{name: string, image: string, tone: ProjectTone, category: string, category_en: string, description: string, description_en: string, tags: list<string>, tags_en: list<string>}>
     */
    private function samples(): array
    {
        return [
            [
                'name' => 'Nusa Commerce',
                'image' => 'commerce.svg',
                'tone' => ProjectTone::Mint,
                'category' => 'Toko Online',
                'category_en' => 'Online Store',
                'description' => 'Contoh toko online yang menyatukan katalog produk, proses belanja yang nyaman, dan pengelolaan pesanan dalam satu tempat.',
                'description_en' => 'A sample online store that brings a product catalog, an easy shopping experience, and order management together in one place.',
                'tags' => ['Katalog Produk', 'Pembayaran', 'Kelola Pesanan'],
                'tags_en' => ['Product Catalog', 'Payment', 'Order Management'],
            ],
            [
                'name' => 'Pulse Dashboard',
                'image' => 'analytics.svg',
                'tone' => ProjectTone::Blue,
                'category' => 'Laporan Bisnis',
                'category_en' => 'Business Reports',
                'description' => 'Contoh aplikasi laporan untuk merangkum performa bisnis, memantau aktivitas, dan menyajikan data yang mudah dipahami tim.',
                'description_en' => 'A sample reporting app that summarizes business performance, tracks activity, and presents data your team can easily understand.',
                'tags' => ['Grafik Data', 'Laporan', 'Panel Admin'],
                'tags_en' => ['Charts', 'Reports', 'Admin Panel'],
            ],
            [
                'name' => 'Aksara Learning',
                'image' => 'learning.svg',
                'tone' => ProjectTone::Lavender,
                'category' => 'Pendidikan',
                'category_en' => 'Education',
                'description' => 'Contoh platform belajar online yang menghubungkan materi, kelas, dan perkembangan peserta dalam tampilan yang rapi dan mudah diikuti.',
                'description_en' => 'A sample online learning platform that connects course materials, classes, and learner progress in a clean, easy-to-follow layout.',
                'tags' => ['Kelas Online', 'Materi Belajar', 'Progres Peserta'],
                'tags_en' => ['Online Classes', 'Learning Materials', 'Learner Progress'],
            ],
            [
                'name' => 'Care Appointment',
                'image' => 'health.svg',
                'tone' => ProjectTone::Peach,
                'category' => 'Layanan Kesehatan',
                'category_en' => 'Healthcare',
                'description' => 'Contoh aplikasi janji temu yang membantu pengguna mencari layanan, memilih jadwal kunjungan, dan mengatur janji temu.',
                'description_en' => 'A sample booking app that helps people find services, pick a visit time, and manage their appointments.',
                'tags' => ['Reservasi', 'Jadwal Layanan', 'Tampilan HP'],
                'tags_en' => ['Bookings', 'Service Schedule', 'Mobile Screens'],
            ],
        ];
    }
}
