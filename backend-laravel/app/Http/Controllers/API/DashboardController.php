<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\LessonLearned;
use App\Models\ProductionLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Controller - KM Analytics
 * 
 * Menyediakan metrik strategis untuk pemilik UMKM Konveksi
 * berdasarkan prinsip Knowledge Management
 */
class DashboardController extends Controller
{
    /**
     * Get Dashboard Metrics (KM-based)
     * 
     * Menghitung 4 metrik utama:
     * 1. Total Nilai Stok Bahan
     * 2. Efisiensi Bahan (Lean KM)
     * 3. Indeks Keandalan Supplier (KBV)
     * 4. Skor Kesehatan Pengetahuan
     */
    public function getMetrics(): JsonResponse
    {
        // 1. Total Nilai Stok
        $totalStockValue = Material::active()
            ->sum(DB::raw('stock_quantity * COALESCE(price_per_unit, 0)'));

        // Perubahan 30 hari terakhir
        $stockGrowth = $this->calculateStockGrowth();

        // 2. Efisiensi Bahan (Lean KM)
        $efficiency = $this->calculateMaterialEfficiency();
        
        // 3. Indeks Keandalan Supplier (KBV)
        $supplierReliability = Supplier::active()
            ->avg(DB::raw('(quality_score + speed_score + reliability_score) / 3'));

        // 4. Skor Kesehatan Pengetahuan
        $knowledgeHealth = $this->calculateKnowledgeHealth();

        return response()->json([
            'success' => true,
            'data' => [
                'total_stock_value' => [
                    'value' => 'Rp ' . number_format($totalStockValue, 0, ',', '.'),
                    'raw_value' => $totalStockValue,
                    'change' => $stockGrowth['percentage'] . '%',
                    'positive' => $stockGrowth['positive'],
                    'description' => 'Nilai total bahan baku & pendukung',
                ],
                'material_efficiency' => [
                    'value' => $efficiency['percentage'] . '%',
                    'raw_value' => $efficiency['percentage'],
                    'change' => 'Waste ' . $efficiency['waste'] . '% (Target <10%)',
                    'positive' => $efficiency['waste'] < 10,
                    'description' => 'Persentase utilisasi bahan vs sisa potongan',
                ],
                'supplier_reliability' => [
                    'value' => number_format($supplierReliability, 1) . '/10',
                    'raw_value' => $supplierReliability,
                    'change' => 'Ketepatan waktu & kualitas warna',
                    'positive' => $supplierReliability >= 8.0,
                    'description' => 'Berdasarkan on-time delivery dan konsistensi',
                ],
                'knowledge_health' => [
                    'value' => $knowledgeHealth['score'] . '%',
                    'raw_value' => $knowledgeHealth['score'],
                    'change' => 'SOP & QC diperbarui ' . $knowledgeHealth['last_update'],
                    'positive' => $knowledgeHealth['score'] >= 70,
                    'description' => 'Tingkat kelengkapan dokumentasi tacit to explicit',
                ],
            ],
        ]);
    }

    /**
     * Get Recent Knowledge Updates (SECI Model)
     */
    public function getRecentKnowledge(): JsonResponse
    {
        $recentLessons = LessonLearned::published()
            ->with(['author:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($lesson) {
                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'updated' => $lesson->created_at->diffForHumans(),
                    'type' => $lesson->seci_type,
                    'author' => $lesson->author->name ?? 'Unknown',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $recentLessons,
        ]);
    }

    /**
     * Get Alerts & Recommendations
     */
    public function getAlerts(): JsonResponse
    {
        $alerts = [];

        // Low Stock Alerts
        $lowStock = Material::lowStock()->count();
        if ($lowStock > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Stok Rendah',
                'message' => "$lowStock jenis bahan di bawah ambang batas minimum",
                'action' => 'Lihat Inventaris',
            ];
        }

        // High Waste Alert (Lean KM)
        $highWaste = ProductionLog::where('production_date', '>=', now()->subDays(7))
            ->where('waste_percentage', '>', 15)
            ->count();
        
        if ($highWaste > 0) {
            $alerts[] = [
                'type' => 'danger',
                'title' => 'Waste Tinggi',
                'message' => "$highWaste catatan produksi dengan waste >15% minggu ini",
                'action' => 'Analisis Penyebab',
            ];
        }

        // Seasonal Prediction Alert
        // TODO: Implement seasonal prediction check

        return response()->json([
            'success' => true,
            'data' => $alerts,
        ]);
    }

    /**
     * Private Helper Methods
     */
    private function calculateStockGrowth(): array
    {
        // Simplified calculation - should use historical data
        $currentValue = Material::active()->sum(DB::raw('stock_quantity * COALESCE(price_per_unit, 0)'));
        // Mock previous value (should be from archive table)
        $previousValue = $currentValue * 0.89; // Assuming 12.5% growth
        
        $growth = (($currentValue - $previousValue) / $previousValue) * 100;
        
        return [
            'percentage' => round($growth, 1),
            'positive' => $growth > 0,
        ];
    }

    private function calculateMaterialEfficiency(): array
    {
        $recentLogs = ProductionLog::where('production_date', '>=', now()->subDays(30))->get();
        
        if ($recentLogs->isEmpty()) {
            return ['percentage' => 0, 'waste' => 0];
        }

        $avgWaste = $recentLogs->avg('waste_percentage');
        $efficiency = 100 - $avgWaste;

        return [
            'percentage' => round($efficiency, 0),
            'waste' => round($avgWaste, 0),
        ];
    }

    private function calculateKnowledgeHealth(): array
    {
        $totalMaterials = Material::active()->count();
        $materialsWithKnowledge = Material::active()
            ->whereNotNull('explicit_knowledge')
            ->whereNotNull('tacit_knowledge')
            ->count();

        $totalLessons = LessonLearned::published()->count();
        $recentLessons = LessonLearned::published()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        // Weighted score
        $documentationScore = $totalMaterials > 0 
            ? ($materialsWithKnowledge / $totalMaterials) * 50 
            : 0;
        
        $activityScore = $totalLessons > 0
            ? min(($recentLessons / $totalLessons) * 50, 50)
            : 0;

        $healthScore = round($documentationScore + $activityScore, 0);

        $lastLesson = LessonLearned::published()
            ->orderByDesc('created_at')
            ->first();

        return [
            'score' => $healthScore,
            'last_update' => $lastLesson ? $lastLesson->created_at->diffForHumans() : 'Tidak ada data',
        ];
    }
}
