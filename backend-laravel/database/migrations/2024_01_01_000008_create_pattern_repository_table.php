<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Pattern Repository - Aset Pengetahuan Digital
     * Menyimpan pola jahit sebagai organizational knowledge asset
     */
    public function up(): void
    {
        Schema::create('pattern_repository', function (Blueprint $table) {
            $table->id();
            
            // Pattern Info
            $table->string('pattern_code')->unique(); // Kode pola: PTN-001
            $table->string('pattern_name'); // Nama pola: Kemeja Formal Slim Fit
            $table->enum('product_type', [
                'Kaos',
                'Kemeja',
                'Celana',
                'Jaket',
                'Dress',
                'Seragam',
                'Other'
            ]);
            
            // Pattern Files
            $table->string('file_path')->nullable(); // Path ke file PDF/DXF
            $table->string('thumbnail_path')->nullable();
            $table->json('size_variants')->nullable(); // ["S", "M", "L", "XL", "XXL"]
            
            // Material Requirements
            $table->json('material_requirements'); 
            // Example: [{"material_id": 1, "quantity": 1.5, "unit": "meter"}]
            
            // Efficiency Metrics (Lean KM)
            $table->decimal('fabric_efficiency', 5, 2)->nullable(); // 85.5% (material utilization)
            $table->decimal('avg_cutting_time', 8, 2)->nullable(); // Minutes
            $table->decimal('avg_sewing_time', 8, 2)->nullable(); // Minutes
            
            // Knowledge Documentation
            $table->text('cutting_instructions')->nullable();
            $table->text('sewing_instructions')->nullable();
            $table->text('quality_checkpoints')->nullable();
            $table->text('common_mistakes')->nullable(); // Tacit knowledge
            
            // Version Control
            $table->string('version', 10)->default('1.0');
            $table->foreignId('parent_pattern_id')->nullable()->constrained('pattern_repository')->onDelete('set null');
            
            // Usage Stats
            $table->integer('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            
            // Status
            $table->enum('status', ['Draft', 'Active', 'Archived'])->default('Active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('product_type');
            $table->index('status');
            $table->index(['fabric_efficiency', 'status']); // Find most efficient patterns
            $table->fullText(['pattern_name', 'cutting_instructions', 'sewing_instructions']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pattern_repository');
    }
};
