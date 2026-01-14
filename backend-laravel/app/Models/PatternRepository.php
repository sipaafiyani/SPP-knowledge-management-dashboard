<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PatternRepository Model - Digital Pattern Assets
 * 
 * Menyimpan pola jahit sebagai organizational knowledge asset
 */
class PatternRepository extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pattern_repository';

    protected $fillable = [
        'pattern_code',
        'pattern_name',
        'product_type',
        'file_path',
        'thumbnail_path',
        'size_variants',
        'material_requirements',
        'fabric_efficiency',
        'avg_cutting_time',
        'avg_sewing_time',
        'cutting_instructions',
        'sewing_instructions',
        'quality_checkpoints',
        'common_mistakes',
        'version',
        'parent_pattern_id',
        'usage_count',
        'last_used_at',
        'status',
        'created_by',
        'approved_by',
    ];

    protected $casts = [
        'size_variants' => 'array',
        'material_requirements' => 'array',
        'fabric_efficiency' => 'decimal:2',
        'avg_cutting_time' => 'decimal:2',
        'avg_sewing_time' => 'decimal:2',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function parentPattern(): BelongsTo
    {
        return $this->belongsTo(PatternRepository::class, 'parent_pattern_id');
    }

    public function childPatterns(): HasMany
    {
        return $this->hasMany(PatternRepository::class, 'parent_pattern_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeByProductType($query, string $type)
    {
        return $query->where('product_type', $type);
    }

    public function scopeHighEfficiency($query, float $threshold = 85.0)
    {
        return $query->where('fabric_efficiency', '>=', $threshold);
    }

    public function scopePopular($query, int $minUsage = 10)
    {
        return $query->where('usage_count', '>=', $minUsage);
    }

    /**
     * Accessors
     */
    public function getTotalProductionTimeAttribute(): float
    {
        return ($this->avg_cutting_time ?? 0) + ($this->avg_sewing_time ?? 0);
    }

    public function getEfficiencyGradeAttribute(): string
    {
        $efficiency = $this->fabric_efficiency ?? 0;
        
        if ($efficiency >= 90) return 'A - Excellent';
        if ($efficiency >= 85) return 'B - Good';
        if ($efficiency >= 80) return 'C - Fair';
        return 'D - Needs Improvement';
    }

    /**
     * Business Logic
     */
    public function recordUsage(): void
    {
        $this->usage_count++;
        $this->last_used_at = now();
        $this->save();
    }

    public function createNewVersion(array $changes): self
    {
        $newVersion = $this->replicate();
        $newVersion->parent_pattern_id = $this->id;
        $newVersion->version = $this->incrementVersion();
        $newVersion->status = 'Draft';
        
        foreach ($changes as $key => $value) {
            $newVersion->$key = $value;
        }
        
        $newVersion->save();
        return $newVersion;
    }

    private function incrementVersion(): string
    {
        $parts = explode('.', $this->version);
        $parts[1] = (int)$parts[1] + 1;
        return implode('.', $parts);
    }

    /**
     * Calculate material cost for this pattern
     */
    public function calculateMaterialCost(): float
    {
        $totalCost = 0;
        
        foreach ($this->material_requirements as $requirement) {
            $material = Material::find($requirement['material_id']);
            if ($material) {
                $totalCost += $requirement['quantity'] * ($material->price_per_unit ?? 0);
            }
        }
        
        return $totalCost;
    }

    /**
     * Get recommended materials for this pattern
     */
    public function getRecommendedMaterials(): array
    {
        $materials = [];
        
        foreach ($this->material_requirements as $requirement) {
            $material = Material::with('supplier')
                ->find($requirement['material_id']);
            
            if ($material) {
                $materials[] = [
                    'material' => $material,
                    'required_quantity' => $requirement['quantity'],
                    'unit' => $requirement['unit'] ?? $material->unit,
                    'available_stock' => $material->stock_quantity,
                    'is_sufficient' => $material->stock_quantity >= $requirement['quantity'],
                ];
            }
        }
        
        return $materials;
    }
}
