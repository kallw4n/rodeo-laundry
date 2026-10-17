# Execution Checklist & Verification Log — Rodeo Laundry Malang

## Summary of Implementation & Status
- **Target Scope:** Admin Portal & POS Kasir Frontend (`/admin/*`) strictly mapped to 11 Figma screens in `docs/figma/`.
- **Backend & Schemas:** Integrated cleanly with Laravel 12 + Breeze, Alpine.js, TailwindCSS.
- **Automated Tests:** 35 passed (86 assertions) — 100% pass rate.
- **Vite Compilation:** Production assets built successfully (`app.css` 86.5 kB, `app.js` 107.8 kB).

---

## Sprint Checklist

- [x] **Step 1: Environment & Tooling Verification**
  - [x] Check Git remote (`https://github.com/kallw4n/rodeo-laundry.git`)
  - [x] Synchronize branch `main` with `origin/main` (fast-forwarded remote commits)
  - [x] Run `composer install` (Laravel Breeze v2.4.2 installed)
  - [x] Run `npm install` (Alpine.js, Tailwind forms, Vite installed)
  - [x] Verify Vite build pipeline (`npm run build`)
  - [x] Run `php artisan migrate:fresh --seed` (All tables & seeded records ready)

- [x] **Step 2: Test Suite & User Factory Alignment**
  - [x] Add `email_verified_at` to `users` migration and set default role `'customer'`
  - [x] Fix `database/factories/UserFactory.php` to use `username` and provide `admin()` state
  - [x] Fix `ProfileUpdateRequest` to validate `username`
  - [x] Add `RefreshDatabase` trait to feature tests
  - [x] Verify base `php artisan test` passes with zero failures

- [x] **Step 3: Shared Admin Layout (`resources/views/layouts/admin.blade.php`)**
  - [x] Sidebar matching Figma: Brand logo, Dashboard, POS/Kasir, Pelanggan, Layanan, Pembayaran, Pengiriman
  - [x] Active state with dark indigo pill (`#1E1B4B`) and electric lime dot (`#BFF108`)
  - [x] Bottom Admin Rodeo profile card with quick logout action
  - [x] Topbar: search bar, date pill (`Hari Ini: D MMM Y`), notification bell with badge, user avatar
  - [x] Responsive mobile slide-out drawer with Alpine.js

- [x] **Step 4: Admin Authentication & Login Page (`page login.png`)**
  - [x] Split-screen layout: Left aesthetic laundry visual + review card (4.9/5 stars, 18.4k+ customers)
  - [x] Right column: Branded login card, username/email, password with show/hide toggle, remember me, Google CTA
  - [x] Admin role redirect logic in `AuthenticatedSessionController` (redirects `role === 'admin'` to `/admin/dashboard`)

- [x] **Step 5: Dashboard Overview (`page dahsboard.png`)**
  - [x] Controller `AdminDashboardController` with live metrics calculations
  - [x] 4 Key Metric Cards: Total Pesanan, Pendapatan Hari Ini, Pelanggan Aktif, Antrian Mesin Aktif (with Urgent pill)
  - [x] Status filter dropdown: Semua Status, Antrian Masuk, Sedang Dicuci, Siap Ambil, Selesai
  - [x] Recent Orders Table with badges, item breakdown, and print action

- [x] **Step 6: Point of Sale (POS) / Kasir (`page kasir.png`)**
  - [x] Controller `PosController` with DB transaction saving Order, OrderItem, Payment, and Delivery
  - [x] Live customer autocomplete search + selected customer card with "Gratis Antar-Jemput Aktif" badge
  - [x] 2-column catalog grid with 5 service packages, duration badges, and "+ Tambah" buttons
  - [x] Reactive Order Summary card with dynamic invoice number (`#ROD-XXXX`)
  - [x] Real-time quantity/weight stepper and subtotal calculations
  - [x] 4 Payment Method buttons (`QRIS / E-Wallet`, `Tunai / Cash`, `Transfer Bank`, `Bayar Saat Antar`)
  - [x] Cash received calculator with instant change return display
  - [x] Action CTAs: `Simpan & Cetak Struk` (`#BFF108`) and `Simpan Jadi Draft / SPK Cuci`

- [x] **Step 7: Kelola Pelanggan (`page pelanggan.png` & `page tambah pelanggan.png`)**
  - [x] Controller `AdminCustomerController` with search, city filtering, and sorting
  - [x] Table with Customer ID (`#RD-XXXX`), Name, Email, Phone, City badge, and circular edit/delete actions
  - [x] Alpine.js Modal "Tambah Pelanggan Baru" & "Edit Pelanggan" with auto ID generation, `+62` prefix, city selector, address textarea, and WhatsApp notice

- [x] **Step 8: Kelola Layanan (`page layanan.png` & `page tambah layanan.png`)**
  - [x] Controller `AdminServiceController` with search, category filtering, and sorting
  - [x] Table with Service ID, Name with green dot, Price badge, Description, and actions
  - [x] Alpine.js Modal "Tambah Layanan Baru" with `POS READY` badge, category pills (`Kiloan`, `Satuan`, `Sepatu / Tas`, `Karpet`), price input, unit pills (`per Kg`, `per Pcs`, `per Meter`), and active toggle

- [x] **Step 9: Kelola Pembayaran (`page pembayaran.png` & `page tambah pembayaran.png`)**
  - [x] Controller `AdminPaymentController` with search, status filtering, and sorting
  - [x] Table with Nota ID, Customer Name, Service Type, Laundry Weight, Price badge, and Lunas / Belum Lunas pills
  - [x] Alpine.js Modal "Catat Pembayaran" with auto nota selector, payment method cards (QRIS, Tunai, Transfer), and cash/change calculator

- [x] **Step 10: Kelola Pengiriman (`page pengiriman.png` & `page tambah pengiriman.png`)**
  - [x] Controller `AdminDeliveryController` with search, status filtering, and status updates
  - [x] Table with Nota ID, Customer & Contact, Laundry Details, Delivery Address, Status Antar pills
  - [x] Quick motorcycle dispatch action (sets status to `on-way`) and complete action (sets status to `completed`)
  - [x] Alpine.js Modal "Jadwalkan Antar Baru" with `EKSPEDISI LOKAL` badge, driver picker (`Bagus Ananta`, `Rian Santoso`, `Hendra Wijaya`), time slots, notes, and WhatsApp notice

- [x] **Step 11: End-to-End Automated & Browser Verification**
  - [x] `php artisan test`: 35 tests passed, 86 assertions (100% pass)
  - [x] `npm run build`: Assets compiled without warnings or errors
  - [x] Browser Subagent verified: Login, Dashboard, POS cart reactivity, Customer modal, Service modal, Payment modal, Delivery modal
