# WO & QC System

Sistem manajemen Work Order dan Quality Control untuk lini produksi manufacturing.

## Tech Stack

- Laravel 12 + PHP 8.2
- MySQL (InnoDB)
- Tailwind CSS + Laravel Breeze
- Chart.js (doughnut charts)

## Fitur

| Fitur | Deskripsi |
|-------|-----------|
| Work Order | CRUD WO — nomor (unique), tanggal, produk, qty_order, status |
| Input Produksi | Catat output per WO — over-production guard aktif — track operator |
| Input QC | Catat Good / Not Good per WO — over-QC guard aktif — track QC by |
| Role-based Dashboard | PPIC, Operator, QC, Manager, Super Admin — masing-masing scoped |
| User Management | CRUD user accounts — Super Admin only |
| Dasbor Monitoring | 6 metric real-time + search + status filter |
| WO Status | Otomatis — In Progress / Prod. Selesai / Fully QC'd |
| Manager Charts | Doughnut charts — distribusi status, QC pass rate |
| Audit Trail | operator_id & qc_by tracking |

## Struktur Database

```
work_orders ──(1:N)── productions
     │
     └───(1:N)── quality_controls

users ──(1:N)── productions (operator_id)
users ──(1:N)── quality_controls (qc_by)
```

| Tabel | Kolom Kunci |
|-------|-------------|
| `work_orders` | `id`, `wo_number`, `date`, `product`, `qty_order`, `status` |
| `productions` | `id`, `work_order_id` (FK), `operator_id` (FK), `qty_production`, `production_date` |
| `quality_controls` | `id`, `work_order_id` (FK), `qc_by` (FK), `qty_good`, `qty_not_good`, `qc_date` |

## Role & Akses

| Role | Buat WO | Lihat WO | Input Prod | Input QC | Dashboard |
|------|:-------:|:--------:|:----------:|:--------:|:---------:|
| Super Admin | ✅ | ✅ | ✅ | ✅ | Full Access |
| PPIC | ✅ | ✅ | ❌ | ❌ | WO & Progress |
| Operator | ❌ | ✅ | ✅ | ❌ | Produksi Saya |
| QC | ❌ | ❌ | ❌ | ✅ | QC Queue |
| Manager | ❌ | ✅ (read) | ❌ | ❌ | Agregat All |

Sidebar navigation otomatis menyesuaikan menu berdasarkan role login.

## WO Status

| Status | Keterangan |
|--------|------------|
| `in_progress` | WO baru atau produksi belum selesai |
| `prod_complete` | Produksi selesai, QC belum semua |
| `fully_qc` | Semua QC selesai |

Status di-set otomatis oleh `WorkOrderStatusService` saat input produksi/QC.

## Validasi Inti

- **Over-production**: `(SUM produksi + input) <= qty_order` — ditolak kalau melebihi sisa
- **Over-QC**: `(SUM QC + input) <= SUM produksi` — ditolak kalau melebihi sisa QC
- **WO number**: UNIQUE constraint di database

## Instalasi

```bash
# 1. Clone & install
composer install
cp .env.example .env
php artisan key:generate

# 2. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE wo_cq"

# 3. Migration + seeder
php artisan migrate
php artisan db:seed --class=UserRoleSeeder

# 4. Frontend
npm install
npm run dev   # development
# atau
npm run build  # production

# 5. Jalankan
php artisan serve
```

Akses di `http://localhost:8000`

## User Test

| Email | Password | Role |
|-------|----------|------|
| `admin@example.com` | `password` | Super Admin |
| `ppic@example.com` | `password` | PPIC |
| `operator@example.com` | `password` | Operator |
| `qc@example.com` | `password` | QC |
| `manager@example.com` | `password` | Manager |

## Route

```
Dashboard:
GET   /dashboard/ppic         Dasbor PPIC
GET   /dashboard/operator     Dasbor Operator
GET   /dashboard/qc           Dasbor QC
GET   /dashboard/manager      Dasbor Manager
GET   /dashboard/super-admin  Dasbor Super Admin

User Management (Super Admin only):
GET    /users                 Daftar user
GET    /users/create          Form tambah user
POST   /users                Simpan user baru
GET    /users/{id}/edit      Form edit user
PUT    /users/{id}           Update user
DELETE /users/{id}           Hapus user

Work Orders:
GET    /work-orders           Daftar WO
GET    /work-orders/create    Form buat WO
POST   /work-orders          Simpan WO baru
GET    /work-orders/{id}     Detail WO
GET    /work-orders/{id}/edit   Form edit WO
PUT    /work-orders/{id}     Update WO
DELETE /work-orders/{id}     Hapus WO (Super Admin only)

Productions:
GET    /productions          Input & log produksi
POST   /productions          Simpan produksi

Quality Controls:
GET    /quality-controls     Input & log QC
POST   /quality-controls      Simpan QC
```

## Desain Sistem

**Single Source of Truth** — semua metric dihitung dari tabel sumber via Eloquent `withSum()`.

**Status Column** — status WO disimpan di kolom `status`, di-set otomatis oleh `WorkOrderStatusService` saat input produksi/QC.

**Audit Trail** — `operator_id` dan `qc_by` tracking siapa yang input data.

**Foreign Key ON DELETE CASCADE** — saat Work Order dihapus, semua record produksi dan QC terkait ikut terhapus otomatis.

**Database Engine InnoDB** — semua tabel menggunakan InnoDB agar foreign key constraint berjalan penuh.
