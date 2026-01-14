<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Vendor Deliveries - Tracking untuk KBV Scoring
     */
    public function up(): void
    {
        Schema::create('vendor_deliveries', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            
            // Order Info
            $table->string('po_number')->unique();
            $table->date('order_date');
            $table->date('expected_delivery_date');
            $table->date('actual_delivery_date')->nullable();
            
            // Quantity & Price
            $table->decimal('quantity_ordered', 10, 2);
            $table->decimal('quantity_delivered', 10, 2)->nullable();
            $table->string('unit', 20);
            $table->decimal('price_per_unit', 12, 2);
            $table->decimal('total_price', 15, 2);
            
            // Quality Assessment (KBV)
            $table->enum('color_consistency', [
                'Excellent',  // Warna 100% match
                'Good',       // Minor difference
                'Fair',       // Noticeable difference
                'Poor'        // Tidak sesuai
            ])->nullable();
            
            $table->enum('material_quality', [
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ])->nullable();
            
            $table->boolean('on_time_delivery')->nullable();
            $table->integer('delay_days')->default(0);
            
            // Knowledge Capture
            $table->text('quality_notes')->nullable(); // Tacit insight
            $table->text('delivery_notes')->nullable();
            $table->decimal('defect_rate', 5, 2)->nullable(); // Percentage
            
            // Status
            $table->enum('status', [
                'Ordered',
                'In_Transit',
                'Delivered',
                'Inspected',
                'Accepted',
                'Rejected'
            ])->default('Ordered');
            
            $table->foreignId('received_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('inspected_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            // Indexes
            $table->index('order_date');
            $table->index('status');
            $table->index(['supplier_id', 'on_time_delivery']);
            $table->index(['material_id', 'order_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_deliveries');
    }
};
