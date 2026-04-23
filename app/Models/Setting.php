<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        $setting = static::where('group', $group)->where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return static::cast($setting->value, $setting->type);
    }

    public static function set(string $group, string $key, mixed $value, string $type = 'string'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => (string) $value, 'type' => $type]
        );
    }

    public static function getGroup(string $group): array
    {
        return static::where('group', $group)
            ->get()
            ->mapWithKeys(fn($s) => [$s->key => static::cast($s->value, $s->type)])
            ->toArray();
    }

   public static function saveGroup(string $group, array $data, array $types = []): void
{
    foreach ($data as $settingKey => $value) {
        static::updateOrCreate(
            [
                'group' => $group,
                'key'   => $settingKey,
            ],
            [
                'value' => (string) $value,
                'type'  => $types[$settingKey] ?? 'string',
            ]
        );
    }
}

    private static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => (bool) (int) $value,
            'integer' => (int) $value,
            'json'    => json_decode($value, true),
            default   => (string) ($value ?? ''),
        };
    }
}
