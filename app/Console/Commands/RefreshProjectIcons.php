<?php

namespace App\Console\Commands;

use App\Actions\PickProjectIcon;
use App\Models\Project;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:refresh-project-icons')]
#[Description('Pilih ulang ikon semua portofolio berdasarkan kategorinya')]
class RefreshProjectIcons extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(PickProjectIcon $pickProjectIcon): int
    {
        $rows = Project::query()->ordered()->get()->map(function (Project $project) use ($pickProjectIcon): array {
            $pickProjectIcon->handle($project, force: true);
            $project->save();

            return [$project->name, $project->category, $project->icon];
        });

        $this->table(['Portofolio', 'Kategori', 'Ikon'], $rows);
        $this->components->info("{$rows->count()} ikon portofolio diperbarui.");

        return self::SUCCESS;
    }
}
