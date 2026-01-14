<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VendorDelivery Model - KBV Tracking
 * 
 * Tracking delivery untuk supplier reliability scoring
 */
class VendorDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'material_id',
        'po_number',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'quantity_ordered',
        'quantity_delivered',
        'unit',
        'price_per_unit',
        'total_price',
        'color_consistency',
        'material_quality',
        'on_time_delivery',
        'delay_days',
        'quality_notes',
        'delivery_notes',
        'defect_rate',
        'status',
        'received_by',
        'inspected_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'quantity_ordered' => 'decimal:2',
        'quantity_delivered' => 'decimal:2',
        'price_per_unit' => 'decimal:2',
        'total_price' => 'decimal:2',
        'defect_rate' => 'decimal:2',
        'on_time_delivery' => 'boolean',
        'delay_days' => 'integer',
    ];

    /**
     * Relationships
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    /**
     * Scopes
     */
    public function scopeOnTime($query)
    {
        return $query->where('on_time_delivery', true);
    }

    public function scopeDelayed($query)
    {
        return $query->where('on_time_delivery', false);
    }

    public function scopeDelivered($query)
    {
        return $query->whereIn('status', ['Delivered', 'Inspected', 'Accepted']);
    }

    /**
     * Accessors
     */
    public function getQualityScoreAttribute(): float
    {
        $colorScore = match($this->color_consistency) {
            'Excellent' => 10,
            'Good' => 8,
            'Fair' => 6,
            'Poor' => 4,
            default => 0
        };
        
        $materialScore = match($this->material_quality) {
            'Excellent' => 10,
            'Good' => 8,
            'Fair' => 6,
            'Poor' => 4,
            default => 0
        };
        
        $defectScore = 10 - ($this->defect_rate ?? 0);
        
        return round(($colorScore + $materialScore + $defectScore) / 3, 1);
    }

    public function getDeliveryScoreAttribute(): float
    {
        if (!$this->on_time_delivery) {
            // Penalty based on delay
            $penalty = min($this->delay_days * 0.5, 5); // Max 5 points penalty
            return max(10 - $penalty, 0);
        }
        
        return 10;
    }

    /**
     * Business Logic
     */
    public function recordDelivery(array $data): void
    {
        $this->actual_delivery_date = $data['delivery_date'] ?? now();
        $this->quantity_delivered = $data['quantity_delivered'];
        $this->status = 'Delivered';
        
        // Calculate delay
        $this->delay_days = $this->actual_delivery_date->diffInDays($this->expected_delivery_date, false);
        $this->on_time_delivery = $this->delay_days <= 0;
        
        $this->save();
        
        // Update supplier scores
        $this->supplier->recordDelivery($this->on_time_delivery);
    }

    public function inspectQuality(array $inspectionData): void
    {
        $this->color_consistency = $inspectionData['color_consistency'];
        $this->material_quality = $inspectionData['material_quality'];
        $this->defect_rate = $inspectionData['defect_rate'] ?? 0;
        $this->quality_notes = $inspectionData['quality_notes'] ?? null;
        $this->inspected_by = $inspectionData['inspected_by'];
        $this->status = 'Inspected';
        
        $this->save();
        
        // Update supplier quality score
        $this->supplier->quality_score = $this->calculateSupplierQualityScore();
        $this->supplier->save();
    }

    private function calculateSupplierQualityScore(): float
    {
        $recentDeliveries = VendorDelivery::where('supplier_id', $this->supplier_id)
            ->where('status', 'Inspected')
            ->orderByDesc('actual_delivery_date')
            ->limit(10)
            ->get();
        
        if ($recentDeliveries->isEmpty()) return 0;
        
        return $recentDeliveries->avg('quality_score');
    }

    /**
     * Generate KBV Insight
     */
    public function generateKbvInsight(): ?string
    {
        $insights = [];
        
        if ($this->on_time_delivery) {
            $insights[] = "Pengiriman tepat waktu";
        } else {
            $insights[] = "Terlambat {$this->delay_days} hari";
        }
        
        if ($this->color_consistency === 'Excellent') {
            $insights[] = "Konsistensi warna excellent";
        }
        
        if ($this->defect_rate > 5) {
            $insights[] = "Cacat material tinggi ({$this->defect_rate}%)";
        }
        
        return !empty($insights) ? implode('. ', $insights) : null;
    }
}
