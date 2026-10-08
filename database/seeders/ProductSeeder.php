<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $img = fn (string $t) => 'https://placehold.co/600x800/ffe4e8/e11d48/png?text=' . urlencode($t);

        // Harga perkiraan dari screenshot Instagram, koreksi lewat dashboard
        $items = [
            ['Princess Tanktop',     60000, 'Tanktop',  ['All Size'],    ['Pink', 'Cream'],        'Tanktop smock manis dengan detail feminin.'],
            ['Basic Polka Tanktop',  50000, 'Tanktop',  ['All Size'],    ['Putih', 'Hitam'],       'Tanktop motif polka, mudah dipadukan.'],
            ['Floral Lace Tanktop',  50000, 'Tanktop',  ['All Size'],    ['Putih', 'Hitam'],       'Tanktop dengan aksen renda floral.'],
            ['Knit Polo Top',        85000, 'Rajut',    ['All Size'],    ['Biru', 'Hitam', 'Abu'], 'Atasan rajut berkerah, nyaman dan rapi.'],
            ['Heart Knit Cardigan', 115000, 'Cardigan', ['All Size'],    ['Navy', 'Cream'],        'Cardigan rajut motif hati, cute untuk layering.'],
            ['Polka Mini Skirt',     95000, 'Rok',      ['S', 'M', 'L'], ['Cream', 'Hitam'],       'Rok mini motif polka dengan ikat pinggang.'],
            ['Tie Pocket Pants',    119000, 'Celana',   ['S', 'M', 'L'], ['Putih', 'Hitam'],       'Celana kantong dengan tali, simpel tapi pretty.'],
        ];

        foreach ($items as [$name, $price, $cat, $sizes, $colors, $desc]) {
            Product::create([
                'name' => $name, 'price' => $price, 'category' => $cat,
                'sizes' => $sizes, 'colors' => $colors,
                'image_url' => $img($name), 'description' => $desc,
            ]);
        }
    }
}