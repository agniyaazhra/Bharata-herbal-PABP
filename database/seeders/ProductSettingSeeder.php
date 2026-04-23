<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ProductSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'stock_minimum',             'value' => '10',        'type' => 'integer'],
            ['key' => 'notification_type',          'value' => 'dashboard', 'type' => 'string'],
            ['key' => 'auto_nonaktif_stok_habis',   'value' => '1',         'type' => 'boolean'],
            ['key' => 'auto_warning_stok_minimum',  'value' => '1',         'type' => 'boolean'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['group' => 'product', 'key' => $s['key']],
                ['value' => $s['value'], 'type' => $s['type']]
            );
        }

        $this->command->info('✅ Product settings seeded with defaults.');
    }
}
