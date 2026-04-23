# 🌿 BHARATA HERBAL
### Sistem E-Commerce Multiplatform Produk Herbal

> Aplikasi e-commerce multiplatform berbasis **Web (Laravel)** dan **Mobile (Flutter)** dengan satu backend terpusat, dikembangkan sebagai Tugas Besar Mata Kuliah Pengembangan Aplikasi Berbasis Platform.

---

## 📑 Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Arsitektur Sistem](#arsitektur-sistem)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Struktur Database](#struktur-database)
- [Alur Sistem Lengkap](#alur-sistem-lengkap)
  - [Sisi Customer (Publik & Terautentikasi)](#sisi-customer-publik--terautentikasi)
  - [Sisi Admin](#sisi-admin)
- [Fitur Detail Per Modul](#fitur-detail-per-modul)
- [Siklus Hidup Pesanan](#siklus-hidup-pesanan)
- [Sistem Otomatisasi](#sistem-otomatisasi)
- [Platform-Specific Features](#platform-specific-features)
- [Struktur Direktori](#struktur-direktori)
- [Instalasi dan Konfigurasi](#instalasi-dan-konfigurasi)
- [Data Demo & Akun Default](#data-demo--akun-default)
- [Perintah Artisan Tersedia](#perintah-artisan-tersedia)
- [Pembagian Tugas Tim](#pembagian-tugas-tim)

---

## Gambaran Umum

**Bharata Herbal** adalah platform e-commerce khusus produk herbal dan kesehatan tradisional Indonesia. Sistem ini dibangun dengan arsitektur multiplatform yang memungkinkan pelanggan berbelanja melalui **website** maupun **aplikasi mobile Android**, keduanya menggunakan satu backend yang sama.

### Tujuan Aplikasi

| Perspektif | Tujuan |
|---|---|
| **Customer** | Memudahkan pembelian produk herbal kapan saja, di mana saja, lewat web atau HP |
| **Admin** | Mengelola produk, pesanan, pelanggan, dan laporan secara terpusat |
| **Bisnis** | Meningkatkan jangkauan penjualan melalui platform digital yang profesional |

---

## Arsitektur Sistem

```
╔══════════════════════════════════════════════════════════════════╗
║                    BHARATA HERBAL BACKEND                       ║
║                  Laravel 11 + MySQL (XAMPP)                     ║
║                                                                  ║
║  ┌─────────────────────┐    ┌──────────────────────────────┐   ║
║  │   Web Routes         │    │   API Routes                 │   ║
║  │   routes/web.php     │    │   routes/api.php             │   ║
║  │   (Blade/Session)    │    │   (JSON/Sanctum Token)       │   ║
║  └──────────┬──────────┘    └──────────────┬───────────────┘   ║
╚═════════════╪══════════════════════════════╪════════════════════╝
              │                              │
              ▼                              ▼
   ┌─────────────────────┐        ┌─────────────────────────┐
   │   WEB CLIENT         │        │   MOBILE CLIENT          │
   │   Laravel Blade      │        │   Flutter (Android)      │
   │   + Tailwind CSS     │        │   + Provider State Mgmt  │
   │   + Alpine.js        │        │   + Dio HTTP Client      │
   └─────────────────────┘        └─────────────────────────┘
   Platform-Specific:             Platform-Specific:
   ✅ PWA (Installable)            ✅ Kamera (foto review)
   ✅ Offline Support              ✅ GPS (auto-isi alamat)
   ✅ Web Push Notification        ✅ Push Notification (FCM)
```

### Komponen Backend

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          ← 16 controller panel admin
│   │   ├── Api/            ← Controller khusus mobile API
│   │   ├── Auth/           ← Register, Login, Logout
│   │   ├── CartController
│   │   ├── CheckoutController
│   │   ├── HomeController
│   │   ├── OrderController
│   │   ├── ShopController
│   │   └── UserDashboardController
│   ├── Middleware/
│   │   ├── AdminMiddleware
│   │   ├── SuperAdminMiddleware
│   │   └── UpdateSessionActivity
│   └── Requests/           ← Form validation
├── Models/                 ← 17 Eloquent models
├── Observers/              ← Auto-update rating & sales
├── Services/               ← Business logic layer
└── Policies/               ← Authorization rules
```

---

## Teknologi yang Digunakan

### Backend (Web & API)

| Teknologi | Versi | Fungsi |
|---|---|---|
| **Laravel** | 11 | Framework PHP utama |
| **PHP** | 8.2 | Bahasa pemrograman backend |
| **MySQL** | 8.0+ | Database relasional |
| **Laravel Breeze** | — | Autentikasi dasar (register/login) |
| **Laravel Sanctum** | — | Token-based auth untuk Mobile API |
| **Spatie Permission** | — | Role-based access control |
| **Eloquent ORM** | — | Query builder & relasi model |
| **Laravel Observers** | — | Auto-update stats (rating, sales) |

### Frontend Web

| Teknologi | Fungsi |
|---|---|
| **Blade Templates** | Templating engine HTML |
| **Tailwind CSS** | Utility-first styling |
| **Alpine.js** | Reaktivitas UI ringan |
| **Vite** | Asset bundler |

### Mobile (Flutter)

| Package | Fungsi |
|---|---|
| `provider` | State management |
| `dio` | HTTP Client (API calls) |
| `shared_preferences` | Penyimpanan token JWT lokal |
| `cached_network_image` | Cache gambar produk |
| `image_picker` | Kamera & galeri (platform-specific) |
| `geolocator` | GPS lokasi pengguna (platform-specific) |
| `firebase_messaging` | Push notification (platform-specific) |

---

## Struktur Database

### Diagram Relasi Antar Tabel

```
┌──────────┐        ┌──────────────────┐       ┌───────────────┐
│  users   │──1:N──▶│     orders        │──1:N─▶│  order_items  │
│          │        │  - status         │       │  - product_id │
│ role:    │        │  - tracking_no    │       │  - quantity   │
│ customer │        │  - courier_name   │       │  - price      │
│ admin    │        │  - payment_       │       └───────┬───────┘
│ super_   │        │    deadline       │               │
│ admin    │        └──────┬───────────┘               │ N:1
└──────────┘               │                           ▼
     │                     │ 1:1              ┌─────────────────┐
     │ 1:N                 │                  │    products      │
     ▼                     ▼                  │  - name, slug   │
┌──────────┐       ┌──────────────┐           │  - price        │
│  carts   │──1:N─▶│  cart_items  │           │  - stock        │
└──────────┘       └──────────────┘           │  - status       │
     │                                        │  - rating       │
     │ 1:N                                    │  - sales_count  │
     ▼                                        └────────┬────────┘
┌──────────────┐                                       │ N:M
│  addresses   │                              ┌────────▼────────┐
│  - province  │                              │   categories    │
│  - city      │                              │  (pivot table)  │
│  - is_default│                              └─────────────────┘
└──────────────┘
     │                ┌──────────────┐
     │ (order uses)   │   payments   │
     └───────────────▶│  - method    │
                      │  - status    │
                      │  - amount    │
                      └──────────────┘

┌──────────────┐       ┌──────────────────────┐
│   reviews    │       │  order_tracking       │
│ - order_id   │       │ - status update       │
│ - product_id │       │ - description         │
│ - rating 1-5 │       │ - created_at          │
│ - comment    │       └──────────────────────┘
│ - image      │
└──────────────┘

┌──────────────┐       ┌──────────────────────┐
│   settings   │       │   notifications       │
│ - group      │       │ - user_id (admin)     │
│ - key        │       │ - title, message      │
│ - value      │       │ - type (info/warning/ │
└──────────────┘       │   danger)             │
                       │ - is_read             │
┌──────────────┐       └──────────────────────┘
│ admin_sessions│
│ - user_id    │
│ - ip_address │       ┌──────────────────────┐
│ - device_info│       │  product_settings    │
│ - last_active│       │ - auto stock rules   │
└──────────────┘       │ - notification_type  │
                       └──────────────────────┘
```

### Daftar Model (17 Model)

| Model | Tabel | Deskripsi |
|---|---|---|
| `User` | `users` | Semua pengguna: customer, admin, super_admin |
| `Product` | `products` | Produk herbal dengan rating & sales tracking |
| `Category` | `categories` | Kategori produk (pivot many-to-many) |
| `Cart` | `carts` | Keranjang belanja per user |
| `CartItem` | `cart_items` | Item dalam keranjang, bisa di-select |
| `Order` | `orders` | Pesanan dengan status & tracking |
| `OrderItem` | `order_items` | Item per pesanan |
| `Payment` | `payments` | Data pembayaran (metode, status, bukti) |
| `Review` | `reviews` | Ulasan produk dari pelanggan |
| `Address` | `addresses` | Alamat pengiriman per user |
| `Setting` | `settings` | Konfigurasi dinamis (store/payment/shipping) |
| `BankAccount` | `bank_accounts` | Rekening bank tujuan transfer |
| `Notification` | `notifications` | Notifikasi dalam sistem untuk admin |
| `AdminSession` | `admin_sessions` | Tracking sesi login admin |
| `OrderTracking` | `order_tracking` | Riwayat update status pesanan |
| `ProductSetting` | `product_settings` | Aturan stok & notifikasi produk |

---

## Alur Sistem Lengkap

### Sisi Customer (Publik & Terautentikasi)

#### 1. Halaman Publik (Tanpa Login)

```
[Beranda]
  ├── Hero section + CTA "Belanja Sekarang"
  ├── Produk Unggulan (is_featured = true)
  ├── Produk Terlaris (is_bestseller = true)
  ├── Kategori produk
  └── Testimonial / Reviews

[Katalog Produk] → /produk
  ├── Filter: kategori, harga min-max, rating, stok
  ├── Pencarian: nama produk
  ├── Urutan: harga ↑↓, rating, terbaru
  └── Pagination

[Detail Produk] → /produk/{slug}
  ├── Gambar, nama, harga, harga diskon
  ├── Rating rata-rata + jumlah review
  ├── Badge "X terjual"
  ├── Deskripsi, manfaat, komposisi, cara pakai
  ├── Produk terkait (kategori sama)
  ├── Distribusi bintang (1★ sampai 5★)
  └── Daftar review pelanggan + foto

[Halaman Tentang] → /tentang
[Halaman Kontak]  → /kontak

[Auth]
  ├── Register → /register
  └── Login    → /login
```

#### 2. Keranjang Belanja (Login Diperlukan)

```
[Keranjang] → /keranjang
  ├── Daftar item keranjang
  ├── Checkbox per item (select individual)
  ├── Checkbox "Pilih Semua"
  ├── Update kuantitas (+/-) dengan AJAX reaktif
  ├── Hapus item individual
  ├── Hapus semua item
  ├── Subtotal per item (real-time)
  └── Ringkasan total (hanya item terpilih)

Alur:
  Tambah ke Keranjang → Pilih Item → Cek Stok →
  Hitung Subtotal → Lanjut Checkout
```

#### 3. Checkout

```
[Checkout] → /checkout
  ├── Pilih / tambah alamat pengiriman
  ├── Pilih metode pengiriman (flat rate, kurir)
  ├── Estimasi hari pengiriman
  ├── Pilih metode pembayaran:
  │   ├── COD (Bayar di Tempat)
  │   ├── DANA
  │   ├── GoPay
  │   ├── QRIS
  │   └── Transfer Bank (tampilkan rekening tujuan)
  ├── Catatan pesanan (opsional)
  ├── Ringkasan: subtotal + ongkir + total
  └── Tombol "Buat Pesanan"

Setelah submit:
  ├── COD → Status: "processing" (langsung dikemas)
  └── Non-COD → Status: "pending" + deadline bayar 2 jam
```

#### 4. Dashboard Customer

```
[Profil Akun] → /akun/profil
  ├── Edit nama, email, nomor HP
  └── Ganti password

[Alamat Pengiriman] → /akun/alamat
  ├── Tambah alamat baru
  ├── Hapus alamat
  └── Set alamat default

[Daftar Pesanan] → /pesanan
  ├── Filter status: semua | belum bayar | dikemas | dikirim | selesai | dibatalkan
  ├── Kode pesanan format: #BHT-YYYYMMDD-XXXXX
  ├── Informasi: tanggal, total, status, metode bayar
  └── Aksi cepat: lihat detail, bayar, batal

[Detail Pesanan] → /pesanan/{id}
  ├── Informasi lengkap: produk, jumlah, harga
  ├── Alamat pengiriman tujuan
  ├── Info pembayaran & metode
  ├── Nomor resi / kurir (jika sudah dikirim)
  ├── Timeline status pesanan
  ├── Tombol "Bayar Sekarang" → jika pending & belum expired
  ├── Tombol "Batalkan Pesanan" → jika < 2 jam & belum dikirim
  ├── Tombol "Beli Lagi" → tambahkan item ke keranjang baru
  └── Seksi Ulasan (jika status = completed):
      ├── Daftar produk yang bisa diulas
      ├── Badge "✓ Sudah Diulas" jika sudah
      └── Modal ulasan: pilih bintang + komentar + foto
```

---

### Sisi Admin

#### Akses & Keamanan

```
Admin masuk lewat: /login (sama dengan customer)
  └── Middleware 'admin' redirect ke /admin/*
  └── Middleware 'super_admin' untuk fitur sensitif
  └── Middleware 'update_session_activity' catat sesi aktif
```

#### 5. Dashboard Admin → /admin

```
[Dashboard] → /admin
  ├── Statistik ringkas:
  │   ├── Total Pendapatan (status revenue: paid/processing/shipped/completed)
  │   ├── Total Pesanan
  │   ├── Total Pelanggan
  │   └── Total Produk
  ├── Ringkasan Stok:
  │   ├── Stok Aman
  │   ├── Stok Peringatan (di bawah minimum)
  │   └── Stok Habis
  ├── Pesanan Terbaru (5 pesanan terakhir)
  └── Tabel "Produk Terlaris & Rating Terbaik":
      ├── Nama produk + thumbnail
      ├── Rating (bintang ★ + angka)
      ├── Jumlah review
      ├── Total terjual
      └── Status stok + tombol Edit
```

#### 6. Manajemen Produk → /admin/products

```
[Daftar Produk]
  ├── Filter: kategori, status stok
  ├── Pencarian nama produk
  └── Tabel: nama, harga, stok, status, rating, aksi

[Tambah Produk] → /admin/products/create
  ├── Nama, slug (auto-generate)
  ├── Harga normal & harga diskon
  ├── Stok
  ├── Deskripsi, manfaat, komposisi, cara pakai
  ├── Gambar produk
  ├── Pilih kategori (multi-select)
  ├── Toggle: Produk Unggulan, Produk Terlaris
  └── Status: auto-resolve dari stok

[Edit Produk] → /admin/products/{id}/edit
  └── Sama seperti tambah + form update stok langsung

[Update Stok] → PATCH /admin/products/{id}/stock
  └── Input stok baru → status otomatis terupdate
```

#### 7. Manajemen Kategori → /admin/categories

```
├── Daftar kategori + jumlah produk per kategori
├── Tambah kategori (nama, slug)
├── Edit kategori (inline)
└── Hapus kategori (jika tidak ada produk)
```

#### 8. Manajemen Pesanan → /admin/orders

```
[Daftar Pesanan]
  ├── Filter: status, tanggal, metode bayar
  ├── Pencarian: kode pesanan, nama pelanggan
  └── Export: CSV/Excel semua data pesanan

[Detail Pesanan] → /admin/orders/{id}
  ├── Info pelanggan + alamat tujuan
  ├── Daftar produk dalam pesanan
  ├── Total pembayaran + metode + status
  ├── Riwayat perubahan status (timeline)
  └── Form update status:
      ├── pending → processing → shipped → completed
      ├── Input: nomor resi, nama kurir
      ├── Input: catatan update (opsional)
      └── Estimasi tanggal tiba

Status Flow Admin:
  pending → paid → processing → shipped → completed
                                        ↘ cancelled
```

#### 9. Manajemen Pelanggan → /admin/customers

```
[Daftar Pelanggan]
  ├── Total pelanggan
  ├── Pencarian nama / email
  └── Tabel: nama, email, no. HP, total pesanan, bergabung

[Detail Pelanggan] → /admin/customers/{id}
  ├── Info profil lengkap
  ├── Daftar alamat terdaftar
  ├── Riwayat semua pesanan
  └── Statistik: total belanja, pesanan selesai
```

#### 10. Laporan Penjualan → /admin/reports

```
[Laporan]
  ├── Filter periode: tanggal awal - tanggal akhir
  ├── Grafik pendapatan per periode
  ├── Tabel penjualan per produk
  ├── Ringkasan: total pendapatan, total pesanan, rata-rata order
  └── Export laporan: CSV/Excel
```

#### 11. Pengaturan Toko → /admin/settings/*

```
[Pengaturan Toko] → /admin/settings/store
  ├── Nama toko, alamat, kota, provinsi
  ├── Email, nomor WhatsApp
  └── Link media sosial

[Pengaturan Pembayaran] → /admin/settings/payment
  ├── Aktifkan / nonaktifkan metode: COD, DANA, GoPay, QRIS, Transfer
  ├── Biaya layanan COD
  └── Kelola rekening bank:
      ├── Tambah rekening (bank, nama, nomor rekening)
      ├── Edit rekening
      └── Hapus rekening

[Pengaturan Pengiriman] → /admin/settings/shipping
  ├── Biaya pengiriman (flat rate)
  ├── Threshold free ongkir (minimum belanja gratis ongkir)
  └── Estimasi hari kirim per kurir

[Pengaturan Notifikasi] → /admin/settings/notification
  ├── Aktifkan notifikasi email
  ├── Aktifkan notifikasi WhatsApp
  ├── Kirim test email
  └── Kirim test WhatsApp

[Pengaturan Produk] → /admin/settings/product
  ├── Auto-nonaktif produk saat stok = 0
  ├── Auto-warning saat stok di bawah minimum
  ├── Batas stok minimum (default: 10)
  └── Jenis notifikasi stok: dashboard / email
```

#### 12. Manajemen Admin (Super Admin Only)

```
[Daftar Admin] → /admin/admins
  ├── Hanya bisa diakses Super Admin
  ├── Tambah admin baru (nama, email, role)
  ├── Edit data admin
  └── Hapus admin (kecuali diri sendiri)

[Keamanan] → /admin/security
  ├── Overview keamanan sistem
  └── Hanya dapat diakses Super Admin
```

#### 13. Sesi Admin → /admin/sessions

```
[Sesi Aktif]
  ├── Daftar semua sesi login aktif (per device)
  ├── Info: IP, device/browser, waktu aktif terakhir
  ├── Hentikan sesi spesifik
  └── Hentikan semua sesi (logout paksa)
```

#### 14. Notifikasi Admin → /admin/notifications

```
[Notifikasi]
  ├── Badge jumlah notifikasi belum dibaca (real-time polling)
  ├── Dropdown notifikasi terbaru di header
  ├── Halaman notifikasi lengkap
  ├── Tandai satu notifikasi sebagai dibaca
  ├── Tandai semua sebagai dibaca
  └── Hapus notifikasi

Jenis Notifikasi:
  ├── ⚠️ Stok produk di bawah minimum (warning)
  ├── 🔴 Stok produk habis → produk dinonaktifkan (danger)
  └── ℹ️ Info sistem lainnya (info)
```

---

## Fitur Detail Per Modul

### Modul Produk

| Fitur | Keterangan |
|---|---|
| **Auto Slug** | Slug dibuat otomatis dari nama produk saat dibuat |
| **Auto Status Stok** | Status `active`, `warning`, `inactive` otomatis berdasarkan level stok |
| **Harga Diskon** | Jika `discount_price` diisi, harga coret tampil otomatis |
| **Persentase Diskon** | Dihitung otomatis: `(harga - diskon) / harga × 100%` |
| **Rating Rata-rata** | Dihitung otomatis dari semua review, simpan di `products.rating` |
| **Sales Count** | Dihitung otomatis dari order dengan status revenue |

### Modul Pesanan

| Status | Label | Deskripsi |
|---|---|---|
| `pending` | Belum Bayar | Pesanan dibuat, menunggu pembayaran (non-COD) |
| `paid` | Sudah Bayar | Pembayaran dikonfirmasi |
| `processing` | Sedang Dikemas | Pesanan diproses (COD langsung masuk sini) |
| `shipped` | Dikirim | Pesanan dalam pengiriman + nomor resi |
| `completed` | Selesai | Pesanan diterima pelanggan |
| `cancelled` | Dibatalkan | Dibatalkan (customer/admin/otomatis) |

### Modul Pembayaran

| Metode | Jenis | Keterangan |
|---|---|---|
| COD | Tunai | Bayar saat barang tiba, pesanan langsung `processing` |
| DANA | E-Wallet | Bayar via DANA, bukti transfer di-upload |
| GoPay | E-Wallet | Bayar via GoPay |
| QRIS | QR Code | Scan QRIS di app apapun |
| Bank Transfer | Bank | Transfer ke rekening terdaftar |

### Modul Review & Rating

```
Alur Review:
  Order selesai (completed)
    → Customer ke detail pesanan
    → Klik "Tulis Ulasan" per produk
    → Modal: pilih bintang (1-5) + komentar + foto (opsional)
    → Submit → ReviewObserver::created() triggered
    → Product::updateRatingStats() → rating & rating_count terupdate
    → Badge "✓ Sudah Diulas" muncul
```

---

## Siklus Hidup Pesanan

```
Customer ──── Checkout ──────────────────────────────────────────┐
                │                                                 │
             COD?  ────── Ya ──→ status: "processing"            │
                │                       │                        │
                No                      │                        │
                │                       ▼                        │
                └──→ status: "pending"  Admin kemas produk       │
                         │              │                        │
                    (2 jam limit)       │                        │
                         │          Admin update: "shipped"      │
                    bayar / expired     │ (input resi + kurir)   │
                         │              │                        │
                    status: "paid"      ▼                        │
                         │          status: "shipped"            │
                         │              │                        │
                    Admin konfirmasi    │ (barang tiba)          │
                         │              ▼                        │
                         └──→ "processing" → "shipped" → "completed"
                                                              │
                                                    Customer bisa tulis review
```

---

## Sistem Otomatisasi

### Observer Pattern

| Observer | Event | Aksi |
|---|---|---|
| `ReviewObserver` | `created`, `updated`, `deleted` | Hitung ulang `rating` & `rating_count` produk |
| `OrderItemObserver` | `saved` | Hitung ulang `sales_count` produk terkait |

### Artisan Commands Terjadwal

```bash
# Batalkan pesanan non-COD yang melewati deadline 2 jam
php artisan app:cancel-expired-orders

# Recalculate semua statistik produk (rating & sales)
php artisan app:initialize-product-stats
```

### Auto Stock Management

```
Stok diubah → Product::resolveStatus() dipanggil:
  stok = 0          → status: "inactive" + notifikasi "bahaya"
  stok ≤ minimum    → status: "warning"  + notifikasi "peringatan"
  stok > minimum    → status: "active"

Notifikasi dikirim ke:
  ├── Dashboard admin (notifikasi panel)
  └── Email admin (jika diaktifkan di settings)
```

---

## Platform-Specific Features

### Web (Progressive Web App)

| Fitur | Implementasi | Keunikan |
|---|---|---|
| **PWA Installable** | `manifest.json` + service worker | Bisa install dari Chrome → ikon di desktop/HP |
| **Offline Support** | Service Worker + cache strategy | Browsing produk saat offline |
| **Web Push Notification** | Push API + VAPID key | Notifikasi status pesanan di browser |
| **Drag & Drop Upload** | HTML5 Drag-and-drop API | Upload bukti transfer tanpa klik browse |

### Mobile (Flutter Android)

| Fitur | Package | Keunikan |
|---|---|---|
| **Kamera & Galeri** | `image_picker` | Langsung foto produk untuk review dari HP |
| **GPS Auto-Alamat** | `geolocator` + `geocoding` | Isi alamat pengiriman otomatis dari lokasi GPS |
| **Push Notification** | `firebase_messaging` | Notifikasi real-time status order ke HP |
| **Biometric Login** | `local_auth` | Login sidik jari / Face ID (tidak bisa di web) |

---

## Struktur Direktori

```
bharata-herbal-PABP/         ← Backend Laravel (Web + API)
├── app/
│   ├── Console/Commands/    ← Artisan commands terjadwal
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/       ← 16 controller panel admin
│   │   │   ├── Api/         ← Controller REST API (untuk mobile)
│   │   │   └── Auth/        ← Register, Login
│   │   ├── Middleware/      ← admin, super_admin, session_activity
│   │   └── Requests/        ← Form validation rules
│   ├── Models/              ← 17 Eloquent models
│   ├── Observers/           ← ReviewObserver, OrderItemObserver
│   ├── Policies/            ← CartItemPolicy
│   ├── Providers/           ← AppServiceProvider (register observers)
│   └── Services/            ← StockNotificationMailer, dll.
├── database/
│   ├── migrations/          ← 32 file migrasi database
│   └── seeders/             ← Data awal (admin, produk, kategori)
├── resources/views/
│   ├── admin/               ← 11 modul tampilan admin panel
│   ├── auth/                ← Login, register
│   ├── components/          ← Komponen reusable (product-card, dll.)
│   ├── dashboard/           ← Customer dashboard (profil, pesanan, review)
│   ├── layouts/             ← Layout utama web & admin
│   └── *.blade.php          ← Halaman publik (home, shop, detail, cart, checkout)
└── routes/
    ├── web.php              ← 50+ route untuk web
    └── api.php              ← REST API routes untuk mobile

bharata-herbal-mobile/       ← Flutter Mobile Client (terpisah)
├── lib/
│   ├── config/              ← API base URL & endpoints
│   ├── models/              ← Product, Cart, Order, User
│   ├── services/            ← API calls, Location, Notification
│   ├── providers/           ← State management
│   └── screens/             ← UI per halaman
└── android/                 ← Konfigurasi native Android (FCM, permissions)
```

---

## Instalasi dan Konfigurasi

### Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0+
- XAMPP (untuk lokal) atau server hosting

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/[username]/bharata-herbal.git
cd bharata-herbal

# 2. Install dependensi PHP
composer install

# 3. Salin file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=bharata_herbal
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi + seeder
php artisan migrate --seed

# 7. Install dependensi frontend
npm install

# 8. Build assets (development)
npm run dev

# 9. Jalankan server
php artisan serve
```

### Konfigurasi Opsional

```bash
# Aktifkan scheduler (untuk auto-cancel pesanan expired)
# Tambahkan ke cron (Linux) atau Task Scheduler (Windows):
php artisan schedule:run

# Storage link (untuk gambar produk)
php artisan storage:link

# Recalculate semua statistik produk
php artisan app:initialize-product-stats
```

---

## Data Demo & Akun Default

### Akun Tersedia Setelah Seeder

| Role | Email | Password |
|---|---|---|
| **Super Admin** | `admin@bharataherbal.id` | `password` |
| **Customer** | `customer@example.com` | `password` |

### Data Awal yang Tersedia

- **Kategori**: Pencernaan, Persendian, Ginjal, Imunitas, Stamina, dan lainnya
- **Produk**: Berbagai produk herbal lengkap dengan gambar, deskripsi, harga
- **Setting default**: Toko, pembayaran (semua aktif), pengiriman, notifikasi
- **Review testimoni**: Beberapa review awal untuk demonstrasi rating

---

## Perintah Artisan Tersedia

```bash
php artisan migrate --seed              # Setup database + data awal
php artisan db:seed --class=DatabaseSeeder  # Jalankan seeder saja
php artisan serve                       # Jalankan web server lokal
npm run dev                             # Jalankan Vite dev server
npm run build                           # Build asset produksi
php artisan app:cancel-expired-orders   # Batalkan pesanan expired
php artisan app:initialize-product-stats # Recalculate rating & sales
php artisan schedule:run                # Jalankan task terjadwal
php artisan route:list                  # Lihat semua route terdaftar
php artisan tinker                      # REPL interaktif untuk debugging
```

---

## Pembagian Tugas Tim

| Anggota | Peran | Tanggung Jawab |
|---|---|---|
| **Anggota 1** | Backend API Developer | Membuat REST API (`routes/api.php`), konfigurasi Laravel Sanctum untuk token auth, API Resource classes, CORS, dokumentasi Postman |
| **Anggota 2** | Flutter Lead Developer | Setup Flutter project, arsitektur layer (Models/Services/Providers/Screens), auth flow, product/cart/order screens, integrasi API |
| **Anggota 3** | Flutter Platform Features | Implementasi kamera (image_picker), GPS auto-isi alamat (geolocator), Firebase Push Notification (FCM), biometric login |
| **Anggota 4** | Web PWA & Dokumentasi | Implementasi PWA (manifest.json + service worker), polishing web UI, dokumen teknis lengkap, video demo, setup GitHub |

---

## Lisensi

Aplikasi ini dikembangkan di atas framework [Laravel](https://laravel.com) dan menggunakan lisensi **MIT**.

---

*Dikembangkan oleh Tim Bharata Herbal — Tugas Besar MK Pengembangan Aplikasi Berbasis Platform*
