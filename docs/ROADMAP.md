# Roadmap

Legenda: `[ ]` belum, `[x]` selesai. Urutan mengikuti prioritas.

## 1. Sekarang: bikin versi pertama benar-benar jalan

- [ ] Tabel `products` terisi (seeder atau lewat dashboard)
- [ ] Akun admin dibuat dan bisa login
- [ ] Pastikan file dashboard admin baru terpasang (`Layouts/AdminLayout.vue`,
      `Pages/Admin/Products/Index.vue` dan `Form.vue`, route `toggle`)
- [ ] `Product::SIZES` berisi "All Size"
- [ ] Uji tambah, edit, hapus, upload foto, switch tampil/sembunyi
- [ ] Uji alur WhatsApp di HP: pilih ukuran dan warna, pesan terisi rapi
- [ ] Konfirmasi nomor WhatsApp penerima pesanan, isi `FRINELO_WHATSAPP`
- [ ] Nonaktifkan route `register` di `routes/auth.php`
- [ ] Ganti password admin bawaan
- [ ] Ganti data contoh dengan produk dan harga asli

## 2. Penyesuaian tampilan Frinelo

- [ ] Terapkan `config/frinelo.php` dan footer info toko (alamat, jam buka, IG, WA)
- [ ] Ganti tagline header menjadi "Little Bangkok Baju · Sidoarjo"
- [ ] Ganti warna aktif katalog dari `stone-900` ke `rose-400`
      (find-and-replace di `Catalog/Index.vue` dan `ProductModal.vue`)
- [ ] Pasang logo "fr." di header katalog, favicon, dan dashboard
      (butuh file logo dari pemilik toko)
- [ ] Cocokkan nama produk, kategori, dan harga dengan akun katalog
      @frinelo.store (butuh screenshot)
- [ ] Tambah baris `- Link:` ke pesan WhatsApp agar admin bisa membuka produknya

## 3. Siap online

- [ ] Pilih hosting dan domain (lihat pertanyaan terbuka)
- [ ] `npm run build`, `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `php artisan storage:link` di server
- [ ] Pasang link katalog di bio Instagram dan TikTok
- [ ] Tes link `/?p=ID` dibuka dari aplikasi Instagram/TikTok di HP
- [ ] Backup rutin database dan folder `storage/app/public/products`

## 4. Pengembangan berikutnya

| Prioritas | Fitur | Catatan |
|---|---|---|
| Tinggi | Label **Baru** dan filter "Baru Datang" | Toko rutin posting "NEW ARRIVAL" |
| Tinggi | Stok per varian | Tabel `product_variants`, ukuran/warna habis tidak bisa dipilih |
| Sedang | Multi-foto per produk | Galeri geser di modal, tabel `product_images` |
| Sedang | Pencatatan klik tombol WhatsApp | Untuk melihat produk paling diminati |
| Sedang | Pencarian produk di katalog | Berguna jika produk sudah banyak |
| Rendah | Harga coret dan label promo | Kolom `compare_price`, `badge` |
| Rendah | Urutan produk manual (drag and drop) | Kolom `sort_order` |
| Rendah | Pagination atau lazy load | Jika produk lebih dari beberapa ratus |
| Rendah | Open Graph / meta tag | Agar link produk tampil dengan foto saat dibagikan |

## Keputusan yang sudah diambil

| Keputusan | Alasan |
|---|---|
| Tanpa keranjang dan payment gateway | Tujuan hanya jembatan ke WhatsApp |
| Laravel Breeze (Vue + Inertia) | Auth, Vue, Inertia, Tailwind langsung siap |
| Filter dan modal di sisi klien | Cepat di HP, tanpa reload |
| Ukuran dibatasi daftar tetap (`Product::SIZES`) | Menjaga data konsisten |
| Kategori teks bebas | Admin bisa menambah kategori tanpa menu khusus |
| Harga integer | Hindari masalah desimal, format hanya di tampilan |
| Foto bisa URL luar atau upload | Data contoh memakai placeholder, produksi memakai upload |
| SQLite untuk awal | Tanpa setup server database |

## Pertanyaan terbuka

1. Apakah 081359933771 benar nomor WhatsApp yang menerima pesanan, atau ada
   nomor admin khusus (di bio IG tertulis link `wa.me/628...`)?
2. Domain dan hosting apa yang akan dipakai? (Perlu PHP 8.2+, mendukung Laravel.)
3. Siapa yang akan mengelola dashboard sehari-hari, dan perlu lebih dari satu akun?
4. Apakah semua produk All Size, atau ada yang punya ukuran S/M/L?
5. Apakah stok perlu ditampilkan, atau cukup ditanyakan lewat WhatsApp?
6. Bahan yang masih dibutuhkan: file logo "fr." dan screenshot @frinelo.store.

## Log perubahan

| Tanggal | Perubahan |
|---|---|
| 2026-10-05 | Proyek dibuat; halaman katalog tampil; perbaikan error Vite (bootstrap, folder Pages, manifest); disesuaikan dengan info toko dari Instagram; dashboard admin bertema pink disiapkan |
