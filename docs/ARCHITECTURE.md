# Arsitektur

## Gambaran umum

```
TikTok / Instagram (caption, bio, komentar)
        │  link: https://domain/  atau  https://domain/?p=ID
        ▼
┌────────────────────────────────────────────┐
│ Katalog publik  (Vue + Inertia)            │
│  • grid produk + filter kategori           │
│  • modal detail: pilih ukuran & warna      │
│  • tombol "Pesan via WhatsApp"             │
└───────────────┬────────────────────────────┘
                │ buka wa.me/<nomor>?text=<pesan>
                ▼
          WhatsApp Admin Frinelo

Admin toko ──login──▶ Dashboard (Vue + Inertia) ──▶ Laravel ──▶ DB + storage foto
```

Prinsip desain:

- **Tanpa keranjang dan pembayaran.** Tujuannya mengurangi chat berulang
  tentang ukuran dan warna.
- **Satu halaman publik.** Semua produk dikirim sekali dari server; filter
  kategori dan modal detail berjalan di sisi klien, jadi terasa instan di HP.
- **Server-driven.** Inertia membuat Laravel mengirim data langsung sebagai
  props ke komponen Vue, tanpa API JSON terpisah.

## Backend (Laravel)

| File | Tanggung jawab |
|---|---|
| `CatalogController@index` | Mengambil produk `is_active = true`, kirim ke `Catalog/Index` bersama `categories`, `whatsappNumber`, `shop` |
| `Admin\ProductController` | CRUD produk, upload foto, `toggle` tampil/sembunyi |
| `ProductRequest` | Validasi; mengubah teks warna "Hitam, Putih" menjadi array |
| `Product` (model) | Cast JSON, accessor `image_src`, hapus file foto lama |
| `config/frinelo.php` | Nomor WhatsApp, Instagram, alamat, jam buka |
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

Route `toggle` harus dideklarasikan **sebelum** `Route::resource` di grup admin.

## Frontend (Vue)

| File | Fungsi |
|---|---|
| `Pages/Catalog/Index.vue` | Halaman utama: header, filter, grid, footer, membuka modal, membaca `?p=ID` |
| `Components/Catalog/ProductCard.vue` | Kartu produk (foto 3:4, nama, harga) |
| `Components/Catalog/ProductModal.vue` | Detail produk, pilihan ukuran/warna, tombol WhatsApp |
| `Pages/Admin/Products/Index.vue` | Ringkasan, pencarian, filter status, switch tampil, salin link, edit, hapus |
| `Pages/Admin/Products/Form.vue` | Form tambah/edit, chip warna, preview kartu |
| `Layouts/AdminLayout.vue` | Header admin, toast notifikasi sukses |
| `Layouts/GuestLayout.vue` | Tampilan halaman login |
| `lib/format.js` | `rupiah()` untuk format "Rp 189.000" |

## Alur pesan WhatsApp

1. Pembeli klik produk, modal terbuka.
2. Tombol WhatsApp nonaktif sampai ukuran **dan** warna dipilih
   (jika hanya ada satu pilihan, otomatis terpilih, contoh: All Size).
3. Klik tombol membuka `https://wa.me/<FRINELO_WHATSAPP>?text=<pesan ter-encode>`.

Format pesan:

```
Halo Admin Frinelo, saya mau pesan produk ini:
- Produk: <nama>
- Ukuran: <ukuran>
- Warna: <warna>
- Harga: <Rp ...>
Apakah stoknya masih ada?
```

Opsional: tambah baris `- Link: ${location.origin}/?p=${product.id}` agar
admin bisa membuka produknya langsung.

## Deep link produk

`/?p=ID` membuka modal produk tersebut saat halaman dimuat. Saat modal dibuka
atau ditutup, URL diperbarui dengan `history.replaceState`. Dashboard punya
tombol "Salin link" yang menyalin URL ini.

## Penyimpanan foto

- Upload admin disimpan di disk `public`, folder `products/`
  (`storage/app/public/products`), nilai kolom `image_url` berisi path relatif.
- Kolom `image_url` juga boleh berisi URL penuh (`http...`), dipakai oleh data
  contoh (placehold.co).
- Accessor `image_src` memilih mana yang dipakai: URL penuh dipakai apa
  adanya, path relatif diubah menjadi `asset('storage/...')`.
- Saat produk dihapus atau foto diganti, file lama ikut dihapus.

## Desain

| Aspek | Nilai |
|---|---|
| Font judul | Cormorant Garamond (`.font-display`) |
| Font isi | Jost |
| Warna dasar | Putih, teks `stone-800` |
| Aksen | `rose-400` (dashboard sudah pink; katalog publik masih `stone-900` pada pilihan aktif, bisa diganti ke rose) |
| Latar admin | `rose-50/40` |
| Gambar | Rasio portrait 3:4 |
| Grid | 2 kolom di HP, 3 di tablet, 4 di desktop |

Font dimuat dari Google Fonts lewat `resources/views/app.blade.php`;
kelas font didefinisikan di bawah `resources/css/app.css`.

## Keamanan

- Semua route `/admin/*` dilindungi middleware `auth`.
- **Route `register` Breeze harus dinonaktifkan** (komentari di `routes/auth.php`),
  jika tidak siapa pun bisa mendaftar menjadi admin.
- Ganti password admin bawaan seeder/tinker.
- Upload dibatasi tipe gambar dan maksimal 2 MB (`ProductRequest`).
- Produksi: `APP_DEBUG=false`, `APP_ENV=production`, HTTPS.

## Batasan yang disengaja (versi awal)

- Semua produk dimuat sekaligus (`->get()`); cukup untuk puluhan hingga
  beberapa ratus produk. Jika lebih, tambahkan pagination atau pencarian server.
- Tidak ada stok per varian: pembeli diminta menanyakan stok lewat WhatsApp.
- Satu foto per produk.
- Satu admin umum, tanpa peran/izin berbeda.
