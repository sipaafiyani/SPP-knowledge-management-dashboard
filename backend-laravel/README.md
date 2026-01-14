# Backend Laravel - Dashboard Inventaris Konveksi KM

## 🎯 Arsitektur Sistem Berbasis Knowledge Management

### Framework Teoritis:
1. **SECI Model (Nonaka & Takeuchi)**: Konversi Tacit to Explicit Knowledge
2. **Knowledge-Based View (KBV)**: Data vendor & pola sebagai aset strategis
3. **Lean Knowledge Management**: Tracking efisiensi bahan & minimalisasi waste

---

## 📊 Alur Data: Dari Staf Produksi → Pemilik UMKM

```
┌─────────────────────────────────────────────────────────────────┐
│                     LAYER 1: INPUT HARIAN                        │
│                  (Staf Produksi - Tacit Knowledge)               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
    ┌──────────────────────────────────────────────────┐
    │  1. Input Stok Bahan (meter/cone/gross)          │
    │  2. Catat Lessons Learned (form sederhana)       │
    │  3. Upload Tutorial Video (optional)             │
    │  4. Update Status Produksi                       │
    └──────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│              LAYER 2: KNOWLEDGE TRANSFORMATION                   │
│                  (Backend Laravel - SECI Model)                  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
    ┌──────────────────────────────────────────────────┐
    │  • Sosialisasi: Video tutorial disimpan          │
    │  • Eksternalisasi: Lessons → SOP Digital         │
    │  • Kombinasi: Agregasi data dari berbagai sumber│
    │  • Internalisasi: Best practice jadi workflow    │
    └──────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│             LAYER 3: KNOWLEDGE ANALYTICS ENGINE                  │
│                    (Business Intelligence)                       │
└─────────────────────────────────────────────────────────────────┘
                              ↓
    ┌──────────────────────────────────────────────────┐
    │  • Hitung Efisiensi Bahan (Lean KM)              │
    │  • Prediksi Stok Musiman (ML sederhana)          │
    │  • Skor Kesehatan Pengetahuan                    │
    │  • Indeks Keandalan Supplier (KBV)               │
    └──────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│           LAYER 4: DASHBOARD STRATEGIS (OUTPUT)                  │
│              (Next.js Frontend - Pemilik UMKM)                   │
└─────────────────────────────────────────────────────────────────┘
                              ↓
    ┌──────────────────────────────────────────────────┐
    │  ✓ Total Nilai Stok (Rp)                         │
    │  ✓ Efisiensi Bahan (87% - target >90%)           │
    │  ✓ Alert: Stok kain katun untuk lebaran          │
    │  ✓ Rekomendasi: Ganti supplier berdasarkan KBV   │
    └──────────────────────────────────────────────────┘
```

---

## 🗄️ Database Schema

### Tabel Utama:

#### 1. **materials** (Bahan Baku)
```sql
id, name, category, stock_quantity, unit, threshold_min, 
explicit_knowledge, tacit_knowledge, supplier_id, 
last_updated_by, created_at, updated_at
```

#### 2. **suppliers** (Vendor/Supplier)
```sql
id, name, specialty, quality_score, speed_score, reliability_score,
is_recommended, kbv_insight, contact_info, created_at, updated_at
```

#### 3. **lessons_learned** (SECI Model)
```sql
id, title, category, seci_type (enum: Sosialisasi, Eksternalisasi, Kombinasi, Internalisasi),
problem_description, solution, impact_level (enum: Tinggi, Sedang, Rendah),
author_id, status, created_at, updated_at
```

#### 4. **knowledge_base** (Pusat Pembelajaran)
```sql
id, title, type (enum: SOP, Tutorial, Best_Practice, Video),
content, file_url, category, view_count, 
created_by, created_at, updated_at
```

#### 5. **production_logs** (Log Produksi Harian)
```sql
id, date, material_used, quantity_used, waste_percentage,
product_type, notes, created_by, created_at
```

#### 6. **seasonal_predictions** (Prediksi Musiman)
```sql
id, month, year, predicted_demand, category, 
reason (enum: Lebaran, Seragam_Sekolah, Normal), 
confidence_score, created_at
```

---

## 🔌 API Endpoints

### Authentication
```
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/user
```

### Dashboard Metrics (KM Analytics)
```
GET    /api/dashboard/metrics
       → Total Nilai Stok, Efisiensi Bahan, Indeks Supplier, Skor Kesehatan Pengetahuan

GET    /api/dashboard/recent-knowledge
       → 5 pembaruan pengetahuan terbaru (SECI Model)

GET    /api/dashboard/alerts
       → Notifikasi stok rendah & rekomendasi berdasarkan KBV
```

### Materials Management
```
GET    /api/materials
GET    /api/materials/{id}
POST   /api/materials
PUT    /api/materials/{id}
DELETE /api/materials/{id}
GET    /api/materials/{id}/knowledge
       → Explicit + Tacit knowledge untuk material tertentu
```

### Suppliers (Knowledge-Based View)
```
GET    /api/suppliers
GET    /api/suppliers/{id}
POST   /api/suppliers/evaluate
       → Hitung score berdasarkan delivery history
GET    /api/suppliers/recommendations
       → Supplier terbaik berdasarkan KBV insights
```

### Lessons Learned (SECI Model)
```
GET    /api/lessons
POST   /api/lessons
PUT    /api/lessons/{id}
DELETE /api/lessons/{id}
GET    /api/lessons/by-seci/{type}
       → Filter berdasarkan Sosialisasi, Eksternalisasi, dll
```

### Knowledge Base (Pusat Pembelajaran)
```
GET    /api/knowledge-base
GET    /api/knowledge-base/{id}
POST   /api/knowledge-base
       → Upload SOP, tutorial, video
PUT    /api/knowledge-base/{id}
POST   /api/knowledge-base/{id}/view
       → Track view count untuk analytics
```

### Production & Analytics
```
GET    /api/production/efficiency
       → Hitung waste percentage & efisiensi bahan (Lean KM)

GET    /api/analytics/seasonal-prediction
       → Prediksi kebutuhan bahan untuk 3 bulan ke depan

GET    /api/analytics/stock-trends
       → Tren nilai stok 6 bulan terakhir

POST   /api/production/log
       → Input harian dari staf produksi
```

---

## 🚀 Instalasi & Setup

### Prerequisites
```bash
- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Laravel 10.x
```

### Install
```bash
cd backend-laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

### Seeder Data Demo
```bash
php artisan db:seed --class=ConvectionDemoSeeder
```
Akan generate:
- 20 jenis bahan (kain, benang, aksesoris)
- 5 supplier dengan KBV insights
- 15 lessons learned (SECI Model)
- 30 hari production logs
- Prediksi musiman untuk 6 bulan

---

## 📈 Fitur Knowledge Management

### 1. **Tacit to Explicit Conversion**
- Form input sederhana untuk staf capture "trik" penjahit senior
- Auto-kategorisasi berdasarkan keywords
- Notifikasi ke tim QC untuk review

### 2. **KBV Analytics**
- Scoring otomatis supplier berdasarkan 3 metrik:
  * Kualitas warna (consistency check)
  * Ketepatan waktu delivery
  * Reliability (order fulfillment rate)

### 3. **Lean KM - Waste Tracking**
- Input: meter kain digunakan vs output produk
- Algoritma: `waste_percentage = ((input - output) / input) * 100`
- Alert jika waste > 15%

### 4. **Seasonal Prediction**
- Rule-based ML:
  * Juni-Juli: Puncak seragam sekolah (+40%)
  * April-Mei: Lebaran (+60%)
  * Agustus: 17 Agustus (+20%)
- Rekomendasi stocking otomatis

---

## 🔐 Role & Permission

### Roles:
1. **Owner** (Pemilik UMKM)
   - Akses penuh dashboard strategis
   - View semua analytics & reports

2. **Production Manager**
   - Input production logs
   - Manage materials
   - Create lessons learned

3. **Staff** (Penjahit/Cutting)
   - Input daily logs
   - View knowledge base
   - Submit lessons learned

4. **Admin**
   - Manage users & suppliers
   - System configuration

---

## 📱 Mobile-First Design Consideration

Backend sudah optimize untuk:
- Pagination (max 20 items per request)
- Image compression untuk tutorial photos
- Offline-first capability (sync queue)
- Push notification untuk alerts

---

## 🧪 Testing

```bash
php artisan test
php artisan test --filter KnowledgeManagementTest
```

Coverage target:
- Unit Tests: 80%+
- Feature Tests: SECI Model, KBV Analytics, Lean Efficiency

---

## 📚 Referensi Akademis

1. Nonaka, I., & Takeuchi, H. (1995). *The Knowledge-Creating Company*
2. Grant, R. M. (1996). *Toward a Knowledge-Based Theory of the Firm*
3. Womack, J., & Jones, D. (2003). *Lean Thinking*

---

## 🤝 Contributing

Project ini untuk Tugas Besar SPP (Sistem Pendukung Keputusan)
Universitas: [Nama Universitas]
Kelompok: [Nama Kelompok]

---

**Built with ❤️ for UMKM Konveksi Indonesia**
