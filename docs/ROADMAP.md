# Roadmap

Legenda: `[ ]` belum, `[x]` selesai.

## 1. Selesai

- [x] Proyek Laravel + Breeze (Vue/Inertia) dan Vite berjalan
- [x] Database MySQL `frinelo`, migration, seeder 7 produk Frinelo
- [x] Katalog publik versi desain baru (hero, filter, pencarian, modal, footer)
- [x] Alur WhatsApp teruji, termasuk dari HP lewat WiFi
- [x] Dashboard admin bertema pink dan login
- [x] Password admin bawaan diganti
- [x] Kode di GitHub (Private)

## 2. Berikutnya: siap ditawarkan

- [ ] Verifikasi route `register` mati (`route:list --path=register` kosong, `/register` 404)
- [ ] Upload 5 sampai 8 foto asli lewat dashboard, hapus produk contoh
- [ ] Koreksi nama dan harga produk sesuai aslinya
- [ ] Konfirmasi nomor WhatsApp penerima pesanan; kirim satu pesan uji
- [ ] Cek dashboard admin di HP (baris tombol di kartu produk)
- [ ] Pasang logo "fr." di header, dashboard, dan favicon (butuh file logo)
- [ ] Cocokkan nama, kategori, harga dengan akun @frinelo.store (butuh screenshot)
- [ ] Perbaikan kecil: hapus `font-display` pada angka di `HowToOrder.vue`
- [ ] Perbaikan kecil di `ProductModal.vue`: baris `- Link:` hanya muncul di luar
      localhost, dan `object-top` pada foto utama
- [ ] Hapus aturan firewall `Laravel dev 8000` jika tidak dipakai lagi
- [ ] Opsional: tes otomatis (`tests/Feature/FrineloTest.php`). Jalankan dengan
      `cmd /c "set DB_CONNECTION=sqlite&& set DB_DATABASE=:memory:&& php artisan test --filter=FrineloTest"`
      supaya database `frinelo` tidak terhapus oleh `RefreshDatabase`

## 3. Siap online

- [ ] Pilih hosting dan domain
- [ ] `npm run build`, `APP_ENV=production`, `APP_DEBUG=false`
- [ ] `php artisan storage:link` di server
- [ ] Pastikan `register` tetap mati dan password admin kuat
- [ ] Pasang link katalog di bio Instagram dan TikTok
- [ ] Tes link `/?p=ID` dari aplikasi Instagram/TikTok di HP
- [ ] Backup rutin database dan folder `storage/app/public/products`

## 4. Pengembangan berikutnya

| Prioritas | Fitur | Catatan |
|---|---|---|
| Tinggi | Filter "Baru Datang" | Label Baru sudah otomatis; tinggal filternya |
| Tinggi | Stok per varian | Tabel `product_variants`, ukuran/warna habis tidak bisa dipilih |
| Sedang | Multi-foto per produk | Galeri geser di modal, tabel `product_images` |
| Sedang | Pencatatan klik tombol WhatsApp | Untuk melihat produk paling diminati |
| Rendah | Harga coret dan label promo | Kolom `compare_price`, `badge` |
| Rendah | Urutan produk manual | Kolom `sort_order` |
| Rendah | Pagination atau lazy load | Jika produk lebih dari beberapa ratus |
| Rendah | Open Graph / meta tag | Link produk tampil dengan foto saat dibagikan |

## Keputusan yang sudah diambil

| Keputusan | Alasan |
|---|---|
| Tanpa keranjang, payment gateway, dan akun pembeli | Tujuan hanya jembatan ke WhatsApp |
| Laravel Breeze (Vue + Inertia) | Auth, Vue, Inertia, Tailwind langsung siap |
| Filter, pencarian, dan modal di sisi klien | Cepat di HP, tanpa reload |
| `/register` dimatikan | Hanya admin toko yang butuh akun; dibuat manual |
| Ukuran dibatasi daftar tetap (`Product::SIZES`) | Menjaga data konsisten |
| Kategori teks bebas | Admin bisa menambah kategori tanpa menu khusus |
| Harga integer | Hindari masalah desimal, format hanya di tampilan |
| Foto bisa URL luar atau upload | Data contoh memakai placeholder, produksi memakai upload |
| MySQL (bukan SQLite) | Sudah tersedia lewat XAMPP/Laragon, lebih dekat ke hosting |
| Desain terinspirasi identitas visual Instagram Frinelo | Cermin pink, label NEW ARRIVAL, nuansa feminin |

## Pertanyaan terbuka

1. Apakah 081359933771 benar nomor WhatsApp penerima pesanan, atau ada nomor admin khusus?
2. Domain dan hosting apa yang dipakai? (Perlu PHP 8.2+ dan MySQL.)
3. Siapa yang mengelola dashboard sehari-hari, dan perlu lebih dari satu akun?
4. Apakah semua produk All Size, atau ada yang S/M/L?
5. Apakah stok perlu ditampilkan, atau cukup ditanyakan lewat WhatsApp?
6. Bahan yang masih dibutuhkan: file logo "fr." dan screenshot @frinelo.store.
7. Skema biaya dan siapa yang merawat website setelah online.

## Log perubahan

| Tanggal | Perubahan |
|---|---|
| 2026-10-05 | Proyek dibuat; katalog tampil; perbaikan error Vite; disesuaikan dengan info toko dari Instagram |
| 2026-10-08 | Pindah ke MySQL `frinelo`; perbaikan migration users; desain katalog baru dan dashboard admin terpasang; uji HP via WiFi; password admin diganti; commit pertama ke GitHub |
