<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProductionWaste Model - Lean KM Implementation
 * 
 * Tracking dan analisis waste untuk continuous improvement
 */
class ProductionWaste extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'production_log_id',
        'waste_date',
        'waste_quantity',
        'unit',
        'waste_value',
        'waste_category',
        'waste_reason',
        'preventive_action',
        'is_preventable',
        'cost_impact',
        'lesson_learned',
        'status',
        'recorded_by',
        'analyzed_by',
    ];

    protected $casts = [
        'waste_date' => 'date',
        'waste_quantity' => 'decimal:2',
        'waste_value' => 'decimal:2',
        'cost_impact' => 'decimal:2',
        'is_preventable' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function productionLog(): BelongsTo
    {
        return $this->belongsTo(ProductionLog::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function analyzedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'analyzed_by');
    }

    /**
     * Scopes
     */
    public function scopePreventable($query)
    {
        return $query->where('is_preventable', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('waste_category', $category);
    }

    public function scopeRecentMonth($query)
    {
        return $query->where('waste_date', '>=', now()->subMonth());
    }

    /**
     * Accessors
     */
    public function getWastePercentageAttribute(): float
    {
        if (!$this->productionLog) return 0;
        
        $totalUsed = $this->productionLog->quantity_used;
        if ($totalUsed == 0) return 0;
        
        return round(($this->waste_quantity / $totalUsed) * 100, 2);
    }

    /**
     * Business Logic - Lean KM Analytics
     */
    public function calculateCostImpact(): void
    {
        if ($this->material && $this->material->price_per_unit) {
            $this->cost_impact = $this->waste_quantity * $this->material->price_per_unit;
            $this->save();
        }
    }

    public function generateLessonLearned(): ?LessonLearned
    {
        // Auto-generate lesson learned jika waste >15%
        if ($this->waste_percentage <= 15) return null;
        
        $lesson = LessonLearned::create([
            'title' => "Waste Tinggi: {$this->material->name} - {$this->waste_percentage}%",
            'seci_type' => 'Eksternalisasi',
            'category' => 'Lean KM',
            'problem_description' => "Waste material {$this->waste_quantity} {$this->unit} pada produksi tanggal {$this->waste_date->format('d/m/Y')}. Kategori: {$this->waste_category}",
            'solution' => $this->preventive_action ?? 'Perlu analisis lebih lanjut',
            'impact_level' => 'Tinggi',
            'estimated_savings' => $this->cost_impact,
            'author_id' => $this->recorded_by,
            'material_id' => $this->material_id,
            'status' => 'Published',
        ]);
        
        return $lesson;
    }

    /**
     * Static Methods - Analytics
     */
    public static function getWasteByCategory(int $days = 30): array
    {
        return self::where('waste_date', '>=', now()->subDays($days))
            ->selectRaw('waste_category, COUNT(*) as count, SUM(waste_quantity) as total_quantity, SUM(cost_impact) as total_cost')
            ->groupBy('waste_category')
            ->get()
            ->toArray();
    }

    public static function getTotalWasteCost(int $days = 30): float
    {
        return self::where('waste_date', '>=', now()->subDays($days))
            ->sum('cost_impact');
    }

    public static function getPreventableWastePercentage(int $days = 30): float
    {
        $total = self::where('waste_date', '>=', now()->subDays($days))->count();
        if ($total == 0) return 0;
        
        $preventable = self::where('waste_date', '>=', now()->subDays($days))
            ->where('is_preventable', true)
            ->count();
        
        return round(($preventable / $total) * 100, 1);
    }
}
