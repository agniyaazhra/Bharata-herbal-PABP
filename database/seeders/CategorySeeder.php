<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pencernaan',  'slug' => 'pencernaan',  'description' => 'Solusi herbal untuk menjaga kesehatan sistem pencernaan', 'icon' => '🫁'],
            ['name' => 'Persendian',  'slug' => 'persendian',  'description' => 'Produk herbal untuk merawat dan menguatkan sendi', 'icon' => '💪'],
            ['name' => 'Ginjal',      'slug' => 'ginjal',      'description' => 'Herbal pilihan untuk mendukung fungsi ginjal optimal', 'icon' => '💧'],
            ['name' => 'Imunitas',    'slug' => 'imunitas',    'description' => 'Tingkatkan daya tahan tubuh dengan herbal alami', 'icon' => '🛡️'],
            ['name' => 'Stamina',     'slug' => 'stamina',     'description' => 'Produk herbal untuk menjaga energi dan vitalitas', 'icon' => '⚡'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
