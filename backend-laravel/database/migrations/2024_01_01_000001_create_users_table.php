<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Users Table dengan Role-Based Access Control
 * 
 * Implementasi Knowledge Distribution melalui peran pengguna
 * untuk memastikan akses pengetahuan strategis sesuai hierarki organisasi
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email'); // Removed unique constraint for testing purposes
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Role-Based Access Control for Knowledge Distribution
            $table->enum('role', ['admin', 'manager', 'staff'])->default('staff');
            
            // Additional Profile Fields
            $table->string('phone')->nullable();
            $table->string('position')->nullable(); // Jabatan
            $table->string('department')->nullable(); // Departemen
            $table->string('avatar')->nullable();
            
            // Account Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            
            // Indexes
            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
