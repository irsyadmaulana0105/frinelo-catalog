# Model Data

## Tabel `products`

| Kolom | Tipe | Null | Default | Keterangan |
|---|---|---|---|---|
| `id` | bigint, PK | tidak | auto | Dipakai di deep link `/?p=ID` |
| `name` | string | tidak | - | Nama produk, maks 120 karakter |
| `price` | unsigned integer | tidak | - | Rupiah bulat tanpa desimal (60000, bukan 60.000,00) |
| `category` | string | tidak | - | Teks bebas: Tanktop, Rajut, Cardigan, Rok, Celana |
| `sizes` | json | tidak | - | Array, contoh `["All Size"]` atau `["S","M","L"]` |
| `colors` | json | tidak | - | Array, contoh `["Pink","Cream"]` |
| `image_url` | string | ya | null | URL penuh **atau** path relatif disk `public` (`products/xxx.jpg`) |
| `description` | text | ya | null | Maks 2000 karakter |
| `is_active` | boolean | tidak | `true` | `false` = tersembunyi dari katalog (draft) |
| `created_at`, `updated_at` | timestamp | ya | - | Katalog diurutkan dari `created_at` terbaru |

## Model `App\Models\Product`

- `SIZES` = `['All Size', 'S', 'M', 'L', 'XL']`, satu-satunya daftar ukuran
  yang diterima validasi dan ditampilkan di form admin. Menambah ukuran
  baru (misalnya XXL) cukup di sini.
- Cast: `sizes` dan `colors` ke array, `price` ke integer, `is_active` ke boolean.
- `$appends = ['image_src']`: atribut turunan yang selalu ikut terkirim ke Vue.
  Frontend memakai `image_src`, **bukan** `image_url`.
- `deleteStoredImage()`: menghapus file di disk `public` hanya jika
  `image_url` berupa path lokal (bukan URL `http`).

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
mengubahnya menjadi array (trim dan buang yang kosong).

## Tabel `users`

Bawaan Laravel/Breeze. Dipakai hanya untuk login admin. Akun dibuat manual
(seeder atau tinker). Pendaftaran publik harus dimatikan.

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

Seeder dijalankan **sekali**; menjalankannya dua kali membuat produk dobel.
Kosongkan dengan:

```powershell
php artisan tinker --execute="App\Models\Product::truncate();"
```

## Konvensi

- Harga disimpan sebagai integer; format "Rp 60.000" hanya di tampilan
  (`lib/format.js`).
- Kategori adalah teks bebas. Daftar filter di katalog dibentuk otomatis dari
  kategori produk yang aktif, jadi mengetik kategori baru langsung muncul
  sebagai filter. Jaga konsistensi ejaan ("Tanktop", bukan "Tank top").
- Produk yang disembunyikan (`is_active = false`) tetap ada di dashboard
  tetapi tidak dikirim ke halaman publik.

## Rencana perubahan skema

**Stok per varian** (jika nanti ingin ukuran/warna habis tidak bisa dipilih):

```
product_variants
  id, product_id (FK, cascade), size, color, stock (int), timestamps
  unique (product_id, size, color)
```

`sizes` dan `colors` di tabel `products` bisa tetap ada sebagai ringkasan,
atau diturunkan dari varian.

**Multi-foto:**

```
product_images
  id, product_id (FK, cascade), path, sort_order, timestamps
```

**Pencatatan klik WhatsApp** (untuk tahu produk paling diminati):

```
whatsapp_clicks
  id, product_id (FK), size, color, created_at
```

**Label produk:** kolom `is_new` (boolean), `badge` (string, mis. "Best Seller"),
`compare_price` (untuk harga coret).
