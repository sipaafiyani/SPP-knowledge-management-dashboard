<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Production Logs - Input Harian dari Staf Produksi
     * Untuk menghitung Efisiensi Bahan (Lean KM)
     */
    public function up(): void
    {
        Schema::create('production_logs', function (Blueprint $table) {
            $table->id();
            $table->date('production_date');
            $table->string('shift')->nullable(); // Pagi, Siang, Malam
            
            // Material Usage
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_used', 10, 2); // Jumlah bahan digunakan
            $table->string('unit', 20); // meter, cone, pcs
            
            // Output Product
            $table->string('product_type'); // Kaos, Kemeja, Celana, dll
            $table->integer('quantity_produced'); // Jumlah produk jadi
            $table->string('size_variant')->nullable(); // S, M, L, XL
            
            // Lean KM - Waste Tracking
            $table->decimal('waste_quantity', 10, 2)->default(0); // Sisa/waste
            $table->decimal('waste_percentage', 5, 2)->default(0); // Calculated
            $table->text('waste_reason')->nullable(); // Kenapa banyak sisa?
            
            // Quality Control
            $table->integer('defect_count')->default(0);
            $table->text('defect_notes')->nullable();
            
            // Worker Info
            $table->foreignId('worker_id')->constrained('users')->onDelete('cascade');
            $table->string('machine_id')->nullable(); // ID mesin jahit/obras
            
            // Knowledge Capture
            $table->text('notes')->nullable(); // Catatan penting hari ini
            $table->text('tacit_insight')->nullable(); // Tips/trik yang ditemukan
            
            $table->timestamps();
            
            // Indexes
            $table->index('production_date');
            $table->index('material_id');
            $table->index(['production_date', 'material_id']); // Analisis per material
            $table->index('waste_percentage'); // Track high waste
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_logs');
    }
};
