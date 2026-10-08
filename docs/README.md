# Catatan Handoff: Frinelo Smart Catalog

**Terakhir diperbarui:** 8 Oktober 2026
**Lokasi proyek di laptop:** `C:\Users\ASUS\Downloads\frinelo-catalog`
**Repo GitHub:** `frinelo-catalog` (Private), branch `main`, commit pertama `358de88`

File ini adalah **indeks dan catatan status** folder `docs/`. README di root
proyek berisi cara instalasi dan menjalankan; file ini berisi posisi pekerjaan
terakhir. Baca ini dulu sebelum melanjutkan.

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
- Database MySQL `frinelo` dengan tabel `users`, `products`, `cache`, `jobs`, `sessions`
- 7 produk contoh bergaya Frinelo (data placeholder, belum foto asli)
- Katalog publik versi desain baru: bar teks berjalan, hero bingkai lengkung,
  strip Cara Pesan, filter menempel, pencarian, urutan harga, modal detail,
  tombol WhatsApp mengambang, footer toko
- Alur pesan WhatsApp teruji: pilih ukuran dan warna, WhatsApp terbuka dengan pesan terisi
- Uji di HP lewat jaringan WiFi (LAN) berhasil
- Dashboard admin bertema pink terpasang; login berhasil; password admin sudah diganti
- Kode sudah di GitHub (Private)

### Perlu diverifikasi
- Route `register` sudah dikomentari di `routes/auth.php`:
  `php artisan route:list --path=register` harus kosong dan `/register` harus 404
- Pesan sukses (toast) muncul setelah tambah/hapus produk (bagian `flash`
  di `HandleInertiaRequests.php` sudah ada)
- Tampilan dashboard admin di layar HP (baris tombol di kartu produk bisa sempit)
- Aturan firewall `Laravel dev 8000` (dibuat untuk uji HP): hapus jika tidak
  dipakai lagi, lewat PowerShell Administrator:
  `Remove-NetFirewallRule -DisplayName "Laravel dev 8000"`

### Belum dikerjakan
Lihat [ROADMAP.md](ROADMAP.md). Yang paling berpengaruh: foto produk asli,
koreksi nama dan harga, konfirmasi nomor WhatsApp, logo, dan hosting.

## Mulai dari sini (urutan langkah berikutnya)

1. Jalankan `composer run dev` (atau `npm run dev` + `php artisan serve`).
2. Login ke `/login`, hapus produk contoh, upload foto asli lewat **+ Tambah**.
3. Koreksi nama dan harga agar sesuai aslinya.
4. Kirim satu pesan WhatsApp uji dan pastikan masuk ke nomor yang benar.
5. Lihat katalog di HP, terutama hero dan modal produk dengan foto asli.

## Masalah yang pernah muncul dan solusinya

| Gejala | Penyebab | Solusi |
|---|---|---|
| `GET .../Pages/Catalog/Index.vue 404` atau `Page not found: ./Pages/Admin/Products/Index.vue` | File halaman ada di folder yang salah atau belum terpasang | Pastikan file ada di `resources/js/Pages/...` dengan huruf besar `P` persis |
| `Failed to resolve import "./bootstrap"` | Skeleton Laravel baru tidak punya `resources/js/bootstrap.js` | Hapus baris `import './bootstrap'` di `app.js` (atau buat file-nya + `npm install axios`) |
| `Vite manifest not found` / `Unable to locate file in Vite manifest` | `npm run dev` tidak berjalan, dan Laravel memakai build lama | Jalankan `npm run dev`; hapus `public\build` agar tidak membingungkan; atau `npm run build` |
| Halaman kosong di HP | Dev server Vite masih aktif (aset dicari ke `localhost`) | Hentikan `npm run dev`, hapus `public\hot`, `npm run build` |
| `Table 'frinelo.users' doesn't exist` padahal migrate "Ran" | File `0001_01_01_000000_create_users_table.php` tertimpa skema `products` (`Schema::create('products'` di dalamnya) | Kembalikan ke isi bawaan Laravel (`Schema::create('users'`, `password_reset_tokens`, `sessions`), lalu `php artisan migrate:fresh` |
| `Table 'products' already exists` saat migrate | Akibat masalah di atas | Perbaiki file migration users, lalu `migrate:fresh` |
| Katalog kosong "Belum ada produk" | Tabel `products` kosong atau semua `is_active = 0` | Jalankan seeder atau tambah lewat dashboard |
| Seeder menghasilkan produk lama (Dress, Tunik) | `ProductSeeder.php` belum diganti | Ganti isinya dengan produk Frinelo, `Product::truncate()`, lalu seed ulang |
| Peringatan `baseUrl is deprecated` di `jsconfig.json` | Hanya peringatan editor | Hapus `baseUrl`, ubah `paths` menjadi `"@/*": ["./resources/js/*"]` |
| HP "cannot be reached" saat uji WiFi | Server hanya di `127.0.0.1`, atau firewall memblokir port 8000 | `php artisan serve --host=0.0.0.0`, buat aturan firewall (PowerShell **Administrator**), pakai IP Wi-Fi laptop |
| `New-NetFirewallRule : Access is denied` | Terminal VS Code tidak punya hak Administrator | Buka PowerShell via Start, klik kanan, Run as administrator |
| `The "--tables" option does not exist` | Opsi tidak ada di versi Laravel ini | Pakai `php artisan db:show --counts` atau `Schema::getTableListing()` di tinker |
| `Get-ChildItem : A positional parameter cannot be found` | PowerShell memisahkan path dengan koma, bukan spasi | `Get-ChildItem a, b, c` |

## Catatan lingkungan

- OS: Windows, terminal PowerShell
- MySQL 8.0 (lokal) berisi banyak database proyek lain; proyek ini hanya
  memakai database `frinelo` sesuai `.env`
- Nama folder dan file case-sensitive di server Linux, samakan persis
- Foto upload tersimpan di `storage/app/public/products` dan dibaca lewat
  `public/storage` (perlu `php artisan storage:link`)
- Jangan menjalankan `php artisan test` langsung: `RefreshDatabase` menghapus
  isi database yang aktif. Arahkan ke SQLite in-memory dulu (lihat ROADMAP)
