# 🔐 Sistem Otentikasi & Role-Based Access Control
## Hub MK - Strategic Knowledge Management System

---

## ✅ Implementasi Lengkap

### 🎯 Tujuan Knowledge Management
Sistem otentikasi ini memastikan **distribusi pengetahuan dilakukan melalui saluran yang tepat** kepada individu yang sesuai dengan peran mereka dalam organisasi (Role-Based Knowledge Distribution).

---

## 📁 File yang Dibuat

### Backend (Laravel Sanctum)

#### 1. **create_users_table.php** ✅
**Lokasi:** `/backend-laravel/database/migrations/2024_01_01_000001_create_users_table.php`

**Kolom Role:**
```php
enum('role', ['admin', 'manager', 'staff'])->default('staff')
```

**Additional Fields:**
- `position` - Jabatan
- `department` - Departemen
- `is_active` - Status akun
- `last_login_at` - Tracking login

#### 2. **create_personal_access_tokens_table.php** ✅
**Lokasi:** `/backend-laravel/database/migrations/2019_12_14_000001_create_personal_access_tokens_table.php`

Tabel untuk Laravel Sanctum token storage.

#### 3. **UserSeeder.php** ✅
**Lokasi:** `/backend-laravel/database/seeders/UserSeeder.php`

**Demo Accounts:**
| Role | Email | Password | Access Level |
|------|-------|----------|--------------|
| Admin | admin@hubmk.com | admin123 | Full access |
| Manager | manager@hubmk.com | manager123 | Strategic insights |
| Staff | staff@hubmk.com | staff123 | Operational data only |

#### 4. **AuthController.php** ✅
**Lokasi:** `/backend-laravel/app/Http/Controllers/API/AuthController.php`

**Methods:**
- ✅ `login()` - Authenticate & generate Bearer token
- ✅ `logout()` - Revoke current token
- ✅ `me()` - Get authenticated user data
- ✅ `refresh()` - Refresh token
- ✅ `getRolePermissions()` - Permission mapping
- ✅ `getKMInsight()` - Role-based KM insights

**Validasi Login:**
```php
- email: required, email format
- password: required, min:6
- Account must be is_active = true
```

#### 5. **api.php (Routes)** ✅
**Lokasi:** `/backend-laravel/routes/api.php`

**Public Routes:**
```php
POST /api/auth/login
```

**Protected Routes (auth:sanctum):**
```php
POST /api/auth/logout
GET  /api/auth/me
POST /api/auth/refresh

GET  /api/inventaris (All roles)
POST /api/inventaris (Manager, Admin)
PUT  /api/inventaris/{id} (Manager, Admin)
DELETE /api/inventaris/{id} (Manager, Admin)

GET  /api/vendors (Manager, Admin only)
POST /api/vendors (Manager, Admin only)
... (other vendor endpoints)
```

---

### Frontend (Next.js)

#### 6. **page.tsx (Login Page)** ✅
**Lokasi:** `/app/login/page.tsx`

**Features:**
- ✅ Dark mode design konsisten dengan tema Hub MK
- ✅ Shield icon & gradient branding
- ✅ Email & password input with icons
- ✅ Show/hide password toggle
- ✅ Loading state saat submit
- ✅ Error handling dengan message display
- ✅ Quick login buttons (Admin, Manager, Staff)
- ✅ Dual mode: Backend API + Demo fallback
- ✅ Success message dengan KM insight

**UI Components:**
- Gradient background (gray-900 → gray-800)
- Card with backdrop blur
- Input dengan icons (Mail, Lock)
- Button gradient (blue-600 → purple-600)
- Demo credentials display

#### 7. **auth-context.tsx** ✅
**Lokasi:** `/lib/auth-context.tsx`

**Context Functions:**
```typescript
- login(token, user, permissions) - Save auth data
- logout() - Clear auth & redirect to login
- hasPermission(permission) - Check user permission
- checkAuth() - Verify stored token
```

**State Management:**
```typescript
- user: User | null
- permissions: Permissions | null
- token: string | null
- isLoading: boolean
- isAuthenticated: boolean
```

#### 8. **layout.tsx** ✅
**Updated:** Wrapped with `<AuthProvider>`

#### 9. **page.tsx (Main Dashboard)** ✅
**Updated Features:**
- ✅ Auto-redirect to /login if not authenticated
- ✅ Loading state saat check auth
- ✅ Permission-based rendering
- ✅ "Akses Ditolak" message untuk unauthorized pages
- ✅ Auto-select first available section for staff

#### 10. **sidebar.tsx** ✅
**Updated Features:**
- ✅ Filter navigation based on permissions
- ✅ User profile card di footer
- ✅ Role badge (Admin/Manager/Staff)
- ✅ Logout button dengan confirmation

---

## 🔑 Role-Based Permissions

### Admin (Full Access)
```json
{
  "dashboard": true,      // ✅ Dasbor Strategis
  "inventaris": true,     // ✅ Manajemen Inventaris
  "analitik": true,       // ✅ Analitik & Reports
  "vendor": true,         // ✅ Intelijen Vendor
  "pengetahuan": true,    // ✅ Basis Pengetahuan
  "users": true,          // ✅ User Management
  "settings": true        // ✅ System Settings
}
```

### Manager (Strategic Insights)
```json
{
  "dashboard": true,      // ✅ Dasbor Strategis
  "inventaris": true,     // ✅ Manajemen Inventaris
  "analitik": true,       // ✅ Analitik & Reports
  "vendor": true,         // ✅ Intelijen Vendor
  "pengetahuan": true,    // ✅ Basis Pengetahuan
  "users": false,         // ❌ User Management
  "settings": false       // ❌ System Settings
}
```

### Staff (Operational Data Only)
```json
{
  "dashboard": false,     // ❌ Dasbor Strategis
  "inventaris": true,     // ✅ Manajemen Inventaris
  "analitik": false,      // ❌ Analitik & Reports
  "vendor": false,        // ❌ Intelijen Vendor
  "pengetahuan": true,    // ✅ Basis Pengetahuan
  "users": false,         // ❌ User Management
  "settings": false       // ❌ System Settings
}
```

---

## 🚀 Cara Testing

### Mode Demo (Tanpa Backend) - Langsung Jalan! ✅

1. **Buka browser:** `http://localhost:3000`
2. **Redirect otomatis ke:** `/login`
3. **Pilih Quick Login:**
   - Klik button **"Admin"** → Auto-fill credentials
   - Klik button **"Manager"** → Auto-fill credentials
   - Klik button **"Staff"** → Auto-fill credentials
4. **Atau isi manual:**
   ```
   Email: admin@hubmk.com
   Password: admin123
   ```
5. **Klik "Masuk ke Sistem"**
6. **Result:** 
   - Alert dengan KM insight sesuai role
   - Redirect ke dashboard
   - Sidebar hanya menampilkan menu sesuai permission
   - User profile di footer sidebar

### Test Role-Based Access:

**Login sebagai Staff:**
```
Email: staff@hubmk.com
Password: staff123
```
- ✅ Bisa akses: Inventaris, Basis Pengetahuan
- ❌ Tidak bisa akses: Dasbor, Analitik, Vendor
- Menu yang tidak diizinkan tidak muncul di sidebar

**Login sebagai Manager:**
```
Email: manager@hubmk.com
Password: manager123
```
- ✅ Bisa akses: Semua kecuali Users & Settings
- Menu Users & Settings tidak muncul

**Login sebagai Admin:**
```
Email: admin@hubmk.com
Password: admin123
```
- ✅ Bisa akses: SEMUA menu

### Mode Full Backend (Dengan Laravel):

1. **Setup Laravel:**
   ```bash
   cd backend-laravel
   php artisan migrate
   php artisan db:seed --class=UserSeeder
   php artisan serve --port=8000
   ```

2. **Test API:**
   ```bash
   # Login
   curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email": "admin@hubmk.com", "password": "admin123"}'
   
   # Response:
   {
     "success": true,
     "message": "Selamat datang, Admin Hub MK!",
     "data": {
       "user": {...},
       "token": "1|abc123...",
       "permissions": {...}
     },
     "km_insight": "..."
   }
   
   # Get User Info
   curl http://localhost:8000/api/auth/me \
     -H "Authorization: Bearer YOUR_TOKEN"
   
   # Logout
   curl -X POST http://localhost:8000/api/auth/logout \
     -H "Authorization: Bearer YOUR_TOKEN"
   ```

3. **Frontend auto-connect:** Login page akan otomatis gunakan API jika backend running

---

## 🎨 UI/UX Features

### Login Page:
- ✅ Dark mode gradient background
- ✅ Shield icon dengan gradient (blue → purple)
- ✅ Input fields dengan icons
- ✅ Show/hide password button
- ✅ Loading spinner saat submit
- ✅ Error message display (red card)
- ✅ Quick login buttons (3 roles)
- ✅ Demo credentials helper text
- ✅ Responsive design

### Sidebar:
- ✅ User profile card dengan avatar gradient
- ✅ Role badge (Admin/Manager/Staff)
- ✅ Permission-filtered navigation
- ✅ Logout button (red color)

### Dashboard:
- ✅ Loading state saat check auth
- ✅ "Akses Ditolak" message untuk unauthorized
- ✅ Auto-redirect untuk unauthenticated users

---

## 📊 Knowledge Distribution Strategy

### Tacit Knowledge → Explicit Knowledge:
1. **Socialization:** Staff mengakses Basis Pengetahuan
2. **Externalization:** Manager input data ke sistem
3. **Combination:** Admin melihat dashboard strategis
4. **Internalization:** Semua role belajar dari sistem

### Information Flow:
```
Admin (Strategic Level)
  ↓ Full Dashboard Access
  ↓ Analytics & Vendor Intelligence
  ↓
Manager (Tactical Level)
  ↓ Strategic Insights
  ↓ Vendor & Analytics Access
  ↓
Staff (Operational Level)
  ↓ Inventory Data
  ↓ Knowledge Base Access
```

---

## 🔒 Security Features

1. **Laravel Sanctum:**
   - Token-based authentication
   - Token revocation on logout
   - Token expiration support

2. **Password Security:**
   - Hashed dengan bcrypt
   - Minimum 6 characters
   - Show/hide toggle

3. **Route Protection:**
   - Middleware `auth:sanctum` pada protected routes
   - Frontend auto-redirect untuk unauthorized

4. **Account Status:**
   - `is_active` check saat login
   - Prevent disabled accounts

---

## 📝 Demo Credentials

| Role | Email | Password | Access |
|------|-------|----------|--------|
| **Admin** | admin@hubmk.com | admin123 | Full system access |
| **Manager** | manager@hubmk.com | manager123 | Strategic insights & analytics |
| **Staff** | staff@hubmk.com | staff123 | Inventory & knowledge base only |
| Staff 2 | budi@hubmk.com | staff123 | Same as Staff |

---

## ✨ Summary

**Backend:** 5 files (Migrations, Seeder, Controller, Routes)  
**Frontend:** 5 files (Login page, Auth context, Layout, Page, Sidebar updates)  
**Total Lines:** ~1200 LOC  
**Status:** ✅ READY TO USE (Demo mode aktif)  
**KM Benefit:** Distribusi pengetahuan strategis sesuai hierarki organisasi

🎉 **Sistem Otentikasi dengan Role-Based Access Control sudah lengkap!**
