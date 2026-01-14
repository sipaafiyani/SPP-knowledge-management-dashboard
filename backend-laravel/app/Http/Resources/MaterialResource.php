<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Material Resource
 * 
 * API Resource untuk mengirimkan data material ke frontend
 * dengan knowledge management attributes
 */
class MaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'stock' => [
                'quantity' => (float) $this->stock_quantity,
                'unit' => $this->unit,
                'threshold' => (float) $this->threshold_min,
                'status' => $this->stock_status,
                'is_low' => $this->is_low_stock,
            ],
            'pricing' => [
                'per_unit' => (float) $this->price_per_unit,
                'total_value' => $this->total_value,
                'formatted' => 'Rp ' . number_format($this->total_value, 0, ',', '.'),
            ],
            'knowledge' => [
                'explicit' => $this->explicit_knowledge,
                'tacit' => $this->tacit_knowledge,
                'has_complete_knowledge' => !empty($this->explicit_knowledge) && !empty($this->tacit_knowledge),
            ],
            'supplier' => $this->when($this->supplier, [
                'id' => $this->supplier?->id,
                'name' => $this->supplier?->name,
                'reliability_score' => $this->supplier?->reliability_score,
                'is_recommended' => $this->supplier?->is_recommended,
            ]),
            'efficiency' => [
                'avg_waste_percentage' => (float) $this->avg_waste_percentage,
                'reorder_point' => $this->reorder_point,
            ],
            'metadata' => [
                'last_restocked' => $this->last_restocked_at?->diffForHumans(),
                'last_updated_by' => $this->updatedBy?->name,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
