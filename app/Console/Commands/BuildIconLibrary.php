<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('app:build-icon-library {--source= : Folder package lucide-static} {--output= : File JSON tujuan}')]
#[Description('Bangun pustaka ikon portofolio dari package lucide-static')]
class BuildIconLibrary extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $source = rtrim($this->option('source') ?: base_path('node_modules/lucide-static'), '/\\');
        $output = $this->option('output') ?: config('project_icons.library');

        if (! File::isFile($source.'/tags.json')) {
            $this->components->error("File tags.json tidak ditemukan di {$source}. Jalankan npm install terlebih dahulu.");

            return self::FAILURE;
        }

        $library = [];

        foreach (File::json($source.'/tags.json') as $name => $tags) {
            $file = "{$source}/icons/{$name}.svg";

            if (! File::isFile($file) || preg_match('/<svg[^>]*>(.*)<\/svg>/s', File::get($file), $match) !== 1) {
                continue;
            }

            $library[$name] = [
                'tags' => array_values(array_map(mb_strtolower(...), $tags)),
                'svg' => preg_replace('/>\s+</', '><', trim($match[1])),
            ];
        }

        ksort($library);

        File::ensureDirectoryExists(dirname($output));
        File::put($output, json_encode($library, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        if (File::isFile($source.'/LICENSE')) {
            File::copy($source.'/LICENSE', dirname($output).'/LICENSE-lucide.txt');
        }

        $this->components->info(count($library).' ikon disimpan ke '.$output);

        return self::SUCCESS;
    }
}
