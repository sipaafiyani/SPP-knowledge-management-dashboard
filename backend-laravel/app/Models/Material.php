<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Material Model - Bahan Baku Konveksi
 * 
 * Menerapkan dual knowledge storage:
 * - Explicit: Dokumentasi formal (SOP)
 * - Tacit: Pengalaman praktis penjahit
 */
class Material extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'stock_quantity',
        'unit',
        'threshold_min',
        'price_per_unit',
        'explicit_knowledge',
        'tacit_knowledge',
        'supplier_id',
        'avg_waste_percentage',
        'reorder_point',
        'last_updated_by',
        'last_restocked_at',
        'is_active',
    ];

    protected $casts = [
        'stock_quantity' => 'decimal:2',
        'threshold_min' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'avg_waste_percentage' => 'decimal:2',
        'is_active' => 'boolean',
        'last_restocked_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function productionLogs(): HasMany
    {
        return $this->hasMany(ProductionLog::class);
    }

    public function lessonsLearned(): HasMany
    {
        return $this->hasMany(LessonLearned::class);
    }

    /**
     * Scopes
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= threshold_min');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessors
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) return 'Habis';
        if ($this->stock_quantity <= $this->threshold_min) return 'Stok Rendah';
        if ($this->stock_quantity <= $this->threshold_min * 1.5) return 'Perlu Pesan';
        return 'Optimal';
    }

    public function getTotalValueAttribute(): float
    {
        return $this->stock_quantity * ($this->price_per_unit ?? 0);
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_quantity <= $this->threshold_min;
    }

    /**
     * Business Logic
     */
    public function updateStock(float $quantity, string $type = 'add'): void
    {
        if ($type === 'add') {
            $this->stock_quantity += $quantity;
            $this->last_restocked_at = now();
        } else {
            $this->stock_quantity -= $quantity;
        }
        
        $this->save();
    }

    public function calculateWastePercentage(): float
    {
        $logs = $this->productionLogs()
            ->where('production_date', '>=', now()->subMonths(3))
            ->get();
            
        if ($logs->isEmpty()) return 0;
        
        $avgWaste = $logs->avg('waste_percentage');
        $this->avg_waste_percentage = $avgWaste;
        $this->save();
        
        return round($avgWaste, 2);
    }
}
