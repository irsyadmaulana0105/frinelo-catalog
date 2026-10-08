# Catatan Handoff: Frinelo Smart Catalog

**Terakhir diperbarui:** 5 Oktober 2026
**Lokasi proyek di laptop:** `C:\Users\ASUS\Downloads\frinelo-catalog`

File ini adalah **indeks dan catatan status** folder `docs/`. Baca ini dulu
sebelum melanjutkan pekerjaan. (README di root proyek berisi cara instalasi
dan menjalankan; file ini berisi posisi pekerjaan terakhir.)

## Isi folder docs

| File | Isi |
|---|---|
| [ARCHITECTURE.md](ARCHITECTURE.md) | Alur kerja sistem, route, komponen frontend, desain, keamanan |
| [DATA-MODEL.md](DATA-MODEL.md) | Struktur tabel, aturan validasi, rencana tabel berikutnya |
| [ROADMAP.md](ROADMAP.md) | Daftar tugas berikutnya, ide, keputusan, pertanyaan terbuka |

## Info toko (dari Instagram)

| Hal | Isi |
|---|---|
| Nama | Toko Frinelo ("Little Bangkok Baju Sidoarjo") |
| Instagram | @frinelo.wear (akun katalog: @frinelo.store) |
| Alamat | Jl. KH Mukmin No. 40, Sidoarjo |
| Jam buka | 10.00 - 22.00 |
| Telepon toko | 081359933771 (format WA: `6281359933771`, **belum dikonfirmasi** sebagai nomor penerima pesanan) |
| Gaya | Feminin, pink, remaja: tanktop, rajut, cardigan, rok, celana |
| Kisaran harga | Sekitar Rp50.000 - Rp95.000 (perkiraan dari screenshot, koreksi lewat dashboard) |
| Ukuran | Kebanyakan All Size, sebagian S/M/L |

## Status pekerjaan

### Sudah berjalan
- Proyek Laravel + Breeze (Vue/Inertia) terpasang, Vite berjalan
- Halaman katalog publik tampil (header, judul, filter, footer)
- Migration, model, controller, request, route sudah dibuat

### Sudah ada kodenya, belum terkonfirmasi diterapkan
- Tabel `products` terisi (terakhir katalog masih menampilkan "Belum ada produk")
- Akun admin dan login `/login`
- Dashboard admin versi baru (`AdminLayout.vue`, `Index.vue`, `Form.vue`, route `toggle`)
- Ukuran "All Size" di `Product::SIZES`
- Footer info toko dan `config/frinelo.php`
- Tema pink pada tombol katalog
- Uji modal: pilih ukuran/warna lalu tombol WhatsApp

### Belum dikerjakan
Lihat [ROADMAP.md](ROADMAP.md).

## Mulai dari sini (urutan langkah berikutnya)

1. Jalankan `composer run dev` (atau `npm run dev` + `php artisan serve`).
2. Cek jumlah produk:
   ```powershell
   php artisan tinker --execute="echo App\Models\Product::count();"
   ```
3. Jika hasilnya 0: `php artisan db:seed --class=ProductSeeder`
   (jalankan **sekali**, kalau dobel: `Product::truncate()` lalu seed ulang).
4. Buat akun admin (perintah ada di README root), login di `/login`.
5. Cek `Pages/Admin/Products/` berisi `Index.vue` dan `Form.vue` versi dashboard baru.
6. Tambah satu produk lewat dashboard dengan foto asli, cek tampil di katalog.
7. Klik produk di katalog, pilih ukuran dan warna, uji tombol WhatsApp
   (pesan harus terisi rapi di WhatsApp).
8. Nonaktifkan route `register` di `routes/auth.php`.

## Masalah yang pernah muncul dan solusinya

| Gejala | Penyebab | Solusi |
|---|---|---|
| `GET .../Pages/Catalog/Index.vue 404` | File halaman ada di `Components/Catalog/`, bukan `Pages/Catalog/` | Pindahkan ke `resources/js/Pages/Catalog/Index.vue` |
| `Failed to resolve import "./bootstrap"` | Skeleton Laravel baru tidak punya `resources/js/bootstrap.js` | Hapus baris `import './bootstrap'` di `app.js` (atau buat file-nya + `npm install axios`) |
| `Vite manifest not found at public/build/manifest.json` | `npm run dev` tidak sedang berjalan | Jalankan `npm run dev` dan biarkan terbuka, atau `npm run build` |
| `Get-ChildItem : A positional parameter cannot be found` | Di PowerShell, beberapa path dipisah koma, bukan spasi | `Get-ChildItem a, b, c` |
| Katalog hanya menampilkan tombol "Semua" dan "Belum ada produk" | Tabel `products` kosong, atau semua `is_active = 0` | Jalankan seeder atau tambah produk lewat dashboard |
| Port 5173 bentrok, atau file `public\hot` basi | Ada Vite lain berjalan | Hentikan proses lama, `Remove-Item public\hot`, jalankan ulang |

## Catatan lingkungan

- OS: Windows, terminal PowerShell
- Folder `Pages` memakai huruf besar `P`; nama folder dan file case-sensitive
  di server Linux, jadi samakan persis
- Foto upload tersimpan di `storage/app/public/products` dan dibaca lewat
  `public/storage` (perlu `php artisan storage:link`)
