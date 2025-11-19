# Dashboard Kelola Users - Dokumentasi

## 🎯 Overview

Dashboard satu halaman untuk mengelola users yang terintegrasi dengan Firebase Firestore.

## 📍 Akses Dashboard

**URL:** http://localhost:8000/users/dashboard

⚠️ **Note:** Halaman ini memerlukan login (protected by auth middleware)

## ✨ Fitur

### 1. **Statistik Dashboard**

-   Total Users
-   Users dengan Face Data terdaftar
-   Users tanpa Face Data

### 2. **Tabel Users**

Menampilkan semua data users dengan kolom:

-   NIP
-   Nama Lengkap (dengan avatar inisial)
-   Email
-   Status Face (Terdaftar/Belum)
-   Tanggal Dibuat
-   Aksi (View, Edit, Delete)

### 3. **CRUD Operations**

#### ➕ Tambah User

-   Klik tombol "Tambah User"
-   Isi form:
    -   NIP (required)
    -   Nama Lengkap (required)
    -   Email (required)
    -   Face Data (optional - biasanya diisi via mobile app)
-   Klik "Simpan"

#### 👁️ Lihat Detail

-   Klik icon mata (eye) pada baris user
-   Muncul modal dengan detail lengkap user

#### ✏️ Edit User

-   Klik icon edit (pencil) pada baris user
-   Update Nama Lengkap atau Email
-   NIP tidak bisa diubah (readonly)
-   Klik "Update"

#### 🗑️ Hapus User

-   Klik icon trash pada baris user
-   Konfirmasi penghapusan
-   User akan dihapus dari Firebase

## 🔌 API Endpoints yang Digunakan

Dashboard ini menggunakan API endpoints berikut:

```
GET    /api/users              - Get all users (untuk tabel)
POST   /api/users              - Create user (dari form tambah)
GET    /api/users/{id}         - Get detail user (untuk view & edit)
PUT    /api/users/{id}         - Update user (dari form edit)
DELETE /api/users/{id}         - Delete user
```

## 🎨 Teknologi

-   **Backend:** Laravel 11
-   **Database:** Firebase Firestore
-   **Frontend:** Tailwind CSS (via CDN)
-   **Icons:** Font Awesome 6
-   **JavaScript:** Vanilla JS (Fetch API)

## 📱 Responsif

Dashboard fully responsive untuk:

-   Desktop
-   Tablet
-   Mobile

## 🔐 Authentication

Dashboard protected dengan middleware:

-   `auth:sanctum`
-   `verified`

## 🚀 Cara Mengakses

1. Start Laravel server:

    ```bash
    php artisan serve
    ```

2. Login ke aplikasi:
   http://localhost:8000/login

3. Setelah login, akses dashboard users:
   http://localhost:8000/users/dashboard

    Atau klik card "Kelola Users" di dashboard utama.

## 💡 Tips

-   **Face Data:** Biasanya diisi melalui mobile app saat registrasi wajah
-   **NIP:** Digunakan sebagai document ID di Firebase
-   **Real-time:** Setiap operasi langsung sync ke Firebase Firestore
-   **Validation:** Email dan NIP otomatis tervalidasi

## 🔄 Data Flow

1. User mengisi form → Submit
2. JavaScript kirim ke API endpoint
3. Laravel Controller → FirebaseUser Model
4. Model → FirebaseService
5. FirebaseService → Firebase Firestore REST API
6. Response → User
7. Page reload dengan data terbaru

## 📝 Struktur File

```
app/
├── Http/Controllers/
│   └── UserController.php         # CRUD logic
├── Models/
│   └── FirebaseUser.php          # Firebase model
└── Services/
    └── FirebaseService.php       # Firebase REST API wrapper

resources/views/
└── users/
    └── dashboard.blade.php       # Main dashboard view

routes/
├── web.php                       # Web routes (dashboard)
└── api.php                       # API routes (CRUD)
```

## ⚡ Performance

-   Menggunakan Firebase REST API (tanpa grpc extension)
-   Efficient data fetching
-   Minimal DOM manipulation
-   Fast page load dengan Tailwind CDN

---

**Dibuat untuk:** Tugas Besar PAM - Sistem Absensi Pegawai
**Tech Stack:** Laravel + Firebase Firestore + Flutter Mobile App
