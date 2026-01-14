# 📊 DOKUMENTASI LENGKAP: Alur Data & Implementasi KM

## 🎯 Overview Sistem

Sistem ini adalah **Dashboard Inventaris Strategis berbasis Knowledge Management** untuk UMKM Konveksi yang menerapkan:
- **SECI Model** (Nonaka & Takeuchi) - Konversi Tacit to Explicit Knowledge
- **Knowledge-Based View (KBV)** - Supplier & pola sebagai aset strategis
- **Lean Knowledge Management** - Tracking efisiensi & minimalisasi waste

---

## 🔄 ALUR DATA LENGKAP: Staf → Pemilik UMKM

### **FASE 1: INPUT HARIAN (Staf Produksi)**

#### 1.1 Input Pagi Hari (Sebelum Produksi)
```
Staf membuka aplikasi mobile/web → Login
└─ Lihat Stok Bahan Hari Ini
   ├─ Kain Katun: 450 meter (Status: Optimal ✓)
   ├─ Benang Polyester: 890 cone (Status: Optimal ✓)
   └─ Kancing: 45 gross (Status: Rendah ⚠️)
```

**Database**: Query ke tabel `materials`
```sql
SELECT name, stock_quantity, unit, threshold_min, 
       CASE 
         WHEN stock_quantity <= 0 THEN 'Habis'
         WHEN stock_quantity <= threshold_min THEN 'Stok Rendah'
         ELSE 'Optimal'
       END as status
FROM materials 
WHERE is_active = true;
```

#### 1.2 Selama Produksi (Input Real-time)
```
Penjahit menemukan masalah:
"Kain katun dari Supplier A menyusut 5% setelah dicuci!"

Action:
├─ Buka Form "Catat Pengetahuan Baru"
├─ Isi:
│   ├─ Judul: "Kain katun menyusut 5% setelah pencucian"
│   ├─ Kategori SECI: Eksternalisasi (Tacit → Explicit)
│   ├─ Masalah: "Baju jadi lebih kecil dari ukuran seharusnya"
│   ├─ Solusi: "Tambah toleransi 7-8cm saat potong pola"
│   └─ Dampak: Tinggi
└─ Submit
```

**Database**: Insert ke tabel `lessons_learned`
```php
{
  "title": "Kain katun menyusut 5% setelah pencucian pertama",
  "seci_type": "Eksternalisasi",
  "problem_description": "Baju jadi lebih kecil...",
  "solution": "Tambah toleransi 7-8cm...",
  "impact_level": "Tinggi",
  "author_id": 5, // ID penjahit
  "status": "Published"
}
```

#### 1.3 Akhir Shift (Log Produksi)
```
Staf QC input laporan harian:
├─ Bahan digunakan: Kain Katun 25 meter
├─ Output: 15 kaos ukuran M
├─ Sisa/Waste: 3.5 meter (14%)
├─ Catatan: "Banyak sisa karena pola tidak optimal"
└─ Submit
```

**Database**: Insert ke `production_logs`
```php
{
  "production_date": "2026-01-14",
  "material_id": 1,
  "quantity_used": 25.00,
  "unit": "meter",
  "quantity_produced": 15,
  "waste_quantity": 3.50,
  "waste_percentage": 14.00, // Auto calculated
  "worker_id": 5
}
```

---

### **FASE 2: TRANSFORMASI PENGETAHUAN (Backend Laravel)**

#### 2.1 SECI Model Processing
Laravel backend secara otomatis mengkategorisasi knowledge:

```php
// LessonLearned Model
public function getSeciDescriptionAttribute(): string
{
    return match($this->seci_type) {
        'Sosialisasi' => 'Sharing pengalaman antar penjahit',
        'Eksternalisasi' => 'Dokumentasi pengalaman → SOP',
        'Kombinasi' => 'Analisis data → Best practice',
        'Internalisasi' => 'SOP → Keahlian praktis'
    };
}
```

**Trigger Otomatis**:
- Jika lesson dengan `impact_level = "Tinggi"` & `seci_type = "Eksternalisasi"`
  → Auto-generate draft SOP di tabel `knowledge_base`

#### 2.2 KBV Analytics (Supplier Scoring)
Setiap kali ada delivery dari supplier, sistem update scoring:

```php
// Supplier Model
public function recordDelivery(bool $onTime = true): void
{
    $this->total_orders++;
    if ($onTime) $this->on_time_deliveries++;
    
    // Auto-calculate reliability
    $this->reliability_score = ($this->on_time_deliveries / $this->total_orders) * 10;
    
    // Auto-recommend if all metrics > 8.5
    $this->is_recommended = (
        $this->quality_score >= 8.5 &&
        $this->speed_score >= 8.5 &&
        $this->reliability_score >= 8.5
    );
    
    $this->save();
}
```

#### 2.3 Lean KM - Waste Calculation
Setiap malam jam 00:00, cron job hitung efisiensi:

```php
// Material Model
public function calculateWastePercentage(): float
{
    $logs = $this->productionLogs()
        ->where('production_date', '>=', now()->subMonths(3))
        ->get();
    
    $avgWaste = $logs->avg('waste_percentage');
    $this->avg_waste_percentage = $avgWaste;
    $this->save();
    
    // Alert jika waste > 15%
    if ($avgWaste > 15) {
        Notification::send(
            User::role('Owner'),
            new HighWasteAlert($this)
        );
    }
    
    return round($avgWaste, 2);
}
```

---

### **FASE 3: BUSINESS INTELLIGENCE ENGINE**

#### 3.1 Dashboard Metrics Calculation
Ketika pemilik buka dashboard, API endpoint `/api/dashboard/metrics`:

```php
// DashboardController@getMetrics
public function getMetrics()
{
    // 1. Total Nilai Stok
    $totalStockValue = Material::active()
        ->sum(DB::raw('stock_quantity * price_per_unit'));
    // Result: Rp 245.000.000
    
    // 2. Efisiensi Bahan
    $avgWaste = ProductionLog::recent(30)->avg('waste_percentage');
    $efficiency = 100 - $avgWaste; // 87%
    
    // 3. Indeks Supplier
    $supplierScore = Supplier::active()
        ->avg(DB::raw('(quality_score + speed_score + reliability_score) / 3'));
    // Result: 8.7/10
    
    // 4. Kesehatan Pengetahuan
    $materialsWithKnowledge = Material::whereNotNull('tacit_knowledge')->count();
    $healthScore = ($materialsWithKnowledge / Material::count()) * 50;
    $recentLessons = LessonLearned::recent(30)->count();
    $healthScore += min(($recentLessons / 10) * 50, 50);
    // Result: 76%
    
    return response()->json(...);
}
```

#### 3.2 Prediksi Musiman (Rule-based ML)
```php
// SeasonalPrediction Model
public static function predictDemand(int $month): array
{
    $rules = [
        6 => [ // Juni
            'demand_level' => 'Sangat Tinggi',
            'reason' => 'Seragam Sekolah',
            'multiplier' => 1.6, // +60%
        ],
        4 => [ // April (Lebaran)
            'demand_level' => 'Sangat Tinggi',
            'reason' => 'Lebaran',
            'multiplier' => 1.5,
        ],
        8 => [ // Agustus (17 Agustus)
            'demand_level' => 'Tinggi',
            'reason' => '17 Agustus',
            'multiplier' => 1.2,
        ],
    ];
    
    $prediction = $rules[$month] ?? [
        'demand_level' => 'Normal',
        'multiplier' => 1.0,
    ];
    
    // Hitung rekomendasi stok
    $avgUsage = ProductionLog::whereMonth('production_date', $month - 1)
        ->avg('quantity_used');
    
    $prediction['recommended_stock'] = $avgUsage * $prediction['multiplier'];
    
    return $prediction;
}
```

---

### **FASE 4: VISUALISASI STRATEGIS (Dashboard Pemilik)**

#### 4.1 Next.js Frontend Request Flow
```typescript
// components/strategic-overview.tsx
const fetchDashboardMetrics = async () => {
  const response = await fetch('/api/dashboard/metrics', {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json'
    }
  });
  
  const data = await response.json();
  // data.total_stock_value.value = "Rp 245.000.000"
  // data.material_efficiency.value = "87%"
  // ... dst
};
```

#### 4.2 Real-time Alerts
```typescript
// Alert jika stok rendah
useEffect(() => {
  fetch('/api/dashboard/alerts')
    .then(res => res.json())
    .then(alerts => {
      alerts.forEach(alert => {
        if (alert.type === 'warning') {
          toast.warning(alert.message);
        }
      });
    });
}, []);
```

---

## 📊 CONTOH KASUS LENGKAP

### Skenario: "Kain Katun Menyusut"

**Day 1 - Pagi (08:00)**
```
Bu Siti (Penjahit Senior) → Login ke sistem
└─ Ambil kain katun 30 meter untuk produksi 20 kaos
   Database: UPDATE materials SET stock_quantity = 420 WHERE id = 1
```

**Day 1 - Sore (15:00)**
```
Bu Siti menemukan masalah:
└─ Setelah dicuci, kaos menyusut 1 ukuran!

Action: Catat Lesson Learned
├─ Form Input (Frontend):
│   ├─ Judul: "Kain katun menyusut 5%..."
│   ├─ SECI: Eksternalisasi
│   ├─ Masalah: "..."
│   ├─ Solusi: "Tambah toleransi 7-8cm"
│   └─ Dampak: Tinggi
│
└─ API POST /api/lessons
    Database: INSERT INTO lessons_learned ...
```

**Day 1 - Malam (17:00)**
```
Tim QC → Input Production Log
├─ Kain digunakan: 30m
├─ Output: 20 kaos
├─ Waste: 2m (6.7%) ✓ Bagus!
└─ Database: INSERT INTO production_logs ...
```

**Day 1 - Malam (23:59)**
```
Cron Job Running:
├─ Calculate avg waste untuk Kain Katun
│   SELECT AVG(waste_percentage) FROM production_logs 
│   WHERE material_id = 1 AND production_date >= '2025-10-14'
│   Result: 8.2% (Lean target <10% ✓)
│
└─ Update material tacit knowledge:
    UPDATE materials 
    SET tacit_knowledge = 'Menyusut 5% setelah cuci. Tambah toleransi 7-8cm'
    WHERE id = 1
```

**Day 2 - Pagi (09:00)**
```
Pak Budi (Owner) → Buka Dashboard
├─ GET /api/dashboard/metrics
│   ├─ Total Stok: Rp 245jt (+12%)
│   ├─ Efisiensi: 87% (Waste 13%)
│   ├─ Supplier: 8.7/10
│   └─ Knowledge Health: 76%
│
└─ GET /api/dashboard/recent-knowledge
    ├─ "Kain katun menyusut 5%..." - 15 jam lalu (SECI: Eksternalisasi)
    ├─ "Supplier lokal sama kualitas" - 1 minggu lalu
    └─ "Teknik obras rapi" - 2 minggu lalu
```

**Day 2 - Siang (12:00)**
```
Ibu Ani (Penjahit Baru) → Login
└─ Lihat Knowledge Base → Cari "kain menyusut"
    └─ Temukan lesson dari Bu Siti
        └─ "Oh, harus tambah 7cm ya!"
        
INILAH SECI MODEL BEKERJA:
Tacit (Bu Siti) → Explicit (Sistem) → Tacit (Ibu Ani)
```

---

## 🔐 SECURITY & ROLES

### Role Permissions Matrix
```
┌───────────────┬───────┬──────────┬─────────┬───────┐
│ Feature       │ Owner │ Manager  │ Staff   │ Admin │
├───────────────┼───────┼──────────┼─────────┼───────┤
│ View Dashboard│   ✓   │    ✓     │    ✓    │   ✓   │
│ Analytics     │   ✓   │    ✓     │    ✗    │   ✓   │
│ Add Material  │   ✓   │    ✓     │    ✗    │   ✓   │
│ Update Stock  │   ✓   │    ✓     │    ✓    │   ✓   │
│ Add Lesson    │   ✓   │    ✓     │    ✓    │   ✓   │
│ Publish SOP   │   ✓   │    ✓     │    ✗    │   ✓   │
│ Manage Users  │   ✗   │    ✗     │    ✗    │   ✓   │
└───────────────┴───────┴──────────┴─────────┴───────┘
```

---

## 📱 MOBILE-FIRST CONSIDERATIONS

### Offline-First Strategy
```javascript
// Service Worker untuk offline capability
if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('/sw.js');
}

// IndexedDB untuk cache data
const db = await openDB('km-dashboard', 1, {
  upgrade(db) {
    db.createObjectStore('lessons', { keyPath: 'id' });
    db.createObjectStore('materials', { keyPath: 'id' });
  },
});

// Sync queue saat online
window.addEventListener('online', () => {
  syncQueue.processAll();
});
```

---

## 🚀 DEPLOYMENT

### Tech Stack
```
Frontend: Next.js 14 + TypeScript + TailwindCSS
Backend:  Laravel 10 + MySQL 8.0
Hosting:  
  - Frontend: Vercel
  - Backend:  Railway / Heroku
  - Database: PlanetScale / Railway
```

### Environment Variables
```env
# Frontend (.env.local)
NEXT_PUBLIC_API_URL=https://api.konveksi-km.com
NEXT_PUBLIC_APP_NAME="Dashboard KM Konveksi"

# Backend (.env)
APP_NAME="Konveksi KM API"
APP_URL=https://api.konveksi-km.com
DB_HOST=mysql.railway.app
DB_DATABASE=railway
```

---

## 📚 REFERENSI AKADEMIS

1. **Nonaka, I., & Takeuchi, H. (1995)**. *The Knowledge-Creating Company: How Japanese Companies Create the Dynamics of Innovation*. Oxford University Press.
   - SECI Model: Sosialisasi → Eksternalisasi → Kombinasi → Internalisasi

2. **Grant, R. M. (1996)**. *Toward a Knowledge-Based Theory of the Firm*. Strategic Management Journal, 17(S2), 109-122.
   - Knowledge sebagai sumber daya strategis
   - Supplier intelligence sebagai competitive advantage

3. **Womack, J. P., & Jones, D. T. (2003)**. *Lean Thinking: Banish Waste and Create Wealth in Your Corporation*. Free Press.
   - Waste minimization
   - Continuous improvement culture

---

**Dibuat dengan ❤️ untuk UMKM Konveksi Indonesia**
*Tugas Besar SPP - Sistem Pendukung Keputusan*
