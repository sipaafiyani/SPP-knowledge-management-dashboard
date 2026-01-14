<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel ini menyimpan data supplier/vendor kain dan aksesoris
     * dengan scoring berdasarkan Knowledge-Based View (KBV)
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('specialty')->nullable(); // Spesialisasi: Kain Katun, Benang, dll
            $table->text('contact_info')->nullable();
            $table->string('address')->nullable();
            $table->string('phone', 20)->nullable();
            
            // KBV Metrics (Knowledge-Based View)
            $table->decimal('quality_score', 3, 1)->default(0); // 0-10
            $table->decimal('speed_score', 3, 1)->default(0); // Delivery speed 0-10
            $table->decimal('reliability_score', 3, 1)->default(0); // On-time delivery 0-10
            
            $table->boolean('is_recommended')->default(false);
            $table->text('kbv_insight')->nullable(); // Tacit knowledge tentang supplier
            
            // Tracking
            $table->date('last_delivery_date')->nullable();
            $table->integer('total_orders')->default(0);
            $table->integer('on_time_deliveries')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('is_recommended');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
