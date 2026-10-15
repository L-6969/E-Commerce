# Frontend Blade — Cilok Sapi SR Khas Ciamis

Template Laravel (Blade + Tailwind CSS + Alpine.js) untuk seluruh antarmuka FR-01 s.d. FR-18. Hanya frontend: route sudah memakai data contoh (`App\Support\Demo`) sehingga semua halaman bisa dibuka tanpa database.

## Pasang ke proyek Laravel 10/11 (Vite)
1. Salin ke proyek: `resources/views/*`, `resources/css/app.css`, `app/Support/*`, `tailwind.config.js`, `postcss.config.js`, dan `routes/web.php` (gabungkan jika sudah ada). Jika proyek memakai Tailwind v4 bawaan Laravel 12, hapus dulu paket v4 atau pasang v3 seperti langkah 3.
2. Di `composer.json` tambahkan `"autoload": { "files": ["app/Support/helpers.php"] }`, lalu `composer dump-autoload`.
3. `npm install -D tailwindcss@3 postcss autoprefixer`. Pastikan `vite.config.js` punya `input: ['resources/css/app.css', 'resources/js/app.js']` (bawaan Laravel).
4. Terminal 1: `npm run dev`. Terminal 2: `php artisan serve`. Buka `/` (publik) dan `/masuk` (admin). Tombol Masuk langsung membuka dashboard (demo). Tambah `?role=staff` di URL admin untuk melihat tampilan Staf.
5. Produksi: `npm run build`.

Gaya mengikuti desain Figma "Website Design for Cilok Sapi": palet hijau tua `forest #183c2f`, krem `cream #f6f2e8`, `leaf #e5eba9`, `gold #f5b849`; font DM Sans dan Libre Franklin. Semua ada di `tailwind.config.js` dan `resources/css/app.css`. Utilitas Tailwind dipakai langsung di Blade; pola yang berulang (`.btn`, `.card`, `.chip`, `.f`) didefinisikan dengan `@apply` di `resources/css/app.css`.

## Daftar halaman
| Route | View | FR |
|---|---|---|
| home | public/home | FR-01, FR-02 |
| cart | public/cart | FR-03 |
| checkout, checkout.store | public/checkout | FR-04, FR-05, FR-06, NFR-12 |
| order.show | public/confirm | FR-07 |
| login, login.store, logout | admin/login | FR-08 |
| admin.orders, admin.orders.show | admin/orders/* | FR-10, FR-11, FR-14 |
| admin.offline | admin/offline | FR-12 |
| admin.products.* | admin/products/* | FR-09 |
| admin.report | admin/report | FR-13, FR-15 |
| admin.stock | admin/stock | FR-18 |
| admin.settings | admin/settings | FR-16 |
| admin.logs | admin/logs | FR-17 |

## Tugas backend (di luar frontend)
- Ganti closure di `routes/web.php` dengan controller. **Nama route jangan diubah.** Kontrak variabel tiap view ada di komentar `{{-- Variabel: ... --}}` paling atas.
- Checkout: validasi di server, hitung ulang total dari harga terkini, tolak produk habis, simpan status "Baru" (atomik), cek `idempotency_token` (satu klik = satu pesanan), lalu redirect ke `order.show` dengan token acak.
- Middleware `auth` + peran: Laporan dan Pengaturan hanya Pemilik (403 untuk Staf), koreksi pesanan "Selesai" hanya Pemilik, batal wajib `reason`.
- Ganti `$authUser ?? auth()->user()` di layout admin dengan `auth()->user()` (hapus `View::composer` demo).
- Pasang throttle login, simpan riwayat di `order_status_logs`, validasi unggah foto di server (tipe, ukuran, nama acak).
- Font dimuat dari Google Fonts, Alpine dari jsDelivr (versi dipin 3.14.1). Untuk produksi bisa dibundel lewat Vite.

## Catatan desain Figma
- Beranda (`public/home.blade.php`) memakai semua bagian desain: header, hero, keunggulan, tentang, CTA, footer. Bagian Katalog (FR-02) disisipkan karena desain Figma tidak punya katalog.
- Tombol "Pesan" di hero dan CTA mengarah ke katalog (`#produk`), bukan langsung ke WhatsApp, supaya pesanan tersimpan dulu (P-01, FR-05).
- Foto hero dan cerita memakai foto stok dari desain (Unsplash). Ganti dengan foto asli produk mitra.
- Angka dan klaim dari desain yang tidak ada di KAK atau usulan (4.9/5, 10K+ porsi, sejak 2018, 6+ tahun, tanpa pengawet, 100% sapi, tautan Instagram) tidak dipakai. Diganti data dari pengaturan toko: jumlah menu, harga termurah, jam buka, nomor halal.
- Teks cerita usaha di bagian Tentang Kami berasal dari desain. Minta mitra memeriksa kebenarannya.
- Foto produk di form hanya menerima WebP (NFR-02). Kompres dan simpan di backend, batas 200 KB.
- Laporan memakai Chart.js 4.4.1 dari jsDelivr. Keranjang otomatis membuang produk yang habis (E-03), server tetap wajib memvalidasi ulang saat checkout.
- Kolom `description` pada produk dipakai di katalog dan form produk.
- **SEO dasar (Fase 2):** judul dan meta description per halaman, canonical, Open Graph, data terstruktur `Restaurant` di beranda, `noindex` untuk admin, keranjang, checkout, dan halaman konfirmasi, serta `public/robots.txt`. Sitemap dan gambar `og:image` perlu dibuat setelah domain dan foto asli tersedia.
- **Waktu ambil (Fase 2):** checkout punya pilihan "Secepatnya" atau "Pilih jam" (`pickup_time`, HH:MM). Kolom `pickup_time` (nullable) **belum ada di ERD Bab 3.2**: tambahkan ke tabel `orders`. Backend memvalidasi jam masuk dalam jam buka.
- **Verifikasi dua langkah Pemilik (Fase 2):** halaman kode ada di `admin/two-factor` (route `login.2fa`). Pendaftaran autentikator (QR) dan logikanya dikerjakan backend.
