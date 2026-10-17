# Catatan Keputusan Teknis & Perubahan Penting (Decision Log) — Rodeo Laundry

Dokumen ini mencatat keputusan arsitektural dan implementasi penting yang telah diputuskan untuk menyelaraskan Backend (Laravel) dan Frontend (Blade + Alpine.js).

---

## 1. Integrasi Customer Self-Checkout (Pemesanan Pelanggan)
* **Tanggal:** 18 Oktober 2026
* **Konteks:**
  Alur pemesanan pelanggan di sisi frontend terdiri dari 3 tahapan:
  1. `/pemesanan`: Pemilihan layanan (kiloan & satuan) disimpan ke state browser (`Alpine.$persist`).
  2. `/pengiriman`: Pemilihan metode antar/jemput (`courier` / `self`) dan catatan kurir.
  3. `/pembayaran`: Pemilihan metode pembayaran (`qris`, `va`, `ewallet`, `cod`) dan ringkasan total.
* **Keputusan Teknis:**
  1. **Otoritas Harga di Server (Security & Integrity):**
     Total harga dan harga satuan dihitung ulang di backend menggunakan database `service_types`. Input harga dari client diabaikan untuk mencegah manipulasi.
  2. **Relasi User dan Customer:**
     Pelanggan yang login dihubungkan dengan data tabel `customers` menggunakan `user_id`. Jika akun baru pertama kali memesan, record `Customer` dibuat otomatis (`Customer::firstOrCreate`) menggunakan username dan email akun tersebut.
  3. **Atomic DB Transaction:**
     Pembuatan pesanan dibungkus dalam `DB::transaction`:
     - Tabel `orders`: Dibuat dengan status awal `pending` dan estimasi selesai H+2.
     - Tabel `order_items`: Berisi daftar layanan, kuantitas, dan harga satuan server.
     - Tabel `payments`: Status awal `pending` (menunggu konfirmasi/verifikasi kasir/admin), dengan nominal sama dengan total order.
     - Tabel `deliveries`: Status awal `scheduled`, dengan tipe `delivery` (jika kurir) atau `pickup` (ambil di outlet), beserta alamat dan catatan pelanggan.
  4. **Pembersihan Keranjang:**
     Setelah checkout sukses, client menerima respon JSON redirect dan membersihkan keranjang `localStorage` (`cart_kiloan`, `cart_service_qty`, dll.) agar tidak terjadi double submit.
  5. **Pengujian:**
     Divalidasi dengan feature test `tests/Feature/CustomerCheckoutTest.php` (100% pass).

---

## 2. Struktur Role & Akses (Authentication & Authorization)
* **Framework:** Laravel Breeze + Custom `RoleMiddleware`.
* **Redirection Pasca Login:**
  - `role === 'admin'`: Diarahkan ke Portal Manajemen & Kasir POS (`/admin/dashboard`).
  - `role === 'customer'`: Diarahkan ke Beranda / Portal Layanan Pelanggan (`/`).
* **Akun Bawaan (Default Seeder):**
  - Admin: `admin@rodeo.com` / `password`
  - Customer contoh: Faker customer Indonesia / `password`

---

## 3. Database Schema Alignment
* Penambahan kolom `remember_token` dan `email_verified_at` pada tabel `users`.
* Default role pada tabel `users` adalah `'customer'`.
* Foreign key `customers.user_id` nullable dengan aksi `nullOnDelete`.
