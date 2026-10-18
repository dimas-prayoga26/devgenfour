<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Get the stored values for the given keys; keys without a value are left out.
     *
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    public static function values(array $keys): array
    {
        return static::query()->whereIn('key', $keys)->whereNotNull('value')->pluck('value', 'key')->all();
    }

    /**
     * Store the given values, removing the keys whose value is empty.
     *
     * @param  array<string, ?string>  $values
     */
    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            if (blank($value)) {
                static::query()->where('key', $key)->delete();

                continue;
            }

            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
