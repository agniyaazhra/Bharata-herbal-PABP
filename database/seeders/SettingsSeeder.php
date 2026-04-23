<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [

            ['group' => 'store', 'key' => 'store_name',        'value' => 'Bharata Herbal',               'type' => 'string'],
            ['group' => 'store', 'key' => 'store_description', 'value' => 'Produk herbal alami berkualitas tinggi dari Nusantara.', 'type' => 'string'],
            ['group' => 'store', 'key' => 'store_address',     'value' => 'Jl. Nusantara No. 1, Jakarta, Indonesia', 'type' => 'string'],
            ['group' => 'store', 'key' => 'whatsapp_number',   'value' => '+6281234567890',                'type' => 'string'],
            ['group' => 'store', 'key' => 'business_email',    'value' => 'info@bharataherbal.id',         'type' => 'string'],
            ['group' => 'store', 'key' => 'instagram',         'value' => '@bharataherbal',                'type' => 'string'],

            ['group' => 'payment', 'key' => 'method_cod',           'value' => '1',     'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'method_dana',          'value' => '1',     'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'method_gopay',         'value' => '1',     'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'method_qris',          'value' => '1',     'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'method_bank_transfer', 'value' => '1',     'type' => 'boolean'],
            ['group' => 'payment', 'key' => 'cod_fee',              'value' => '15000', 'type' => 'integer'],

            ['group' => 'shipping', 'key' => 'shipping_method',        'value' => 'flat_rate', 'type' => 'string'],
            ['group' => 'shipping', 'key' => 'flat_rate_cost',         'value' => '10000',     'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'free_shipping_minimum',  'value' => '0',         'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'minimum_order_amount',   'value' => '0',         'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'fallback_estimated_days', 'value' => '3',         'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'courier_jne_active',     'value' => '1',         'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'courier_jnt_active',     'value' => '1',         'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'courier_sicepat_active', 'value' => '1',         'type' => 'boolean'],
            ['group' => 'shipping', 'key' => 'courier_jne_days',       'value' => '2',         'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'courier_jnt_days',       'value' => '2',         'type' => 'integer'],
            ['group' => 'shipping', 'key' => 'courier_sicepat_days',   'value' => '1',         'type' => 'integer'],

            ['group' => 'notification', 'key' => 'event_order_created',      'value' => '1',                    'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'event_payment_confirmed',  'value' => '1',                    'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'event_order_shipped',      'value' => '1',                    'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'event_order_completed',    'value' => '1',                    'type' => 'boolean'],
            ['group' => 'notification', 'key' => 'email_primary',            'value' => 'info@bharataherbal.id', 'type' => 'string'],
            ['group' => 'notification', 'key' => 'email_backup',             'value' => '',                     'type' => 'string'],
            ['group' => 'notification', 'key' => 'whatsapp_primary',         'value' => '+6281234567890',        'type' => 'string'],
            ['group' => 'notification', 'key' => 'whatsapp_backup',          'value' => '',                     'type' => 'string'],
        ];

        foreach ($defaults as $setting) {
            DB::table('settings')->updateOrInsert(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type'], 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }
}
