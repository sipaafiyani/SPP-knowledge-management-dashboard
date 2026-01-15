# Setup & Installation Guide
# Strategic KM Inventory Dashboard - Backend Laravel

## Prerequisites
- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Laravel 10.x

## Quick Start

### 1. Install Laravel (jika belum ada artisan)
```bash
cd /Users/sipa/TUBESSPP/SPP-knowledge-management-dashboard
composer create-project laravel/laravel backend-laravel "10.*"
```

Atau copy struktur file yang sudah ada ke Laravel project baru.

### 2. Database Configuration
Edit `.env` di folder `backend-laravel`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spp_km_inventory
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Run Migrations
```bash
cd backend-laravel
php artisan migrate
```

### 4. Seed Data (Optional)
```bash
php artisan db:seed
```

### 5. Start Laravel Server
```bash
php artisan serve --port=8000
```

API akan berjalan di: `http://localhost:8000`

## API Endpoints

### Inventory Management
- `GET /api/inventaris` - List all inventory items
- `POST /api/inventaris` - Create new inventory item
- `GET /api/inventaris/{id}` - Get single item
- `PUT /api/inventaris/{id}` - Update item
- `DELETE /api/inventaris/{id}` - Delete item

### Test API
```bash
# Health check
curl http://localhost:8000/api/health

# Get inventory list
curl http://localhost:8000/api/inventaris

# Create new item
curl -X POST http://localhost:8000/api/inventaris \
  -H "Content-Type: application/json" \
  -d '{
    "nama_bahan": "Kain Katun Premium",
    "kategori": "Bahan Utama",
    "stok": 100,
    "satuan": "meter",
    "harga_per_unit": 50000,
    "threshold_min": 20
  }'
```

## Frontend Integration

Frontend Next.js sudah dikonfigurasi untuk mengakses API di `http://localhost:8000/api`

Pastikan:
1. ✅ Laravel server berjalan di port 8000
2. ✅ Next.js dev server berjalan di port 3000
3. ✅ CORS diaktifkan di Laravel

## CORS Configuration

Tambahkan di `config/cors.php`:

```php
'paths' => ['api/*'],
'allowed_origins' => ['http://localhost:3000'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

## Troubleshooting

### "Module not found: axios"
```bash
cd /Users/sipa/TUBESSPP/SPP-knowledge-management-dashboard
npm install axios
```

### "Connection refused to localhost:8000"
Pastikan Laravel server berjalan:
```bash
cd backend-laravel
php artisan serve --port=8000
```

### "SQLSTATE[HY000] [1049] Unknown database"
Buat database terlebih dahulu:
```sql
CREATE DATABASE spp_km_inventory;
```

## File Structure

```
backend-laravel/
├── app/
│   ├── Http/Controllers/API/
│   │   └── InventoryController.php  ✅ Created
│   └── Models/
│       └── Material.php              ✅ Exists
├── database/
│   └── migrations/
│       └── 2024_01_01_000002_create_materials_table.php  ✅ Exists
└── routes/
    └── api.php                       ✅ Created
```

## Next Steps

1. ⬜ Install Laravel lengkap (composer, artisan)
2. ⬜ Setup database & run migrations
3. ⬜ Configure CORS
4. ⬜ Start Laravel server
5. ⬜ Test API endpoints
6. ⬜ Verify frontend integration
