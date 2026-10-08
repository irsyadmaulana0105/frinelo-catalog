# Frinelo Smart Catalog

Katalog etalase digital untuk **Toko Frinelo** (Sidoarjo). Fungsinya sebagai
**jembatan dari TikTok/Instagram ke WhatsApp admin**: pembeli melihat koleksi,
memilih ukuran dan warna, lalu pesan lewat WhatsApp dengan teks pesanan yang
sudah tersusun otomatis. Tidak ada keranjang belanja, payment gateway, atau
akun untuk pembeli.

> Catatan status dan langkah lanjutan ada di [`docs/README.md`](docs/README.md).

## Fitur

- Katalog publik bergaya butik pink feminin, responsif di HP
- Hero dengan bingkai lengkung (terinspirasi cermin pink di toko), bar teks berjalan
- Filter kategori yang menempel saat scroll, pencarian, urutan harga
- Kartu produk: label **Baru** otomatis (14 hari), bulatan warna, ukuran
- Modal detail: pilih ukuran dan warna, tombol **Pesan via WhatsApp** dengan pesan otomatis
- Link langsung per produk: `https://domain/?p=ID` (untuk caption TikTok/IG)
- Dashboard admin (login): tambah, edit, hapus produk, upload foto,
  switch tampil/sembunyi, salin link produk, preview kartu

## Tech stack

| Bagian | Teknologi |
|---|---|
| Backend | Laravel (PHP 8.2+) |
| Frontend | Vue 3 + Inertia.js |
| Styling | Tailwind CSS |
| Auth admin | Laravel Breeze (Vue); pendaftaran publik dimatikan |
| Database | **MySQL** (database `frinelo`) |
| Build tool | Vite |

## Prasyarat

PHP 8.2+, Composer, Node 18+, MySQL (XAMPP atau Laragon).

## Instalasi (Windows PowerShell)

1. Nyalakan MySQL, lalu buat database kosong bernama `frinelo`
   (phpMyAdmin, atau biarkan `php artisan migrate` menawarkan membuatnya).
2. Siapkan proyek:

```powershell
composer install
npm install
copy .env.example .env
php artisan key:generate
```

3. Atur bagian database di `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=frinelo
DB_USERNAME=root
DB_PASSWORD=

FRINELO_WHATSAPP=6281359933771
```

`FRINELO_WHATSAPP` memakai format internasional, tanpa `+` dan tanpa `0` di
depan. Nomor ini perlu dikonfirmasi dulu sebagai nomor penerima pesanan.

4. Buat tabel, isi data contoh, dan siapkan penyimpanan foto:

```powershell
php artisan migrate
php artisan db:seed --class=ProductSeeder
php artisan storage:link
```

Jalankan seeder **sekali saja** (menjalankannya dua kali membuat produk dobel).

5. Buat akun admin (ganti email dan password sesuai kebutuhan):

```powershell
php artisan tinker --execute="App\Models\User::updateOrCreate(['email'=>'admin@frinelo.test'], ['name'=>'Admin Frinelo','password'=>bcrypt('GANTI-DENGAN-PASSWORD-KUAT')]);"
```

Pendaftaran publik (`/register`) dimatikan, jadi akun admin hanya dibuat
dengan cara ini.

> `php artisan migrate:fresh` **menghapus semua data** di database. Pakai
> hanya selagi datanya masih contoh.

## Menjalankan

```powershell
composer run dev
```

Atau dua terminal yang dibiarkan terbuka:

```powershell
npm run dev          # terminal 1
php artisan serve    # terminal 2
```

| Alamat | Isi |
|---|---|
| http://127.0.0.1:8000 | Katalog publik |
| http://127.0.0.1:8000/?p=1 | Langsung buka detail produk #1 |
| http://127.0.0.1:8000/login | Login admin |
| http://127.0.0.1:8000/admin/products | Dashboard produk |

Buka alamat dari `php artisan serve` (port 8000), bukan port Vite (5173).

## Menguji dari HP (satu WiFi)

Saat menguji lewat HP, hentikan `npm run dev` dan pakai hasil build:

```powershell
Remove-Item public\hot -ErrorAction SilentlyContinue
npm run build
php artisan serve --host=0.0.0.0 --port=8000
```

Buka `http://IP-LAPTOP:8000` di HP (cari IP dengan `ipconfig | findstr IPv4`,
pakai yang dari adapter Wi-Fi). Windows perlu aturan firewall untuk port 8000
(PowerShell sebagai Administrator, lihat `docs/README.md`). Setelah selesai,
kembali ke `npm run dev`.

## Struktur singkat

```
app/Http/Controllers/CatalogController.php        halaman publik
app/Http/Controllers/Admin/ProductController.php  CRUD admin + toggle
app/Http/Requests/ProductRequest.php              validasi produk
app/Models/Product.php
config/frinelo.php                                nomor WA dan info toko
database/migrations/*_create_products_table.php
database/seeders/ProductSeeder.php
resources/css/frinelo.css                         gaya khusus katalog
resources/js/Pages/Catalog/Index.vue              halaman katalog
resources/js/Pages/Admin/Products/{Index,Form}.vue
resources/js/Components/Catalog/*.vue             hero, kartu, modal, footer, dst.
resources/js/Layouts/{AdminLayout,GuestLayout}.vue
resources/js/lib/{format,colors}.js
routes/web.php
```

## Produksi (ringkas)

```powershell
npm run build
```

Set `APP_ENV=production` dan `APP_DEBUG=false`, isi `FRINELO_WHATSAPP` dengan
nomor asli, jalankan `php artisan storage:link` di server, pastikan route
`register` tetap nonaktif, dan gunakan password admin yang kuat.
