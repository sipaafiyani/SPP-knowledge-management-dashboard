<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Pattern Repository Resource
 * 
 * API Resource untuk digital pattern assets
 */
class PatternRepositoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pattern_code' => $this->pattern_code,
            'pattern_name' => $this->pattern_name,
            'product_type' => $this->product_type,
            'version' => $this->version,
            'files' => [
                'pattern_file' => $this->file_path ? url($this->file_path) : null,
                'thumbnail' => $this->thumbnail_path ? url($this->thumbnail_path) : null,
            ],
            'specifications' => [
                'size_variants' => $this->size_variants,
                'material_requirements' => $this->material_requirements,
                'estimated_cost' => $this->calculateMaterialCost(),
            ],
            'efficiency' => [
                'fabric_efficiency' => (float) $this->fabric_efficiency,
                'efficiency_grade' => $this->efficiency_grade,
                'avg_cutting_time' => (float) $this->avg_cutting_time,
                'avg_sewing_time' => (float) $this->avg_sewing_time,
                'total_production_time' => $this->total_production_time,
            ],
            'knowledge' => [
                'cutting_instructions' => $this->cutting_instructions,
                'sewing_instructions' => $this->sewing_instructions,
                'quality_checkpoints' => $this->quality_checkpoints,
                'common_mistakes' => $this->common_mistakes,
            ],
            'usage' => [
                'usage_count' => $this->usage_count,
                'last_used' => $this->last_used_at?->diffForHumans(),
            ],
            'status' => [
                'current_status' => $this->status,
                'has_parent' => !is_null($this->parent_pattern_id),
                'versions_count' => $this->childPatterns->count(),
            ],
            'metadata' => [
                'created_by' => $this->creator?->name,
                'approved_by' => $this->approver?->name,
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
