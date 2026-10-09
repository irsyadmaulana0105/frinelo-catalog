<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Produk asli Frinelo (bukan data contoh), diambil dari postingan @frinelo.wear.
 *
 * Aman dijalankan berulang kali: hanya MENAMBAH produk yang belum ada (dicocokkan
 * lewat nama) dan tidak menimpa perubahan yang dibuat lewat dashboard.
 * Foto sumber ada di database/seeders/images dan disalin ke storage saat seeding.
 */
class RealProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->products() as $i => $p) {
            $product = Product::firstOrNew(['name' => $p['name']]);

            if ($product->exists) {
                // Sudah ada: jangan timpa. Hanya lengkapi galeri jika masih kosong.
                if (empty($product->gallery) && ! empty($p['gallery'])) {
                    $product->gallery = $this->storeGallery($p['gallery']);
                    $product->save();
                }

                continue;
            }

            $source   = database_path("seeders/images/{$p['slug']}.jpg");
            $path     = "products/{$p['slug']}.jpg";
            $hasImage = File::exists($source);

            if ($hasImage) {
                Storage::disk('public')->put($path, File::get($source));
            }

            $product->fill([
                'price'       => $p['price'],
                'category'    => $p['category'],
                'sizes'       => ['All Size'],
                'colors'      => $p['colors'],
                'image_url'   => $hasImage ? $path : null,
                'gallery'     => $this->storeGallery($p['gallery'] ?? []),
                'description' => implode("\n", array_merge([$p['intro'], '', 'Detail ukuran'], $p['detail'])),
                'is_active'   => $p['active'] ?? true,
            ]);

            // Urutan katalog mengikuti urutan daftar ini (paling atas = paling baru)
            $product->created_at = now()->subMinutes($i);
            $product->save();
        }
    }

    /** Salin foto tambahan dari database/seeders/images ke storage, kembalikan path-nya. */
    private function storeGallery(array $slugs): array
    {
        $paths = [];

        foreach ($slugs as $slug) {
            $source = database_path("seeders/images/{$slug}.jpg");

            if (! File::exists($source)) {
                continue;
            }

            $path = "products/{$slug}.jpg";
            Storage::disk('public')->put($path, File::get($source));
            $paths[] = $path;
        }

        return $paths;
    }

    private function products(): array
    {
        return [
            [
                'slug' => 'university-top', 'name' => 'University Top', 'price' => 130000, 'category' => 'Rajut',
                'colors' => ['White', 'Cream', 'Pink'],
                'intro'  => 'Atasan model vest rajut V-neck dengan kerah kemeja dan lengan puff. Aksen garis pink dan biru serta emblem university di dada.',
                'detail' => ['Lingkar dada: 90-120 cm', 'Panjang baju: 55 cm', 'Panjang lengan: 25 cm'],
                'gallery' => ['university-top-2'],
            ],
            [
                'slug' => 'stripe-button-top', 'name' => 'Stripe Button Top', 'price' => 95000, 'category' => 'Rajut',
                'colors' => ['White', 'Cream', 'Brown', 'Pink'],
                'intro'  => 'Atasan rajut tanpa lengan bermotif garis hitam dan putih, dengan deretan kancing di bagian depan dan saku kecil di bawah.',
                'detail' => ['Lingkar dada: 70-110 cm', 'Panjang baju: 45 cm'],
            ],
            [
                'slug' => 'ribbon-flip-top', 'name' => 'Ribbon Flip Top', 'price' => 125000, 'category' => 'Rajut',
                'colors' => ['Blue', 'White', 'Pink'],
                'intro'  => 'Atasan lengan pendek dengan motif pita di bagian depan dan tampilan berlapis yang manis.',
                'detail' => ['Lingkar dada: 75-110 cm', 'Panjang baju: 49 cm', 'Panjang lengan: 16 cm'],
            ],
            [
                'slug' => 'flowerry-dress', 'name' => 'Flowerry Dress', 'price' => 80000, 'category' => 'Dress',
                'colors' => ['Navy', 'Blue', 'Black'],
                'intro'  => 'Dress tali spageti model wrap dengan potongan V-neck dan motif bunga kecil.',
                'detail' => ['Lingkar dada: 72-88 cm', 'Panjang baju: 59 cm'],
            ],
            [
                'slug' => 'polka-belt-skirt', 'name' => 'Polka Belt Skirt', 'price' => 125000, 'category' => 'Rok',
                'colors' => ['Pink', 'Cream', 'Beige', 'Black'],
                'intro'  => 'Rok mini motif polka dengan lapisan lipit di bagian bawah, lengkap dengan ikat pinggang.',
                'detail' => ['Lingkar pinggang: 60-78 cm', 'Panjang rok: 40 cm'],
            ],
            [
                'slug' => 'layer-pretty-skirt', 'name' => 'Layer Pretty Skirt', 'price' => 135000, 'category' => 'Rok',
                'colors' => ['White', 'Brown', 'Black'],
                'intro'  => 'Rok mini berlapis dua dengan pinggang karet yang nyaman dan tampilan ruffle yang manis.',
                'detail' => ['Lingkar pinggang: 60-110 cm', 'Panjang rok: 35 cm'],
            ],
            [
                // Di Instagram berstatus SOLD, jadi disembunyikan. Aktifkan lewat dashboard jika restock.
                'slug' => 'cutie-bkk-tanktop', 'name' => 'Cutie BKK Tanktop', 'price' => 95000, 'category' => 'Tanktop',
                'colors' => ['Cream'], 'active' => false,
                'intro'  => 'Tanktop motif bunga lembut dengan lengan ruffle, aksen pita pink di bagian depan, dan lis renda di bagian bawah.',
                'detail' => ['Lingkar dada: 58-64 cm', 'Panjang baju: 31 cm'],
            ],
        ];
    }
}
