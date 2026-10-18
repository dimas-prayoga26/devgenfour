<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Move the JSON tag lists into the tags table, keeping each project's tag order and English names.
     */
    public function up(): void
    {
        foreach (DB::table('projects')->get(['id', 'tags', 'tags_en']) as $project) {
            $names = json_decode($project->tags ?? '[]', true) ?: [];
            $englishNames = json_decode($project->tags_en ?? '[]', true) ?: [];

            foreach (array_values($names) as $position => $name) {
                $name = trim((string) $name);

                if ($name === '') {
                    continue;
                }

                $englishName = $englishNames[$position] ?? null;
                $tag = DB::table('tags')->where('name', $name)->first();

                if ($tag === null) {
                    $tagId = DB::table('tags')->insertGetId([
                        'name' => $name,
                        'name_en' => $englishName,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $tagId = $tag->id;

                    if ($tag->name_en === null && $englishName !== null) {
                        DB::table('tags')->where('id', $tagId)->update(['name_en' => $englishName]);
                    }
                }

                DB::table('project_tag')->insertOrIgnore([
                    'project_id' => $project->id,
                    'tag_id' => $tagId,
                    'position' => $position,
                ]);
            }
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['tags', 'tags_en']);
        });
    }

    /**
     * Rebuild the JSON tag lists from the tags table.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('description_en');
            $table->json('tags_en')->nullable()->after('tags');
        });

        foreach (DB::table('projects')->pluck('id') as $projectId) {
            $tags = DB::table('project_tag')
                ->join('tags', 'tags.id', '=', 'project_tag.tag_id')
                ->where('project_tag.project_id', $projectId)
                ->orderBy('project_tag.position')
                ->get(['tags.name', 'tags.name_en']);

            DB::table('projects')->where('id', $projectId)->update([
                'tags' => json_encode($tags->pluck('name')->all(), JSON_UNESCAPED_UNICODE),
                'tags_en' => json_encode($tags->map(fn (object $tag): string => $tag->name_en ?? $tag->name)->all(), JSON_UNESCAPED_UNICODE),
            ]);
        }
    }
};
