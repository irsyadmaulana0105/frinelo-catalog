# Arsitektur

## Gambaran umum

```
TikTok / Instagram (caption, bio, komentar)
        |  link: https://domain/  atau  https://domain/?p=ID
        v
+--------------------------------------------+
| Katalog publik  (Vue + Inertia)            |
|  - hero, filter, pencarian, grid produk    |
|  - modal detail: pilih ukuran & warna      |
|  - tombol "Pesan via WhatsApp"             |
+---------------+----------------------------+
                | buka wa.me/<nomor>?text=<pesan>
                v
          WhatsApp Admin Frinelo

Admin toko --login--> Dashboard (Vue + Inertia) --> Laravel --> MySQL + storage foto
```

Prinsip desain:

- **Tanpa keranjang, pembayaran, dan akun pembeli.** Tujuannya mengurangi chat
  berulang tentang ukuran dan warna.
- **Satu halaman publik.** Semua produk aktif dikirim sekali dari server;
  filter, pencarian, dan modal berjalan di sisi klien, jadi terasa instan di HP.
- **Server-driven.** Inertia membuat Laravel mengirim data langsung sebagai
  props ke komponen Vue, tanpa API JSON terpisah.

## Backend (Laravel)

| File | Tanggung jawab |
|---|---|
| `CatalogController@index` | Produk `is_active = true`, dikirim ke `Catalog/Index` bersama `categories`, `whatsappNumber`, `shop` |
| `Admin\ProductController` | CRUD produk, upload foto, `toggle` tampil/sembunyi |
| `ProductRequest` | Validasi; mengubah teks warna "Hitam, Putih" menjadi array |
| `Product` (model) | Cast JSON, accessor `image_src`, hapus file foto lama |
| `config/frinelo.php` | Nomor WhatsApp, Instagram, alamat, jam buka, tagline |
| `HandleInertiaRequests` | Membagikan `auth.user` dan `flash.success` ke semua halaman |

## Route

| Method | URL | Nama | Akses |
|---|---|---|---|
| GET | `/` | `catalog` | Publik |
| GET | `/dashboard` | `dashboard` | Login; redirect ke daftar produk |
| GET | `/admin/products` | `admin.products.index` | Login |
| GET | `/admin/products/create` | `admin.products.create` | Login |
| POST | `/admin/products` | `admin.products.store` | Login |
| GET | `/admin/products/{id}/edit` | `admin.products.edit` | Login |
| PUT | `/admin/products/{id}` | `admin.products.update` | Login |
| DELETE | `/admin/products/{id}` | `admin.products.destroy` | Login |
| PATCH | `/admin/products/{id}/toggle` | `admin.products.toggle` | Login |
| - | `/login`, `/logout`, dst. | dari `routes/auth.php` | Breeze |

`/register` **dinonaktifkan** (dikomentari di `routes/auth.php`). Route
`toggle` harus dideklarasikan sebelum `Route::resource`.

## Frontend (Vue)

Halaman katalog (`Pages/Catalog/Index.vue`) merakit komponen di
`Components/Catalog/`:

| Komponen | Fungsi |
|---|---|
| `AnnouncementBar` | Bar pink dengan teks berjalan (animasi CSS, mati jika pengguna memilih reduced motion) |
| `HeroSection` | Judul, tombol, dan dua produk berfoto dalam bingkai lengkung (`.arch`) |
| `HowToOrder` | Strip tiga langkah cara pesan |
| `ProductCard` | Kartu: foto 3:4, label Baru, bulatan warna, harga, ukuran |
| `ProductModal` | Detail, pilihan ukuran/warna, tombol WhatsApp menempel di bawah, salin link |
| `SiteFooter` | Alamat (link Google Maps), jam buka, Instagram, WhatsApp |
| `WhatsAppFab` | Tombol chat admin mengambang |
| `Icon` | Ikon garis inline (tanpa library tambahan) |

Dashboard admin: `Pages/Admin/Products/{Index,Form}.vue` dengan
`Layouts/AdminLayout.vue` (header, toast sukses). Login memakai
`Layouts/GuestLayout.vue`.

Helper: `lib/format.js` (`rupiah`, `isNew`, `waLink`) dan `lib/colors.js`
(nama warna ke kode warna untuk bulatan swatch).

## Alur pesan WhatsApp

1. Pembeli klik produk, modal terbuka.
2. Tombol WhatsApp nonaktif sampai ukuran **dan** warna dipilih
   (jika hanya ada satu pilihan, otomatis terpilih, mis. All Size).
3. Klik tombol membuka `https://wa.me/<FRINELO_WHATSAPP>?text=<pesan ter-encode>`.

Format pesan:

```
Halo Admin Frinelo, saya mau pesan produk ini:
- Produk: <nama>
- Ukuran: <ukuran>
- Warna: <warna>
- Harga: <Rp ...>
- Link: <url produk>        (konstanta INCLUDE_LINK di ProductModal.vue)
Apakah stoknya masih ada?
```

## Deep link produk

`/?p=ID` membuka modal produk tersebut saat halaman dimuat. URL diperbarui
dengan `history.replaceState` saat modal dibuka atau ditutup. Dashboard punya
tombol "Salin link" yang menyalin URL ini. Label "Baru" dihitung dari
`created_at` (14 hari terakhir), tanpa kolom tambahan.

## Penyimpanan foto

- Upload admin disimpan di disk `public`, folder `products/`
  (`storage/app/public/products`); kolom `image_url` berisi path relatif.
- `image_url` juga boleh berisi URL penuh (`http...`), dipakai data contoh.
- Accessor `image_src` memilih mana yang dipakai; file lama ikut terhapus
  saat foto diganti atau produk dihapus.

## Desain

| Aspek | Nilai |
|---|---|
| Font judul | Cormorant Garamond (`.font-display`, termasuk huruf miring) |
| Font isi | Jost |
| Warna dasar | Putih, teks `stone-800` |
| Aksen | `rose-400` (katalog dan dashboard) |
| Tombol WhatsApp | `emerald-500` (warna yang dikenali pengguna) |
| Bingkai | `.arch` (lengkung atas) di `resources/css/frinelo.css` |
| Gambar | Rasio portrait 3:4 |
| Grid | 2 kolom di HP, 3 di tablet, 4 di desktop |

`frinelo.css` berisi CSS biasa (tanpa direktif Tailwind) dan diimpor dari
`resources/js/app.js`, sehingga aman untuk Tailwind v3 maupun v4.

## Menguji dan membagikan

- **Di laptop:** `npm run dev` + `php artisan serve`.
- **Dari HP (WiFi sama):** `npm run build` + `php artisan serve --host=0.0.0.0`,
  buka `http://IP-LAPTOP:8000`, butuh aturan firewall port 8000.
- **Demo publik sementara:** tunnel (ngrok, Cloudflare Tunnel). Perlu
  `npm run build` dan `$middleware->trustProxies(at: '*')` di `bootstrap/app.php`.
- **Produksi:** hosting dengan domain tetap.

## Keamanan

- Semua route `/admin/*` dilindungi middleware `auth`.
- Pendaftaran publik dimatikan; akun admin dibuat manual lewat tinker.
- Password admin bawaan sudah diganti; gunakan password kuat dan unik.
- Upload dibatasi tipe gambar dan maksimal 2 MB.
- Produksi: `APP_DEBUG=false`, `APP_ENV=production`, HTTPS.
- Jangan membagikan link tunnel sebelum password diganti dan register dimatikan.

## Batasan yang disengaja (versi awal)

- Semua produk dimuat sekaligus (`->get()`); cukup untuk puluhan hingga
  beberapa ratus produk.
- Tidak ada stok per varian: pembeli menanyakan stok lewat WhatsApp.
- Satu foto per produk, satu peran admin.
