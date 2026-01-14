<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Production Wastes - Lean KM Implementation
     * Tracking waste untuk analisis efisiensi dan continuous improvement
     */
    public function up(): void
    {
        Schema::create('production_wastes', function (Blueprint $table) {
            $table->id();
            
            // Material & Production Info
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            $table->foreignId('production_log_id')->nullable()->constrained()->onDelete('set null');
            $table->date('waste_date');
            
            // Waste Details
            $table->decimal('waste_quantity', 10, 2); // Jumlah sisa/waste
            $table->string('unit', 20); // meter, cone, pcs
            $table->decimal('waste_value', 12, 2)->nullable(); // Nilai rupiah waste
            
            // Lean Analysis
            $table->enum('waste_category', [
                'Material Defect',      // Cacat bahan
                'Cutting Error',        // Kesalahan potong
                'Production Error',     // Kesalahan produksi
                'Planning Error',       // Kesalahan perencanaan
                'Other'
            ]);
            
            $table->text('waste_reason'); // Root cause analysis
            $table->text('preventive_action')->nullable(); // Cara mencegah
            
            // Process Improvement (Lean KM)
            $table->boolean('is_preventable')->default(true);
            $table->decimal('cost_impact', 12, 2)->nullable(); // Estimasi kerugian
            $table->text('lesson_learned')->nullable(); // Knowledge capture
            
            // Status & Follow-up
            $table->enum('status', ['Recorded', 'Analyzed', 'Action_Taken'])->default('Recorded');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('analyzed_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes for analytics
            $table->index('waste_date');
            $table->index('waste_category');
            $table->index(['material_id', 'waste_date']);
            $table->index('is_preventable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_wastes');
    }
};
