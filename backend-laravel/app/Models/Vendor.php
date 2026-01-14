<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Vendor Model - External Knowledge Representation
 * 
 * Model ini merepresentasikan pengetahuan eksternal tentang mitra pemasok
 * yang tersimpan dalam database sebagai Knowledge-Based View (KBV)
 */
class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vendors';

    protected $fillable = [
        'nama_vendor',
        'kategori_bahan',
        'rating_kualitas',
        'rating_kecepatan',
        'indeks_keandalan',
        'kbv_insight',
        'is_pilihan_utama',
        'contact_person',
        'phone',
        'email',
        'address',
        'last_delivery',
        'created_by',
        'last_updated_by',
        'is_active',
    ];

    protected $casts = [
        'rating_kualitas' => 'decimal:1',
        'rating_kecepatan' => 'decimal:1',
        'indeks_keandalan' => 'decimal:1',
        'is_pilihan_utama' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Relationship: Vendor created by User
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Vendor last updated by User
     */
    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Relationship: Vendor has many Materials (suppliers)
     */
    public function materials()
    {
        return $this->hasMany(Material::class, 'supplier_id');
    }

    /**
     * Scope: Active vendors only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Strategic partners only
     */
    public function scopeStrategicPartners($query)
    {
        return $query->where('is_pilihan_utama', true);
    }

    /**
     * Scope: By category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori_bahan', 'like', "%{$category}%");
    }

    /**
     * Accessor: Get overall score
     */
    public function getOverallScoreAttribute()
    {
        return round(($this->rating_kualitas + $this->rating_kecepatan + $this->indeks_keandalan) / 3, 1);
    }

    /**
     * Accessor: Get star rating (1-5 stars based on overall score)
     */
    public function getStarRatingAttribute()
    {
        return round($this->overall_score / 2);
    }

    /**
     * Check if vendor is high performer (overall score >= 8.5)
     */
    public function isHighPerformer(): bool
    {
        return $this->overall_score >= 8.5;
    }

    /**
     * Check if vendor needs improvement (overall score < 7.0)
     */
    public function needsImprovement(): bool
    {
        return $this->overall_score < 7.0;
    }
}
