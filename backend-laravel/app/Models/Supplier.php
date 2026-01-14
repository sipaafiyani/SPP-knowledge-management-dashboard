<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Supplier Model - Knowledge-Based View (KBV)
 * 
 * Model ini menerapkan KBV dengan menyimpan tidak hanya data transaksional,
 * tetapi juga "insight" sebagai aset pengetahuan strategis.
 */
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'specialty',
        'contact_info',
        'address',
        'phone',
        'quality_score',
        'speed_score',
        'reliability_score',
        'is_recommended',
        'kbv_insight',
        'last_delivery_date',
        'total_orders',
        'on_time_deliveries',
        'is_active',
    ];

    protected $casts = [
        'quality_score' => 'decimal:1',
        'speed_score' => 'decimal:1',
        'reliability_score' => 'decimal:1',
        'is_recommended' => 'boolean',
        'is_active' => 'boolean',
        'last_delivery_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function lessonsLearned(): HasMany
    {
        return $this->hasMany(LessonLearned::class);
    }

    /**
     * Scopes
     */
    public function scopeRecommended($query)
    {
        return $query->where('is_recommended', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessors & Mutators
     */
    public function getOverallScoreAttribute(): float
    {
        return round(
            ($this->quality_score + $this->speed_score + $this->reliability_score) / 3,
            1
        );
    }

    public function getOnTimePercentageAttribute(): float
    {
        if ($this->total_orders == 0) return 0;
        return round(($this->on_time_deliveries / $this->total_orders) * 100, 1);
    }

    /**
     * Business Logic - KBV Analytics
     */
    public function updateScores(): void
    {
        // Auto-calculate reliability based on delivery history
        $this->reliability_score = $this->on_time_percentage / 10; // Convert to 0-10 scale
        
        // Auto-recommend if all scores > 8.5
        $this->is_recommended = (
            $this->quality_score >= 8.5 &&
            $this->speed_score >= 8.5 &&
            $this->reliability_score >= 8.5
        );
        
        $this->save();
    }

    public function recordDelivery(bool $onTime = true): void
    {
        $this->total_orders++;
        if ($onTime) {
            $this->on_time_deliveries++;
        }
        $this->last_delivery_date = now();
        $this->save();
        
        $this->updateScores();
    }
}
