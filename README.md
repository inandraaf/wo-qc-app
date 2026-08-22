# WO & QC System

Sistem manajemen Work Order dan Quality Control untuk lini produksi manufacturing.

## Tech Stack

- Laravel 12 + PHP 8.2
- MySQL (InnoDB)
- Tailwind CSS + Laravel Breeze

## Fitur

| Fitur | Deskripsi |
|-------|-----------|
| Work Order | CRUDWO — nomor (unique), tanggal, produk, qty_order |
| Input Produksi | Catat output per WO — over-production guard aktif |
| Input QC | Catat Good / Not Good per WO — over-QC guard aktif |
| Dasbor Monitoring | 6 metric real-time per WO + search + status filter |
| WO Status Badge | Otomatis — Belum Produksi / In Progress / Prod. Selesai / Fully QC'd |
| Role-based Access | PPIC, Operator, QC, Manager |
| Dark Sidebar UI | Industrial command center design |

## Struktur Database

```
work_orders ──(1:N)── productions
     │
     └───(1:N)── quality_controls
```

| Tabel | Kolom Kunci |
|-------|-------------|
| `work_orders` | `id`, `wo_number` (UNIQUE), `date`, `product`, `qty_order` |
| `productions` | `id`, `work_order_id` (FK → work_orders, ON DELETE CASCADE), `qty_production`, `production_date` |
| `quality_controls` | `id`, `work_order_id` (FK → work_orders, ON DELETE CASCADE), `qty_good`, `qty_not_good`, `qc_date` |

## Validasi Inti

- **Over-production**: `(SUM produksi + input) <= qty_order` — ditolak kalau melebihi sisa
- **Over-QC**: `(SUM QC + input) <= SUM produksi` — ditolak kalau melebihi sisa QC
- **WO number**: UNIQUE constraint di database

## Monitoring Metric (per WO)

```
Qty Order         = qty_order (dari work_orders)
Total Produksi    = SUM(qty_production)
Total Good        = SUM(qty_good)
Total Not Good    = SUM(qty_not_good)
Sisa Belum Prod   = qty_order - SUM(qty_production)
Sisa Belum QC     = SUM(qty_production) - SUM(qty_good + qty_not_good)
```

Semua dihitung on-the-fly via Eloquent `withSum()` — tidak ada kolom turunan di database.

## WO Status Badge

| Kondisi | Label | Warna |
|---------|-------|-------|
| Belum ada produksi | `Belum Produksi` | Gray |
| Produksi sedang berjalan | `In Progress` | Orange |
| Produksi selesai, QC belum semua | `Prod. Selesai` | Blue |
| Semua QC selesai | `Fully QC'd` | Green |

Status di-determine di Blade secara otomatis berdasarkan `sisaProduksi` dan `sisaQc`. Tidak perlu input manual.

## Role & Akses

| Role | Buat WO | Edit WO | Input Produksi | Input QC | Dashboard |
|------|:-------:|:-------:|:--------------:|:--------:|:---------:|
| PPIC | ✅ | ✅ | | | |
| Operator | | | ✅ | | |
| QC | | | | ✅ | |
| Manager | | | | | ✅ |

## Instalasi

```bash
# 1. Clone & install
composer install
cp .env.example .env
php artisan key:generate

# 2. Buat database MySQL
mysql -u root -p -e "CREATE DATABASE wo_qc"

# 3. Migration + seeder
php artisan migrate
php artisan db:seed

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
| `ppic@example.com` | `password` | ppic |
| `operator@example.com` | `password` | operator |
| `qc@example.com` | `password` | qc |
| `manager@example.com` | `password` | manager |

## Route

```
GET   /dashboard           Dasbor monitoring (search & filter via query string)
GET   /dashboard?search=   Filter by wo_number or product
GET   /dashboard?status=   Filter by: in_progress | prod_complete | fully_qc
GET   /work-orders        Daftar WO
GET   /work-orders/create Form buat WO
POST  /work-orders        Simpan WO baru
GET   /work-orders/{id}   Detail WO
GET   /work-orders/{id}/edit  Form edit WO
PUT   /work-orders/{id}  Update WO
DELETE /work-orders/{id}  Hapus WO
GET   /productions         Input & log produksi
POST  /productions         Simpan produksi
GET   /quality-controls    Input & log QC
POST  /quality-controls    Simpan QC
```

## Desain Sistem

**Single Source of Truth** — semua metric dihitung dari tabel sumber (`work_orders`, `productions`, `quality_controls`) via Eloquent `withSum()`. Tidak ada kolom turunan yang disimpan, sehingga tidak mungkin terjadi drift antara state terhitung dan data aktual.

**Over-limit Guard di Controller Layer** — validasi over-production dan over-QC diletakkan di Controller (`ProductionController::store`, `QualityControlController::store`) menggunakan query `withSum()` sebelum insert. Ini lebih reliable daripada FormRequest closure yang tidak konsisten di CLI context.

**Foreign Key ON DELETE CASCADE** — saat Work Order dihapus, semua record produksi dan QC terkait ikut terhapus otomatis. Ini menjamin konsistensi data tanpa perlu cleanup manual.

**Database Engine InnoDB** — semua tabel menggunakan InnoDB agar foreign key constraint berjalan penuh dan transaction support tersedia.
