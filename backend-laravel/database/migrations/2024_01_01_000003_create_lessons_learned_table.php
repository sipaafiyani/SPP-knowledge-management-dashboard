<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Lessons Learned - SECI Model Implementation
     * Konversi Tacit to Explicit Knowledge
     */
    public function up(): void
    {
        Schema::create('lessons_learned', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            
            // SECI Model Categories
            $table->enum('seci_type', [
                'Sosialisasi',      // Tacit to Tacit (sharing pengalaman)
                'Eksternalisasi',   // Tacit to Explicit (dokumentasi)
                'Kombinasi',        // Explicit to Explicit (analisis data)
                'Internalisasi'     // Explicit to Tacit (learning by doing)
            ]);
            
            $table->string('category')->nullable(); // Tacit to Explicit, KBV, Lean KM, dll
            
            // Content
            $table->text('problem_description');
            $table->text('solution');
            $table->text('recommendation')->nullable();
            
            // Impact Assessment
            $table->enum('impact_level', ['Tinggi', 'Sedang', 'Rendah'])->default('Sedang');
            $table->decimal('estimated_savings', 12, 2)->nullable(); // Perkiraan penghematan (Rp)
            
            // Author & Validation
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Published');
            
            // Engagement metrics
            $table->integer('view_count')->default(0);
            $table->integer('likes_count')->default(0);
            
            // Related entities
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('seci_type');
            $table->index('impact_level');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons_learned');
    }
};
