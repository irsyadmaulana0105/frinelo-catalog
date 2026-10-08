<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Produk asli Frinelo (bukan data contoh).
 * Aman dijalankan berulang kali: produk dicocokkan lewat nama (updateOrCreate).
 * Foto sumber ada di database/seeders/images dan disalin ke storage saat seeding.
 */
class RealProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->universityTop();
    }

    private function universityTop(): void
    {
        $source = database_path('seeders/images/university-top.jpg');
        $path   = 'products/university-top.jpg';

        if (File::exists($source)) {
            Storage::disk('public')->put($path, File::get($source));
        }

        $description = implode("\n", [
            'Atasan model vest rajut V-neck dengan kerah kemeja dan lengan puff. Aksen garis pink dan biru serta emblem university di dada.',
            '',
            'Detail ukuran',
            'Lingkar dada: 90-120 cm',
            'Panjang baju: 55 cm',
            'Panjang lengan: 25 cm',
        ]);

        Product::updateOrCreate(
            ['name' => 'University Top'],
            [
                'price'       => 130000,
                'category'    => 'Rajut',
                'sizes'       => ['All Size'],
                'colors'      => ['White', 'Cream', 'Pink'],
                'image_url'   => File::exists($source) ? $path : null,
                'description' => $description,
                'is_active'   => true,
            ]
        );
    }
}
