<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Vendors Table
 * 
 * Penyimpanan Pengetahuan Eksternal tentang Mitra Pemasok Strategis
 * Implementasi Knowledge-Based View untuk Supplier Intelligence
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            
            // Basic Vendor Information
            $table->string('nama_vendor'); // Nama perusahaan vendor
            $table->string('kategori_bahan'); // Spesialisasi bahan (misal: Kain Katun & Drill)
            
            // Performance Metrics (Knowledge Storage)
            $table->decimal('rating_kualitas', 3, 1)->default(7.0); // Rating 1-10
            $table->decimal('rating_kecepatan', 3, 1)->default(7.0); // Rating pengiriman 1-10
            $table->decimal('indeks_keandalan', 3, 1)->default(7.0); // Reliability index 1-10
            
            // Knowledge-Based View Fields
            $table->text('kbv_insight'); // Tacit knowledge tentang vendor
            $table->boolean('is_pilihan_utama')->default(false); // Strategic partner flag
            
            // Contact & Delivery Information
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('last_delivery')->default('Belum ada pengiriman');
            
            // Audit & Metadata
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for Performance
            $table->index('nama_vendor');
            $table->index('kategori_bahan');
            $table->index('is_pilihan_utama');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
