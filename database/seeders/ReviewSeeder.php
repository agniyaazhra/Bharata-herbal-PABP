<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            ['name' => 'Budi Santoso',    'title' => 'Pengusaha, Jakarta',      'comment' => 'Jamu Beras Kencur dari Bharata Herbal luar biasa! Badan terasa segar dan nafsu makan meningkat. Sudah 3 bulan konsumsi dan hasilnya sangat terasa.', 'rating' => 5, 'product_slug' => 'jamu-beras-kencur-premium'],
            ['name' => 'Siti Aminah',     'title' => 'Ibu Rumah Tangga, Bogor', 'comment' => 'Kapsul sambiloto sudah saya coba setelah dokter menyarankan herbal pendamping. Hasilnya gula darah lebih stabil. Produk terpercaya!', 'rating' => 5, 'product_slug' => 'kapsul-sambiloto-diabetes-care'],
            ['name' => 'Prof. Agus W.',   'title' => 'Akademisi, Yogyakarta',   'comment' => 'Herbal Nyeri Sendi Plus sangat membantu mobilitas saya. Nyeri sendi berkurang signifikan setelah 2 minggu konsumsi. Sangat direkomendasikan!', 'rating' => 5, 'product_slug' => 'herbal-nyeri-sendi-plus'],
            ['name' => 'Dewi Rahayu',     'title' => 'Guru, Surabaya',          'comment' => 'Propolis Plus Madu-nya terasa manfaatnya. Sudah jarang sakit sejak konsumsi rutin. Anak-anak pun suka rasanya yang manis!', 'rating' => 5, 'product_slug' => 'propolis-plus-madu-hutan'],
            ['name' => 'Dr. Eko P.',      'title' => 'Dokter Umum, Bandung',    'comment' => 'Sebagai tenaga medis, saya sangat mengapresiasi kualitas produk Bharata Herbal yang sudah melalui seleksi bahan yang ketat. Recommended!', 'rating' => 5, 'product_slug' => 'ekstrak-temulawak-kunyit'],
            ['name' => 'Fitri Handayani', 'title' => 'Mahasiswi, Malang',       'comment' => 'Kapsul Pegagan benar-benar membantu konsentrasi belajar saya. Sudah ujian pun lebih tenang. Terima kasih Bharata Herbal!', 'rating' => 4, 'product_slug' => 'kapsul-pegagan-brain-booster'],
        ];

        $users = User::where('role', 'customer')->get();
        $products = Product::pluck('id', 'slug')->toArray();

        foreach ($testimonials as $i => $t) {
            $user = $users[$i % $users->count()];
            $productId = $products[$t['product_slug']] ?? $products[array_key_first($products)];

            Review::create([
                'user_id'       => $user->id,
                'product_id'    => $productId,
                'rating'        => $t['rating'],
                'comment'       => $t['comment'],
                'reviewer_name' => $t['name'],
                'reviewer_title'=> $t['title'],
                'is_featured'   => true,
            ]);
        }
    }
}
