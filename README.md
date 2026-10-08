# Frinelo Smart Catalog

Katalog etalase digital untuk **Toko Frinelo** (Sidoarjo). Fungsinya sebagai
**jembatan dari TikTok/Instagram ke WhatsApp admin**: pembeli melihat koleksi,
memilih ukuran dan warna, lalu pesan lewat WhatsApp dengan teks pesanan yang
sudah tersusun otomatis. Tidak ada keranjang belanja atau payment gateway.

> Dokumentasi lengkap ada di folder [`docs/`](docs/README.md).
> Mulai dari `docs/README.md` untuk status terakhir dan langkah lanjutan.

## Fitur

- Katalog publik bergaya butik (putih bersih, aksen pink), responsif di HP
- Filter kategori (Tanktop, Rajut, Cardigan, Rok, Celana, dst.)
- Modal detail produk: pilih ukuran dan warna secara interaktif
- Tombol **Pesan via WhatsApp** dengan pesan otomatis (produk, ukuran, warna, harga)
- Link langsung per produk: `https://domain/?p=ID` (untuk caption TikTok/IG)
- Dashboard admin (login): tambah, edit, hapus produk, upload foto,
  tampil/sembunyikan produk, salin link produk

## Tech stack

| Bagian | Teknologi |
|---|---|
| Backend | Laravel (PHP 8.2+) |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS |
| Auth admin | Laravel Breeze (Vue) |
| Database | SQLite (bawaan Laravel), bisa diganti MySQL |
| Build tool | Vite |

## Prasyarat

PHP 8.2+, Composer, Node 18+, npm.

## Instalasi (Windows PowerShell)

```powershell
composer install
npm install
copy .env.example .env        # lewati jika .env sudah ada
php artisan key:generate      # lewati jika .env sudah ada
php artisan migrate
php artisan db:seed --class=ProductSeeder
php artisan storage:link
```

Buat akun admin:

```powershell
php artisan tinker --execute="App\Models\User::updateOrCreate(['email'=>'admin@frinelo.test'], ['name'=>'Admin Frinelo','password'=>bcrypt('ganti-password-ini')]);"
```

Ganti password tersebut segera setelah login pertama.

## Konfigurasi

Di `.env`:

```env
FRINELO_WHATSAPP=6281359933771
```

Format internasional, tanpa `+` dan tanpa `0` di depan. Nomor ini perlu
dipastikan dulu apakah benar nomor WhatsApp yang menerima pesanan.
Info toko lain (Instagram, alamat, jam buka) ada di `config/frinelo.php`.

## Menjalankan

```powershell
composer run dev
```

Atau manual dengan dua terminal yang dibiarkan terbuka:

```powershell
npm run dev          # terminal 1
php artisan serve    # terminal 2
```

| Alamat | Isi |
|---|---|
| http://127.0.0.1:8000 | Katalog publik |
| http://127.0.0.1:8000/login | Login admin |
| http://127.0.0.1:8000/admin/products | Dashboard produk |
| http://127.0.0.1:8000/?p=1 | Langsung buka detail produk #1 |

Buka alamat dari `php artisan serve` (port 8000), bukan port Vite (5173).

## Struktur singkat

```
app/Http/Controllers/CatalogController.php        halaman publik
app/Http/Controllers/Admin/ProductController.php  CRUD admin
app/Http/Requests/ProductRequest.php              validasi produk
app/Models/Product.php
config/frinelo.php                                nomor WA dan info toko
database/migrations/*_create_products_table.php
database/seeders/ProductSeeder.php
resources/js/Pages/Catalog/Index.vue              halaman katalog
resources/js/Pages/Admin/Products/{Index,Form}.vue
resources/js/Components/Catalog/{ProductCard,ProductModal}.vue
resources/js/Layouts/AdminLayout.vue
resources/js/lib/format.js                        format rupiah
routes/web.php
```

## Produksi (ringkas)

```powershell
npm run build
```

Set `APP_ENV=production`, `APP_DEBUG=false`, isi `FRINELO_WHATSAPP` dengan
nomor asli, jalankan `php artisan storage:link` di server, dan **matikan route
register** di `routes/auth.php`.
