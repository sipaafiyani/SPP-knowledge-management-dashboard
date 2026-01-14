<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Seasonal Predictions - Prediksi Kebutuhan Musiman
     * Untuk stocking strategy (Lebaran, Seragam Sekolah, dll)
     */
    public function up(): void
    {
        Schema::create('seasonal_predictions', function (Blueprint $table) {
            $table->id();
            
            // Time Period
            $table->integer('year');
            $table->integer('month'); // 1-12
            $table->date('period_start');
            $table->date('period_end');
            
            // Category & Material
            $table->string('category'); // Seragam Sekolah, Lebaran, 17 Agustus
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            
            // Prediction
            $table->enum('demand_level', [
                'Sangat Tinggi', // >150% normal
                'Tinggi',        // 100-150%
                'Normal',        // 80-100%
                'Rendah'         // <80%
            ])->default('Normal');
            
            $table->decimal('predicted_quantity', 10, 2); // Prediksi jumlah kebutuhan
            $table->string('unit', 20);
            $table->decimal('confidence_score', 5, 2)->default(0); // 0-100%
            
            // Reasoning
            $table->text('reason')->nullable(); // Kenapa demand tinggi?
            $table->json('historical_data')->nullable(); // Data tahun lalu
            
            // Recommendations
            $table->text('stocking_recommendation')->nullable();
            $table->decimal('recommended_stock', 10, 2)->nullable();
            $table->date('optimal_order_date')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('alert_sent')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['year', 'month']);
            $table->index('demand_level');
            $table->index(['is_active', 'period_start']); // Upcoming predictions
            $table->unique(['year', 'month', 'material_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seasonal_predictions');
    }
};
