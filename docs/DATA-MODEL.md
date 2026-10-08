# Model Data

Database: **MySQL**, nama `frinelo`. Tabel utama proyek ini: `products` dan
`users`. Tabel lain (`cache`, `jobs`, `sessions`, dst.) bawaan Laravel.

## Tabel `products`

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint, PK | tidak | auto | Dipakai di deep link `/?p=ID` |
| `name` | string | tidak | - | Maks 120 karakter |
| `price` | unsigned integer | tidak | - | Rupiah bulat (60000, bukan 60.000,00) |
| `category` | string | tidak | - | Teks bebas: Tanktop, Rajut, Cardigan, Rok, Celana |
| `sizes` | json | tidak | - | Array, mis. `["All Size"]` atau `["S","M","L"]` |
| `colors` | json | tidak | - | Array, mis. `["Pink","Cream"]` |
| `image_url` | string | ya | null | URL penuh **atau** path relatif disk `public` (`products/xxx.jpg`) |
| `description` | text | ya | null | Maks 2000 karakter |
| `is_active` | boolean | tidak | `true` | `false` = tersembunyi dari katalog (draft) |
| `created_at`, `updated_at` | timestamp | ya | - | Urutan katalog dari `created_at` terbaru; label **Baru** = 14 hari terakhir |

## Model `App\Models\Product`

- `SIZES` = `['All Size', 'S', 'M', 'L', 'XL']`, satu-satunya daftar ukuran
  yang diterima validasi dan ditampilkan di form admin.
- Cast: `sizes` dan `colors` ke array, `price` ke integer, `is_active` ke boolean.
- `$appends = ['image_src']`: frontend memakai `image_src`, **bukan** `image_url`.
- `deleteStoredImage()`: menghapus file di disk `public` hanya jika
  `image_url` berupa path lokal.

## Aturan validasi (`ProductRequest`)

| Field | Aturan |
|---|---|
| `name` | wajib, string, maks 120 |
| `price` | wajib, integer, min 0 |
| `category` | wajib, string, maks 60 |
| `sizes` | wajib, array minimal 1; tiap isi harus ada di `Product::SIZES` |
| `colors` | wajib, array minimal 1; tiap isi maks 30 karakter |
| `description` | opsional, maks 2000 |
| `image` | opsional, harus gambar, maks 2048 KB |
| `is_active` | boolean |

Jika `colors` dikirim sebagai teks dipisah koma, `prepareForValidation()`
mengubahnya menjadi array.

## Tabel `users`

Bawaan Laravel/Breeze, dipakai hanya untuk login admin. Akun dibuat manual
lewat tinker; pendaftaran publik dimatikan.

## Data contoh (`ProductSeeder`)

Tujuh produk bergaya Frinelo. **Harga adalah perkiraan** dari screenshot
Instagram dan perlu dikoreksi:

| Nama | Harga | Kategori | Ukuran | Warna |
|---|---|---|---|---|
| Princess Tanktop | 60000 | Tanktop | All Size | Pink, Cream |
| Basic Polka Tanktop | 50000 | Tanktop | All Size | Putih, Hitam |
| Floral Lace Tanktop | 50000 | Tanktop | All Size | Putih, Hitam |
| Knit Polo Top | 85000 | Rajut | All Size | Biru, Hitam, Abu |
| Heart Knit Cardigan | 115000 | Cardigan | All Size | Navy, Cream |
| Polka Mini Skirt | 95000 | Rok | S, M, L | Cream, Hitam |
| Tie Pocket Pants | 119000 | Celana | S, M, L | Putih, Hitam |

Seeder dijalankan **sekali**. Untuk mengosongkan produk contoh:

```powershell
php artisan tinker --execute="App\Models\Product::truncate();"
```

## Peringatan migration

- Pastikan `0001_01_01_000000_create_users_table.php` berisi
  `Schema::create('users'` (bukan `'products'`). Pernah tertimpa saat menempel
  kode, akibatnya tabel `users` tidak terbentuk.
- `php artisan migrate:fresh` menghapus semua tabel di database aktif. Aman
  hanya selagi data masih contoh.
- Angka baris di `php artisan db:show --counts` hanya perkiraan MySQL; hitung
  data dengan `Product::count()`.

## Konvensi

- Harga disimpan sebagai integer; format "Rp 60.000" hanya di tampilan.
- Kategori adalah teks bebas; filter katalog dibentuk otomatis dari kategori
  produk aktif. Jaga ejaan konsisten ("Tanktop", bukan "Tank top").
- Produk tersembunyi tetap ada di dashboard tetapi tidak dikirim ke halaman publik.
- Nama warna dipetakan ke kode warna swatch di `resources/js/lib/colors.js`;
  warna yang tidak dikenal tampil abu-abu muda.

## Rencana perubahan skema

**Stok per varian:**

```
product_variants
  id, product_id (FK, cascade), size, color, stock (int), timestamps
  unique (product_id, size, color)
```

**Multi-foto:** `product_images` (`id`, `product_id`, `path`, `sort_order`).

**Pencatatan klik WhatsApp:** `whatsapp_clicks` (`id`, `product_id`, `size`,
`color`, `created_at`).

**Label produk:** kolom `badge` (mis. "Best Seller") dan `compare_price`
(harga coret).
