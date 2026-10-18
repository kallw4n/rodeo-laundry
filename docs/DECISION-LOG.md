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

## 3. Integrasi Riwayat Pesanan Pelanggan (`/orders`)
* **Tanggal:** 18 Oktober 2026
* **Konteks:**
  Halaman `/orders` sebelumnya menggunakan data dummy statis di Alpine.js.
* **Keputusan Teknis:**
  1. **Scoping Data Sesuai Kepemilikan (Privacy & Security):**
     Query pesanan difilter berdasarkan `customer_id` yang terhubung ke `user_id` yang sedang login (`Order::where('customer_id', $customer->id)`). Pelanggan tidak dapat melihat transaksi pelanggan lain.
  2. **Mapping Data Responsif:**
     Controller memformat tanggal, label status Indonesia, relasi item pakaian, dan alamat pengiriman agar langsung sesuai dengan struktur Alpine.js di `pesanan.blade.php`.
  3. **Aksi Pembatalan Pesanan (`PATCH /orders/{order}/cancel`):**
     Pelanggan dapat membatalkan pesanan secara mandiri selama statusnya masih `pending` (belum diproses atau dicuci oleh pihak laundry).

---

## 4. Database Schema Alignment
* Penambahan kolom `remember_token` dan `email_verified_at` pada tabel `users`.
* Default role pada tabel `users` adalah `'customer'`.
* Foreign key `customers.user_id` nullable dengan aksi `nullOnDelete`.

---

## 5. Pembersihan OrderController Lama
* **Tanggal:** 18 Oktober 2026
* **Keputusan:** Method create, store, show, edit, update, destroy dihapus karena:
  - Tidak ada route yang memakainya, dan view orders.* tidak ada.
  - store() menduplikasi checkout() dan me-redirect ke route orders.index yang tidak ada (error).
  - Manajemen order oleh staf sudah ditangani Admin\PosController dan portal admin.
* **Dipertahankan:** index (riwayat pelanggan), cancelCustomerOrder, checkout.

---

## 6. Pembersihan Route Legacy & Insecure (Expenses & Dead Routes)
* **Tanggal:** 18 Oktober 2026
* **Konteks:** Terdapat route Route::resource('expenses') tanpa proteksi auth/role guard, serta route legacy /riwayat, /laporan, dan /profil yang mengarah ke method tidak ada.
* **Keputusan Teknis:**
  - expenses dihapus dari outes/web.php karena ExpenseController masih stub kosong, tidak ada view yang menyertainya, dan modul pengeluaran tidak masuk dalam spesifikasi PRD/Figma.
  - Route mati /riwayat, /laporan, dan /profil dibersihkan dari outes/web.php sehingga seluruh route aplikasi kini valid, bersih, dan terlindungi sesuai hak akses role.
