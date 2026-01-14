<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * LessonLearned Model - SECI Model Implementation
 * 
 * Menangkap dan mengkonversi Tacit Knowledge menjadi Explicit Knowledge
 * berdasarkan model SECI (Nonaka & Takeuchi, 1995)
 */
class LessonLearned extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'lessons_learned';

    protected $fillable = [
        'title',
        'seci_type',
        'category',
        'problem_description',
        'solution',
        'recommendation',
        'impact_level',
        'estimated_savings',
        'author_id',
        'validated_by',
        'status',
        'view_count',
        'likes_count',
        'material_id',
        'supplier_id',
    ];

    protected $casts = [
        'estimated_savings' => 'decimal:2',
        'view_count' => 'integer',
        'likes_count' => 'integer',
    ];

    /**
     * Relationships
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Scopes
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'Published');
    }

    public function scopeBySeciType($query, string $type)
    {
        return $query->where('seci_type', $type);
    }

    public function scopeHighImpact($query)
    {
        return $query->where('impact_level', 'Tinggi');
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessors
     */
    public function getSeciDescriptionAttribute(): string
    {
        $descriptions = [
            'Sosialisasi' => 'Tacit to Tacit - Sharing pengalaman antar penjahit',
            'Eksternalisasi' => 'Tacit to Explicit - Dokumentasi pengalaman ke SOP',
            'Kombinasi' => 'Explicit to Explicit - Analisis & sintesis data',
            'Internalisasi' => 'Explicit to Tacit - Penerapan SOP jadi keahlian',
        ];
        
        return $descriptions[$this->seci_type] ?? '';
    }

    /**
     * Business Logic
     */
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function toggleLike(): void
    {
        $this->increment('likes_count');
    }

    public function publish(): void
    {
        $this->status = 'Published';
        $this->save();
    }

    public function archive(): void
    {
        $this->status = 'Archived';
        $this->save();
    }
}
