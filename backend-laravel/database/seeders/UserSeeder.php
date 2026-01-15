<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * User Seeder - Demo Accounts untuk Testing
 * 
 * Membuat akun demo untuk setiap role:
 * - Admin: Akses penuh ke semua knowledge
 * - Manager: Akses ke strategic insights
 * - Staff: Akses ke operational data
 */
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account - Kredensial Testing: admin/admin
        User::create([
            'name' => 'Admin derras',
            'email' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'position' => 'System Administrator',
            'department' => 'IT',
            'phone' => '081234567890',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Manager Account - Kredensial Testing: admin/admin
        User::create([
            'name' => 'Manager Produksi',
            'email' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'manager',
            'position' => 'Production Manager',
            'department' => 'Produksi',
            'phone' => '081234567891',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Staff Account - Kredensial Testing: admin/admin
        User::create([
            'name' => 'Staff Gudang',
            'email' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'staff',
            'position' => 'Warehouse Staff',
            'department' => 'Gudang',
            'phone' => '081234567892',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Additional Staff - Kredensial Testing: admin/admin
        User::create([
            'name' => 'Budi Santoso',
            'email' => 'admin',
            'password' => Hash::make('admin'),
            'role' => 'staff',
            'position' => 'Inventory Staff',
            'department' => 'Gudang',
            'phone' => '081234567893',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
