<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Lesson Learned Resource
 * 
 * API Resource untuk SECI Model implementation
 */
class LessonLearnedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'seci' => [
                'type' => $this->seci_type,
                'description' => $this->seci_description,
            ],
            'category' => $this->category,
            'content' => [
                'problem' => $this->problem_description,
                'solution' => $this->solution,
                'recommendation' => $this->recommendation,
            ],
            'impact' => [
                'level' => $this->impact_level,
                'estimated_savings' => $this->estimated_savings,
                'formatted_savings' => $this->estimated_savings 
                    ? 'Rp ' . number_format($this->estimated_savings, 0, ',', '.') 
                    : null,
            ],
            'author' => [
                'id' => $this->author->id,
                'name' => $this->author->name,
                'role' => $this->author->roles->first()?->name ?? 'Staff',
            ],
            'validation' => [
                'status' => $this->status,
                'validated_by' => $this->validator?->name,
                'validated_at' => $this->updated_at->toIso8601String(),
            ],
            'engagement' => [
                'views' => $this->view_count,
                'likes' => $this->likes_count,
            ],
            'related' => [
                'material' => $this->when($this->material, [
                    'id' => $this->material?->id,
                    'name' => $this->material?->name,
                ]),
                'supplier' => $this->when($this->supplier, [
                    'id' => $this->supplier?->id,
                    'name' => $this->supplier?->name,
                ]),
            ],
            'timestamps' => [
                'created_at' => $this->created_at->toIso8601String(),
                'created_ago' => $this->created_at->diffForHumans(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
        ];
    }
}
