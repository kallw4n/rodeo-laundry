# System Architecture & Technical Specifications

## 1. High-Level Architecture Overview

Rodeo Laundry follows a clean Layered MVC Architecture in Laravel 12, serving server-rendered Blade templates integrated with Vite, Tailwind CSS, and reactive Alpine.js components.

```mermaid
graph TD
    Client[Browser / Client Device] -->|HTTP / HTTPS| WebServer[Laragon Apache / Nginx / PHP CLI]
    WebServer --> Router[routes/web.php & routes/auth.php]
    Router --> Middleware[Middleware: auth, RoleMiddleware, csrf]
    Middleware --> Controllers[Controllers: OrderController, Auth, Customer, etc.]
    Controllers --> Models[Eloquent Models: User, Customer, Order, etc.]
    Models --> DB[(MySQL 8.4 rodeo_laundry)]
    Controllers --> Views[Blade Templates + Alpine.js]
    Views --> ViteBundle[Vite Bundle: app.css + app.js]
    ViteBundle --> Client
```

---

## 2. Customer Booking Flow Architecture

```mermaid
sequenceDiagram
    autonumber
    actor Customer as Pelanggan
    participant Front as Landing Page
    participant Wizard1 as Pemesanan (Layanan)
    participant Wizard2 as Pengiriman (Metode & Alamat)
    participant Wizard3 as Pembayaran (Metode & Konfirmasi)
    participant Backend as Laravel Controller
    participant DB as MySQL Database

    Customer->>Front: Akses Website Rodeo Laundry
    Customer->>Wizard1: Klik "Pesan Sekarang" (pilih layanan)
    Note over Wizard1: Alpine.js $persist menyimpan keranjang layanan
    Customer->>Wizard2: Lanjut ke Pengiriman
    Note over Wizard2: Pilih Antar Sendiri / Jemput Antar Rodeo
    Customer->>Wizard3: Lanjut ke Pembayaran
    Customer->>Wizard3: Pilih Metode (QRIS/Transfer/Tunai) & Konfirmasi
    Wizard3->>Backend: POST /orders (payload lengkap)
    Backend->>DB: DB::transaction (Insert Order, OrderItems, Delivery, Payment)
    DB-->>Backend: Commit Transaction
    Backend-->>Customer: Redirect ke /orders dengan flash success & Invoice
```

---

## 3. Security & Access Control Matrix

| Route Group | Path | Allowed Roles | Middleware |
|---|---|---|---|
| **Public** | `/` | Guest, Customer, Admin | `web` |
| **Auth** | `/login`, `/register`, `/forgot-password` | Guest only | `guest` |
| **Customer Booking** | `/pemesanan`, `/pengiriman`, `/pembayaran` | Authenticated (`customer`, `admin`) | `auth` |
| **Customer Orders** | `/orders`, `/pesanan` | Authenticated | `auth` |
| **Admin Operations** | `/service-types`, `/customers`, `/payments`, `/deliveries`, `/expenses` | Authenticated (`admin`) | `auth`, `role:admin` |

---

## 4. State Management (Client-Side & Server-Side)

### 4.1 Client-Side (Alpine.js Persist)
- Key: `rodeo_booking_cart`
- Fields:
  ```json
  {
    "items": [
      { "id": 1, "name": "Cuci Kering Biasa", "price": 5000, "qty": 3, "unit": "pcs" },
      { "id": 2, "name": "Cuci Setrika Kiloan", "price": 8000, "qty": 5, "unit": "kg" }
    ],
    "delivery": {
      "type": "both",
      "address": "Jl. Soekarno Hatta No. 45, Malang",
      "city": "Malang",
      "pickup_time": "2026-10-18T10:00",
      "notes": "Tolong jangan dicampur baju putih dengan warna pekat"
    },
    "payment": {
      "method": "qris"
    }
  }
  ```

### 4.2 Server-Side Synchronization
- Transactional integrity using `DB::transaction()`:
  - If payment or delivery record creation fails, order and order items are cleanly rolled back.
  - Calculation validation: Prices are strictly verified against `ServiceType` prices in database to prevent client tampering.
