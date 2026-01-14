<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel materials untuk bahan baku konveksi
     * Menyimpan Explicit & Tacit Knowledge
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama bahan: Kain Katun Combed 30s
            $table->enum('category', [
                'Bahan Utama',
                'Bahan Pendukung',
                'Aksesoris',
                'Alat Produksi'
            ]);
            
            // Stock Management
            $table->decimal('stock_quantity', 10, 2)->default(0);
            $table->string('unit', 20); // meter, cone, gross, pcs
            $table->decimal('threshold_min', 10, 2); // Minimum stok untuk alert
            $table->decimal('price_per_unit', 12, 2)->nullable();
            
            // Knowledge Management (SECI Model)
            $table->text('explicit_knowledge')->nullable(); // Dokumentasi formal
            $table->text('tacit_knowledge')->nullable(); // Pengalaman praktis dari penjahit
            
            // Supplier relationship
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            
            // Lean KM - Tracking
            $table->decimal('avg_waste_percentage', 5, 2)->default(0); // Rata-rata waste
            $table->integer('reorder_point')->nullable(); // Kapan harus pesan ulang
            
            // Audit trail
            $table->foreignId('last_updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_restocked_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('category');
            $table->index('stock_quantity');
            $table->index(['stock_quantity', 'threshold_min']); // Alert stok rendah
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
