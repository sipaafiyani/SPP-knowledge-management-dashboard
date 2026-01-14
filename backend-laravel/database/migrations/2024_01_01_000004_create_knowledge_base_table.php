<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Knowledge Base - Pusat Pembelajaran (SECI Model)
     * SOP, Tutorial, Video, Best Practices
     */
    public function up(): void
    {
        Schema::create('knowledge_base', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            // Content Type
            $table->enum('type', [
                'SOP',              // Standard Operating Procedure
                'Tutorial',         // Panduan langkah demi langkah
                'Best_Practice',    // Praktik terbaik
                'Video',           // Video tutorial
                'Checklist',       // Daftar periksa
                'Template'         // Template dokumen
            ]);
            
            // Content
            $table->longText('content')->nullable(); // Rich text content
            $table->string('file_path')->nullable(); // Path untuk PDF/Video
            $table->string('file_type', 50)->nullable(); // pdf, mp4, docx
            $table->integer('file_size')->nullable(); // In KB
            
            // Categorization
            $table->string('category')->nullable(); // Jahit, Cutting, QC, dll
            $table->json('tags')->nullable(); // ["obras", "kaos", "finishing"]
            
            // SECI Mapping
            $table->enum('seci_stage', [
                'Sosialisasi',
                'Eksternalisasi',
                'Kombinasi',
                'Internalisasi'
            ])->nullable();
            
            // Metrics
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->decimal('avg_rating', 3, 2)->nullable(); // 0-5 stars
            
            // Author & Status
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
            $table->boolean('is_featured')->default(false);
            
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('category');
            $table->index('status');
            $table->index(['is_featured', 'view_count']); // Featured & popular content
            $table->fullText(['title', 'description', 'content']); // Search optimization
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base');
    }
};
