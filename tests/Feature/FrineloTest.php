<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FrineloTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite(); // tes tidak bergantung pada npm run build
    }

    private function produk(array $o = []): Product
    {
        return Product::create(array_merge([
            'name' => 'Tes Tanktop',
            'price' => 55000,
            'category' => 'Tanktop',
            'sizes' => ['All Size'],
            'colors' => ['Pink', 'Cream'],
            'image_url' => null,
            'description' => 'Produk uji',
            'is_active' => true,
        ], $o));
    }

    private function admin(): User
    {
        return User::factory()->create();
    }

    private function dataValid(array $o = []): array
    {
        return array_merge([
            'name' => 'Princess Tanktop',
            'price' => 60000,
            'category' => 'Tanktop',
            'sizes' => ['All Size'],
            'colors' => 'Pink, Cream',
            'description' => 'Manis',
            'is_active' => true,
        ], $o);
    }

    public function test_katalog_hanya_menampilkan_produk_aktif(): void
    {
        $this->produk(['name' => 'Tampil']);
        $this->produk(['name' => 'Disembunyikan', 'is_active' => false]);

        $this->get('/')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Catalog/Index')
                ->has('products', 1)
                ->where('products.0.name', 'Tampil')
            );
    }

    public function test_nomor_whatsapp_berformat_internasional(): void
    {
        // harus 62..., tanpa +, tanpa 0 di depan
        $this->assertMatchesRegularExpression(
            '/^62\d{8,13}$/',
            (string) config('frinelo.whatsapp_number')
        );
    }

    public function test_tamu_tidak_bisa_membuka_dashboard_admin(): void
    {
        $this->get('/admin/products')->assertRedirect('/login');
        $this->post('/admin/products', $this->dataValid())->assertRedirect('/login');
        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_bisa_membuka_daftar_produk(): void
    {
        $this->produk();

        $this->actingAs($this->admin())
            ->get('/admin/products')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Products/Index')
                ->has('products', 1)
            );
    }

    public function test_admin_bisa_menambah_produk_dan_warna_dipecah_jadi_array(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', $this->dataValid())
            ->assertRedirect(route('admin.products.index'));

        $p = Product::firstOrFail();
        $this->assertSame('Princess Tanktop', $p->name);
        $this->assertSame(60000, $p->price);
        $this->assertSame(['Pink', 'Cream'], $p->colors);
        $this->assertSame(['All Size'], $p->sizes);
    }

    public function test_validasi_menolak_data_kosong(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', [])
            ->assertSessionHasErrors(['name', 'price', 'category', 'sizes', 'colors']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_ukuran_di_luar_daftar_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', $this->dataValid(['sizes' => ['XXXL']]))
            ->assertSessionHasErrors('sizes.0');
    }

    public function test_harga_negatif_ditolak(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/products', $this->dataValid(['price' => -1]))
            ->assertSessionHasErrors('price');
    }

    public function test_admin_bisa_mengubah_produk(): void
    {
        $p = $this->produk();

        $this->actingAs($this->admin())
            ->put(route('admin.products.update', $p), $this->dataValid(['name' => 'Nama Baru', 'price' => 70000]))
            ->assertRedirect(route('admin.products.index'));

        $this->assertSame('Nama Baru', $p->fresh()->name);
        $this->assertSame(70000, $p->fresh()->price);
    }

    public function test_upload_foto_tersimpan_dan_terhapus_saat_produk_dihapus(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/admin/products', $this->dataValid([
                'image' => UploadedFile::fake()->image('foto.jpg', 600, 800),
            ]))
            ->assertRedirect(route('admin.products.index'));

        $p = Product::firstOrFail();
        $this->assertStringStartsWith('products/', $p->image_url);
        Storage::disk('public')->assertExists($p->image_url);

        $this->actingAs($admin)->delete(route('admin.products.destroy', $p))->assertRedirect();

        Storage::disk('public')->assertMissing($p->image_url);
        $this->assertDatabaseCount('products', 0);
    }

    public function test_switch_tampil_sembunyi_membalik_status(): void
    {
        $p = $this->produk(['is_active' => true]);

        $this->actingAs($this->admin())
            ->patch(route('admin.products.toggle', $p))
            ->assertRedirect();

        $this->assertFalse($p->fresh()->is_active);
    }

    public function test_image_src_untuk_url_penuh_dan_file_upload(): void
    {
        $a = $this->produk(['image_url' => 'https://placehold.co/600x800.png']);
        $this->assertSame('https://placehold.co/600x800.png', $a->image_src);

        $b = $this->produk(['image_url' => 'products/abc.jpg']);
        $this->assertStringEndsWith('/storage/products/abc.jpg', $b->image_src);

        $c = $this->produk(['image_url' => null]);
        $this->assertNull($c->image_src);
    }

    public function test_seeder_mengisi_7_produk_frinelo(): void
    {
        $this->seed(ProductSeeder::class);

        $this->assertDatabaseCount('products', 7);
        $this->assertSame(
            ['Cardigan', 'Celana', 'Rajut', 'Rok', 'Tanktop'],
            Product::pluck('category')->unique()->sort()->values()->all()
        );

        // semua ukuran di seeder harus ada di daftar yang diterima validasi
        foreach (Product::all() as $p) {
            $this->assertSame([], array_diff($p->sizes, Product::SIZES));
        }
    }
}