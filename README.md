# 📚 Perpustakaan Sekolah - Sistem Manajemen Perpustakaan

Sebuah aplikasi manajemen perpustakaan sekolah yang dibangun dengan **Laravel 13** dan **Vue.js** (via Vite + Tailwind CSS). Aplikasi ini mencakup fitur peminjaman buku, pengelolaan koleksi, dan API RESTful yang aman dengan **Laravel Sanctum**.

---

## 🎯 Fitur Utama

### Untuk Admin
- 📖 **Manajemen Buku** - CRUD buku dengan kategori, ISBN, stok, dll
- 👥 **Manajemen Anggota** - Kelola data siswa yang terdaftar
- 💳 **Transaksi Peminjaman** - Monitoring dan validasi peminjaman
- 📊 **Laporan & Dashboard** - Statistik peminjaman dan pengembalian
- 🏷️ **Kategori Buku** - Kelola kategori koleksi

### Untuk Siswa
- 📚 **Browse & Cari Buku** - Lihat koleksi buku yang tersedia
- 🔐 **Peminjaman Buku** - Pinjam buku dengan sistem otomatis
- ↩️ **Riwayat Peminjaman** - Lihat riwayat peminjaman dan pengembalian
- 👤 **Profil Pengguna** - Kelola data pribadi

### API
- ✅ Autentikasi via Token (Sanctum)
- 📡 REST API untuk semua fitur utama
- 🔒 Role-based access control
- 📄 JSON response yang terstruktur

---

## 🛠️ Requirement Sistem

### Minimum
- **PHP 8.3+**
- **Composer** (Package Manager PHP)
- **Node.js 18+** (untuk frontend assets)
- **npm** atau **yarn**
- **SQLite** (default) atau **MySQL/PostgreSQL**

### Recommended
- **Laravel Artisan** CLI tools
- **Postman** atau **Insomnia** (untuk testing API)

---

## ⚡ Quick Start (3 Langkah)

### 1️⃣ Clone & Setup Awal

```bash
# Clone repository
git clone <repository-url>
cd perpustakaan-sekolah

# Jalankan setup otomatis
composer run setup
```

**Atau setup manual:**

```bash
# Install PHP dependencies
composer install

# Copy .env configuration
cp .env.example .env

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate --force

# Install JavaScript dependencies
npm install

# Build frontend assets
npm run build
```

### 2️⃣ Akses Aplikasi

```bash
# Start development server
php artisan serve
# Aplikasi akan tersedia di http://localhost:8000
```

```bash
# Di terminal lain, jalankan Vite dev server (untuk auto-refresh)
npm run dev
```

### 3️⃣ Login

```
Email Admin: admin@perpustakaan.test
Password: password

Email Siswa: siswa@perpustakaan.test
Password: password
```

---

## 🗄️ Database Setup

### Struktur Database

Aplikasi menggunakan 4 tabel utama:

```
┌──────────────┐
│    Users     │ (admin, siswa)
├──────────────┤
│ id (PK)      │
│ name         │
│ username     │
│ email        │
│ password     │
│ role         │ ← 'admin' atau 'siswa'
│ nis          │
│ kelas        │
│ no_hp        │
│ alamat       │
│ is_active    │
└──────────────┘
        │
        ├──────┐
        │      │
        ▼      ▼
┌──────────────┐  ┌───────────────┐
│   Pinjams    │  │    Bukus      │
├──────────────┤  ├───────────────┤
│ id (PK)      │  │ id (PK)       │
│ user_id (FK) │  │ kategori_id   │
│ buku_id (FK) │  │ kode_buku     │
│ tanggal_pij  │  │ judul         │
│ tanggal_kmbk │  │ pengarang     │
│ denda        │  │ penerbit      │
│ status       │  │ tahun_terbit  │
│              │  │ isbn          │
│              │  │ stok          │
│              │  │ stok_tersedia │
│              │  │ deskripsi     │
│              │  │ sampul        │
│              │  │ rak           │
└──────────────┘  └───────────────┘
        ▲                 ▲
        │                 │
        └─────────────────┘
        
┌──────────────────┐
│    Kategoris     │
├──────────────────┤
│ id (PK)          │
│ nama             │
│ deskripsi        │
└──────────────────┘
```

### Jalankan Migration

```bash
# Run semua migration
php artisan migrate

# Jika ada perubahan schema
php artisan migrate:fresh --seed

# Lihat status migration
php artisan migrate:status
```

### Seed Data (Opsional)

```bash
# Seed database dengan data dummy
php artisan db:seed

# Atau run seeding spesifik
php artisan db:seed --class=DatabaseSeeder
```

---

## 🔐 Authentication & Login

### Web (Browser)

#### Login Page
- **URL**: `/login`
- **Method**: GET

#### Login Process
1. Buka `http://localhost:8000`
2. Masuk di halaman login
3. Gunakan kredensial sesuai role:

**Admin:**
```
username/email: admin@perpustakaan.test
password: password
```

**Siswa:**
```
username/email: siswa@perpustakaan.test
password: password
```

4. Akan diredirect ke dashboard sesuai role

#### Register (Siswa Baru)
- **URL**: `/register`
- **Field**: name, username, email, password, kelas, NIS
- Setelah register, akun akan pending approval dari admin

#### Logout
```bash
POST /logout
```

---

## 📡 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication

#### 1. Register (Publik)
```http
POST /register
Content-Type: application/json

{
  "name": "Budi Santoso",
  "username": "budi_santoso",
  "email": "budi@sekolah.test",
  "password": "password123",
  "kelas": "XII-A",
  "nis": "1234567890"
}

Response 201:
{
  "success": true,
  "message": "Registrasi berhasil. Tunggu persetujuan admin",
  "data": {
    "user": {...}
  }
}
```

#### 2. Login (Publik)
```http
POST /login
Content-Type: application/json

{
  "email": "admin@perpustakaan.test",
  "password": "password"
}

Response 200:
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin",
      "email": "admin@perpustakaan.test",
      "role": "admin"
    },
    "token": "1|aBcD1234eFgH5678ijKlMnOpQrStUvWxyz"
  }
}
```

**Gunakan token di header untuk request berikutnya:**
```http
Authorization: Bearer 1|aBcD1234eFgH5678ijKlMnOpQrStUvWxyz
```

#### 3. Logout (Protected)
```http
POST /logout
Authorization: Bearer <token>

Response 200:
{
  "success": true,
  "message": "Logout berhasil"
}
```

#### 4. Get Current User (Protected)
```http
GET /me
Authorization: Bearer <token>

Response 200:
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Admin",
    "email": "admin@perpustakaan.test",
    "role": "admin"
  }
}
```

---

### Buku (Books)

#### List Semua Buku (Protected)
```http
GET /buku
Authorization: Bearer <token>

Query Parameters:
  - page: int (default: 1)
  - per_page: int (default: 15)
  - search: string (cari judul/pengarang)
  - kategori_id: int (filter kategori)

Response 200:
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "kode_buku": "BK001",
        "judul": "Pemrograman PHP",
        "pengarang": "Andl Heriyanto",
        "penerbit": "Informatika",
        "tahun_terbit": 2020,
        "isbn": "978-1234567890",
        "stok": 5,
        "stok_tersedia": 3,
        "kategori": {
          "id": 2,
          "nama": "Komputer & IT"
        },
        "deskripsi": "Panduan lengkap PHP modern",
        "rak": "A1"
      }
    ],
    "pagination": {
      "current_page": 1,
      "total": 45,
      "per_page": 15,
      "last_page": 3
    }
  }
}
```

#### Detail Buku (Protected)
```http
GET /buku/{id}
Authorization: Bearer <token>

Response 200:
{
  "success": true,
  "data": {
    "id": 1,
    "kode_buku": "BK001",
    "judul": "Pemrograman PHP",
    "pengarang": "Andl Heriyanto",
    "penerbit": "Informatika",
    "tahun_terbit": 2020,
    "isbn": "978-1234567890",
    "stok": 5,
    "stok_tersedia": 3,
    "kategori": {...},
    "deskripsi": "Panduan lengkap PHP modern",
    "rak": "A1",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

#### Tambah Buku (Admin Only)
```http
POST /buku
Authorization: Bearer <admin_token>
Content-Type: application/json

{
  "kode_buku": "BK045",
  "judul": "Data Science dengan Python",
  "pengarang": "Edward Smith",
  "penerbit": "Tech Press",
  "tahun_terbit": 2023,
  "isbn": "978-0987654321",
  "kategori_id": 2,
  "stok": 8,
  "deskripsi": "Belajar data science dari pemula hingga expert",
  "rak": "B2"
}

Response 201: Created
```

#### Update Buku (Admin Only)
```http
PUT /buku/{id}
Authorization: Bearer <admin_token>
Content-Type: application/json

{
  "judul": "Data Science dengan Python (2nd Edition)",
  "stok": 10
}

Response 200: OK
```

#### Hapus Buku (Admin Only)
```http
DELETE /buku/{id}
Authorization: Bearer <admin_token>

Response 200: Deleted
```

---

### Peminjaman (Loans)

#### List Peminjaman User (Protected)
```http
GET /peminjaman
Authorization: Bearer <token>

Query Parameters:
  - page: int
  - per_page: int
  - status: string (dipinjam, dikembalikan, hilang)

Response 200:
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "buku_id": 5,
        "buku": {
          "id": 5,
          "judul": "PHP Modern",
          "pengarang": "Taylor Otwell"
        },
        "user_id": 3,
        "tanggal_pinjam": "2025-03-29",
        "tanggal_kembali": null,
        "tanggal_kembali_seharusnya": "2025-04-12",
        "denda": 0,
        "status": "dipinjam"
      }
    ],
    "pagination": {...}
  }
}
```

#### Peminjaman Aktif (Protected)
```http
GET /peminjaman/aktif
Authorization: Bearer <token>

Response 200: Daftar buku yang sedang dipinjam termasuk durasi dan deadline
```

#### Riwayat Peminjaman (Protected)
```http
GET /peminjaman/riwayat
Authorization: Bearer <token>

Response 200: Semua riwayat peminjaman (yang sudah dikembalikan/hilang)
```

#### Buat Peminjaman Baru (Protected)
```http
POST /peminjaman
Authorization: Bearer <token>
Content-Type: application/json

{
  "buku_id": 5,
  "durasi_hari": 14
}

Response 201:
{
  "success": true,
  "message": "Peminjaman berhasil",
  "data": {
    "id": 1,
    "buku_id": 5,
    "tanggal_pinjam": "2025-03-29",
    "tanggal_kembali_seharusnya": "2025-04-12",
    "status": "dipinjam"
  }
}

Possible Errors:
- 400: Stok buku tidak tersedia
- 400: User sudah mencapai batas peminjaman
- 400: User memiliki denda belum lunas
```

#### Kembalikan Buku (Protected)
```http
POST /peminjaman/{id}/kembalikan
Authorization: Bearer <token>

Response 200:
{
  "success": true,
  "message": "Buku berhasil dikembalikan",
  "data": {
    "id": 1,
    "status": "dikembalikan",
    "denda": 0,
    "tanggal_kembali": "2025-04-08"
  }
}

Jika terlambat:
{
  "denda": 15000,
  "hari_terlambat": 3
}
```

---

### Dashboard

#### Dashboard Stats (Protected)
```http
GET /dashboard
Authorization: Bearer <token>

Response 200:
{
  "success": true,
  "data": {
    "total_buku": 150,
    "total_member": 45,
    "peminjaman_aktif": 28,
    "pengembalian_terlambat": 3,
    "denda_belum_dibayar": 450000,
    "kategori_favorite": [
      ["Fiksi Thriller", 12],
      ["Komik", 8]
    ]
  }
}
```

---

### Anggota (Members) - Admin Only

#### List Anggota (Admin Only)
```http
GET /anggota
Authorization: Bearer <admin_token>

Response 200: List semua pengguna sistem
```

#### Detail Anggota (Admin Only)
```http
GET /anggota/{id}
Authorization: Bearer <admin_token>

Response 200: Detail user termasuk riwayat peminjaman
```

#### Tambah Anggota (Admin Only)
```http
POST /anggota
Authorization: Bearer <admin_token>
Content-Type: application/json

{
  "name": "Siswa Baru",
  "username": "siswa_baru",
  "email": "siswa_baru@sekolah.test",
  "password": "password123",
  "kelas": "X-A",
  "nis": "9999999999",
  "role": "siswa"
}
```

#### Update Anggota (Admin Only)
```http
PUT /anggota/{id}
Authorization: Bearer <admin_token>

{
  "name": "Nama Baru",
  "kelas": "XI-A",
  "is_active": true
}
```

#### Hapus Anggota (Admin Only)
```http
DELETE /anggota/{id}
Authorization: Bearer <admin_token>
```

---

## 🧪 Testing API Dengan Postman

### Import Collection

1. **Download Postman**: https://www.postman.com/downloads/
2. **Buat Collection** → Nama: "Perpustakaan API"
3. **Tambah Request**:

### Environment Variables

Buat environment dengan variabel:
```
base_url: http://localhost:8000/api/v1
token: <diisi setelah login>
admin_token: <diisi setelah login sebagai admin>
```

### Flow Testing

1. **Register User Baru**
   ```
   POST {{base_url}}/register
   ```

2. **Login**
   ```
   POST {{base_url}}/login
   Set token dari response
   ```

3. **Get Buku**
   ```
   GET {{base_url}}/buku
   Header: Authorization: Bearer {{token}}
   ```

4. **Buat Peminjaman**
   ```
   POST {{base_url}}/peminjaman
   Header: Authorization: Bearer {{token}}
   Body: { "buku_id": 1, "durasi_hari": 14 }
   ```

---

## 📁 Struktur Project

```
perpustakaan-sekolah/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # Web auth
│   │   │   ├── Api/
│   │   │   │   ├── AuthApiController.php   # API auth
│   │   │   │   ├── BukuApiController.php   # API buku
│   │   │   │   ├── PeminjamanApiController.php
│   │   │   │   ├── DashboardApiController.php
│   │   │   │   └── AnggotaApiController.php
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php
│   │   │   │   ├── BukuController.php
│   │   │   │   ├── AnggotaController.php
│   │   │   │   ├── TransaksiController.php
│   │   │   │   └── KategoriController.php
│   │   │   └── Siswa/
│   │   │       ├── DashboardController.php
│   │   │       ├── PeminjamanController.php
│   │   │       └── PengembalianController.php
│   │   └── Middleware/
│   │       ├── RoleMiddleware.php           # Check role (admin/siswa)
│   │       └── Authenticate.php
│   └── Models/
│       ├── User.php
│       ├── Buku.php
│       ├── Pinjam.php
│       └── Kategori.php
├── database/
│   ├── migrations/                          # Schema database
│   │   ├── 2025_01_01_000001_create_users_table.php
│   │   ├── 2025_01_01_000002_create_kategoris_table.php
│   │   ├── 2025_01_01_000003_create_bukus_table.php
│   │   ├── 2025_01_01_000004_create_pinjams_table.php
│   │   └── 2025_01_01_000005_create_sessions_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php               # Seed data
│   └── perpustakaan.sql                     # SQL dump (opsional)
├── routes/
│   ├── web.php                              # Web routes (browser)
│   └── api.php                              # API routes (JSON)
├── resources/
│   ├── views/                               # Blade templates
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── admin/
│   │   └── siswa/
│   ├── css/
│   │   └── app.css                          # Tailwind styles
│   └── js/
│       └── app.js                           # Frontend logic
├── config/
│   ├── auth.php                             # Auth config
│   ├── database.php                         # Database config
│   ├── sanctum.php                          # API token config
│   └── app.php                              # App config
├── .env.example                             # Environment template
├── composer.json                            # PHP dependencies
├── package.json                             # Node dependencies
├── vite.config.js                           # Frontend builder config
└── README.md                                # Dokumentasi ini
```

---

## ⚙️ Konfigurasi

### .env Configuration

Salin `.env.example` → `.env` dan sesuaikan:

```env
# App
APP_NAME=PerpustakaanSekolah
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite default)
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/database.sqlite

# Atau gunakan MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_sekolah
DB_USERNAME=root
DB_PASSWORD=

# Session & Auth
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Queue
QUEUE_CONNECTION=database

# Cache
CACHE_STORE=database

# Sanctum (API Token)
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### Ganti Database menjadi MySQL

```bash
# Edit .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_sekolah
DB_USERNAME=root
DB_PASSWORD=your_password

# Run migration
php artisan migrate:fresh --seed
```

---

## 🚀 Development Commands

### Server & Building

```bash
# Start Laravel development server (http://localhost:8000)
php artisan serve

# Start Vite dev server (auto-refresh frontend)
npm run dev

# Build untuk production
npm run build

# Run combined dev environment (server + vite + logs)
composer run dev
```

### Database

```bash
# Run migration
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Fresh migration + seed
php artisan migrate:fresh --seed

# Check migration status
php artisan migrate:status
```

### Artists Management

```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Tinker (Interactive PHP shell)
php artisan tinker
```

### Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test tests/Feature/AuthTest.php

# Run with coverage
php artisan test --coverage
```

---

## 🔍 Troubleshooting

### Problem: "SQLSTATE[HY000]: General error: 1 unable to open database file"

**Solution:**
```bash
# Generate SQLite database
touch database/database.sqlite

# Run migration
php artisan migrate
```

### Problem: "No application encryption key has been specified"

**Solution:**
```bash
php artisan key:generate
```

### Problem: "Class not found" pada migration

**Solution:**
```bash
# Clear cached
composer dump-autoload
php artisan optimize:clear
```

### Problem: Vite tidak auto-refresh

**Solution:**
```bash
# Stop Vite server
# Clear npm cache
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Problem: Login tidak berfungsi

**Solution:**
```bash
# Cek session table
php artisan migrate

# Clear semua sessions
php artisan session:table
php artisan migrate

# Clear cookies browser
```

### Problem: Token API tidak valid

**Solution:**
```bash
# Regenerate Sanctum key di .env
SANCTUM_ENCRYPTION_KEY= (kosongkan)

# Atau generate ulang
php artisan key:generate
```

---

## 👥 Support & Contact

Jika ada pertanyaan atau bug:
1. Check dokumentasi di `dokumentasi/DOKUMENTASI_PROGRAM.html`
2. Check ERD di `dokumentasi/ERD.html`
3. Review database schema di `database/perpustakaan.sql`

---

## 📜 License

MIT License - Bebas digunakan untuk keperluan pendidikan dan komersial.

---

## 🎓 Learning Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Laravel API Authentication**: https://laravel.com/docs/sanctum
- **Tailwind CSS**: https://tailwindcss.com
- **Vue.js**: https://vuejs.org
- **RESTful API Best Practices**: https://restfulapi.net

---

**Last Updated**: 30 Maret 2026  
**Version**: 1.0.0
