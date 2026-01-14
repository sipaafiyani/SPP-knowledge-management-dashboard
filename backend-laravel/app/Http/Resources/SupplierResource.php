<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Supplier Resource
 * 
 * API Resource untuk Knowledge-Based View (KBV)
 */
class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'specialty' => $this->specialty,
            'contact' => [
                'phone' => $this->phone,
                'address' => $this->address,
                'info' => $this->contact_info,
            ],
            'kbv_scores' => [
                'quality' => (float) $this->quality_score,
                'speed' => (float) $this->speed_score,
                'reliability' => (float) $this->reliability_score,
                'overall' => $this->overall_score,
            ],
            'kbv_insight' => $this->kbv_insight,
            'is_recommended' => $this->is_recommended,
            'performance' => [
                'total_orders' => $this->total_orders,
                'on_time_deliveries' => $this->on_time_deliveries,
                'on_time_percentage' => $this->on_time_percentage,
                'last_delivery' => $this->last_delivery_date?->format('Y-m-d'),
                'last_delivery_ago' => $this->last_delivery_date?->diffForHumans(),
            ],
            'status' => [
                'is_active' => $this->is_active,
            ],
            'timestamps' => [
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
