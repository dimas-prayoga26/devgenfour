<?php

namespace App\Models;

use App\Enums\ProjectTone;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

#[Fillable([
    'name',
    'category',
    'category_en',
    'description',
    'description_en',
    'image_path',
    'tone',
    'is_published',
    'sort_order',
])]
class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tone' => ProjectTone::class,
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the feature tags of the project in the order the admin entered them.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withPivot('position')->orderByPivot('position');
    }

    /**
     * Limit the query to projects that are visible on the website.
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Order projects the way the admin arranged them.
     */
    #[Scope]
    protected function ordered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Split a comma-separated tag input into a clean list of tags.
     *
     * @return list<string>
     */
    public static function parseTags(?string $input): array
    {
        return Str::of((string) $input)
            ->explode(',')
            ->map(fn (string $tag): string => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Get an attribute in the active language, falling back to Indonesian when no English copy exists.
     */
    public function translated(string $attribute): mixed
    {
        $translation = app()->getLocale() === 'en' ? $this->getAttribute($attribute.'_en') : null;

        return blank($translation) ? $this->getAttribute($attribute) : $translation;
    }

    /**
     * Render the project icon as an inline SVG.
     *
     * The icon markup is only ever copied from the bundled icon library, never from request input,
     * which is why `icon` and `icon_svg` are deliberately left out of the fillable attributes.
     */
    public function iconSvg(string $class): HtmlString
    {
        return new HtmlString(sprintf(
            '<svg class="%s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%s</svg>',
            e($class),
            $this->icon_svg ?? '',
        ));
    }

    /**
     * Get the public URL of the project image.
     */
    public function imageUrl(): string
    {
        return asset('storage/'.$this->image_path);
    }
}
