<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\LessonLearned;
use App\Models\ProductionLog;
use App\Models\ProductionWaste;
use App\Models\VendorDelivery;
use App\Models\KnowledgeBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Strategic Dashboard Controller
 * 
 * Controller utama untuk dashboard strategis manajemen menengah-atas
 * Mengimplementasikan Knowledge Life Cycle, SECI Model, dan Lean KM
 */
class StrategicDashboardController extends Controller
{
    /**
     * GET /api/strategic/overview
     * 
     * Metrik Utama Dashboard Strategis:
     * 1. Total Nilai Stok (Financial)
     * 2. Vendor Reliability Index (KBV)
     * 3. Knowledge Health Score (SECI)
     * 4. Lean Efficiency Score (Waste Reduction)
     */
    public function getStrategicOverview(): JsonResponse
    {
        $overview = [
            'total_stock_value' => $this->calculateTotalStockValue(),
            'vendor_reliability_index' => $this->calculateVendorReliabilityIndex(),
            'knowledge_health_score' => $this->calculateKnowledgeHealthScore(),
            'lean_efficiency_score' => $this->calculateLeanEfficiencyScore(),
            'period' => [
                'from' => now()->subDays(30)->format('Y-m-d'),
                'to' => now()->format('Y-m-d'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $overview,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * 1. Total Nilai Stok
     * Menghitung nilai total inventory dengan breakdown per kategori
     */
    private function calculateTotalStockValue(): array
    {
        // Total value
        $totalValue = Material::active()
            ->sum(DB::raw('stock_quantity * COALESCE(price_per_unit, 0)'));

        // Breakdown by category
        $byCategory = Material::active()
            ->select('category')
            ->selectRaw('SUM(stock_quantity * COALESCE(price_per_unit, 0)) as value')
            ->selectRaw('SUM(stock_quantity) as total_quantity')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'value' => round($item->value, 2),
                    'quantity' => round($item->total_quantity, 2),
                ];
            });

        // Low stock items (Strategic Alert)
        $lowStockCount = Material::lowStock()->count();
        $criticalItems = Material::lowStock()
            ->select('name', 'stock_quantity', 'unit', 'threshold_min')
            ->limit(5)
            ->get();

        // Growth comparison (30 days ago vs now)
        $growthPercentage = $this->calculateStockGrowth();

        return [
            'total_value' => [
                'amount' => round($totalValue, 2),
                'formatted' => 'Rp ' . number_format($totalValue, 0, ',', '.'),
                'growth_percentage' => $growthPercentage,
                'trend' => $growthPercentage > 0 ? 'increasing' : 'decreasing',
            ],
            'by_category' => $byCategory,
            'alerts' => [
                'low_stock_count' => $lowStockCount,
                'critical_items' => $criticalItems,
            ],
        ];
    }

    /**
     * 2. Vendor Reliability Index (KBV - Knowledge-Based View)
     * 
     * Scoring berdasarkan:
     * - On-time delivery rate
     * - Quality consistency
     * - Historical performance
     */
    private function calculateVendorReliabilityIndex(): array
    {
        $suppliers = Supplier::active()->get();
        
        $totalReliability = 0;
        $vendorScores = [];

        foreach ($suppliers as $supplier) {
            // Get recent deliveries (last 6 months)
            $recentDeliveries = VendorDelivery::where('supplier_id', $supplier->id)
                ->where('order_date', '>=', now()->subMonths(6))
                ->delivered()
                ->get();

            if ($recentDeliveries->isEmpty()) {
                $score = 0;
            } else {
                // Calculate composite score
                $onTimeRate = $recentDeliveries->where('on_time_delivery', true)->count() / $recentDeliveries->count();
                $avgQualityScore = $recentDeliveries->avg('quality_score');
                $avgDeliveryScore = $recentDeliveries->avg('delivery_score');
                
                // Weighted score
                $score = ($onTimeRate * 10 * 0.4) + ($avgQualityScore * 0.3) + ($avgDeliveryScore * 0.3);
            }

            $supplier->reliability_score = round($score, 1);
            $supplier->save();
            
            $totalReliability += $score;
            
            $vendorScores[] = [
                'supplier_id' => $supplier->id,
                'name' => $supplier->name,
                'score' => round($score, 1),
                'deliveries_count' => $recentDeliveries->count(),
                'on_time_percentage' => $recentDeliveries->isEmpty() ? 0 : round($onTimeRate * 100, 1),
                'kbv_insight' => $supplier->kbv_insight,
            ];
        }

        $avgReliability = $suppliers->count() > 0 
            ? round($totalReliability / $suppliers->count(), 1) 
            : 0;

        // Strategic recommendations
        $recommendations = $this->generateVendorRecommendations($vendorScores);

        return [
            'overall_index' => $avgReliability,
            'grade' => $this->getReliabilityGrade($avgReliability),
            'total_suppliers' => $suppliers->count(),
            'recommended_suppliers' => $suppliers->where('is_recommended', true)->count(),
            'vendor_scores' => $vendorScores,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * 3. Knowledge Health Score (SECI Model Implementation)
     * 
     * Mengukur kesehatan sistem knowledge management:
     * - Dokumentasi coverage (% materials dengan explicit + tacit knowledge)
     * - SECI activity (lessons learned creation rate)
     * - Knowledge update frequency
     * - SOP completeness
     */
    private function calculateKnowledgeHealthScore(): array
    {
        // 1. Dokumentasi Coverage (Eksternalisasi)
        $totalMaterials = Material::active()->count();
        $materialsWithKnowledge = Material::active()
            ->whereNotNull('explicit_knowledge')
            ->whereNotNull('tacit_knowledge')
            ->count();
        
        $documentationCoverage = $totalMaterials > 0 
            ? ($materialsWithKnowledge / $totalMaterials) * 100 
            : 0;

        // 2. SECI Activity Score
        $totalLessons = LessonLearned::published()->count();
        $recentLessons = LessonLearned::published()
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        
        // Activity rate: min 2 lessons per week
        $targetLessonsPerMonth = 8;
        $activityScore = min(($recentLessons / $targetLessonsPerMonth) * 100, 100);

        // 3. SECI Distribution
        $seciDistribution = LessonLearned::published()
            ->select('seci_type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('seci_type')
            ->get()
            ->pluck('count', 'seci_type');

        // 4. Knowledge Base Health
        $totalSOPs = KnowledgeBase::where('type', 'SOP')
            ->where('status', 'Published')
            ->count();
        
        $updatedSOPs = KnowledgeBase::where('type', 'SOP')
            ->where('status', 'Published')
            ->where('updated_at', '>=', now()->subMonths(3))
            ->count();
        
        $sopFreshness = $totalSOPs > 0 ? ($updatedSOPs / $totalSOPs) * 100 : 0;

        // 5. Tacit to Explicit Conversion Rate
        $tacitToExplicitRate = $this->calculateTacitToExplicitRate();

        // Calculate Overall Knowledge Health Score
        $overallScore = (
            $documentationCoverage * 0.25 +
            $activityScore * 0.25 +
            $sopFreshness * 0.25 +
            $tacitToExplicitRate * 0.25
        );

        return [
            'overall_score' => round($overallScore, 1),
            'grade' => $this->getHealthGrade($overallScore),
            'components' => [
                'documentation_coverage' => [
                    'score' => round($documentationCoverage, 1),
                    'materials_documented' => $materialsWithKnowledge,
                    'total_materials' => $totalMaterials,
                ],
                'seci_activity' => [
                    'score' => round($activityScore, 1),
                    'recent_lessons' => $recentLessons,
                    'target' => $targetLessonsPerMonth,
                    'distribution' => $seciDistribution,
                ],
                'sop_freshness' => [
                    'score' => round($sopFreshness, 1),
                    'updated_sops' => $updatedSOPs,
                    'total_sops' => $totalSOPs,
                ],
                'tacit_conversion_rate' => [
                    'score' => round($tacitToExplicitRate, 1),
                ],
            ],
            'recommendations' => $this->generateKnowledgeRecommendations($overallScore, [
                'documentation' => $documentationCoverage,
                'activity' => $activityScore,
                'freshness' => $sopFreshness,
            ]),
        ];
    }

    /**
     * 4. Lean Efficiency Score (Lean KM)
     * 
     * Mengukur efisiensi operasional:
     * - Material efficiency (100% - waste%)
     * - Waste reduction trend
     * - Preventable waste percentage
     * - Cost savings from waste reduction
     */
    private function calculateLeanEfficiencyScore(): array
    {
        // 1. Material Efficiency (30 days)
        $recentLogs = ProductionLog::where('production_date', '>=', now()->subDays(30))->get();
        
        if ($recentLogs->isEmpty()) {
            $avgWaste = 0;
        } else {
            $avgWaste = $recentLogs->avg('waste_percentage');
        }
        
        $materialEfficiency = 100 - $avgWaste;

        // 2. Waste Analysis
        $totalWaste = ProductionWaste::recentMonth()->count();
        $preventableWaste = ProductionWaste::recentMonth()->preventable()->count();
        $preventablePercentage = $totalWaste > 0 ? ($preventableWaste / $totalWaste) * 100 : 0;

        // 3. Waste Cost Impact
        $totalWasteCost = ProductionWaste::recentMonth()->sum('cost_impact');
        $wasteCostPercentage = $this->calculateWasteCostPercentage($totalWasteCost);

        // 4. Waste Trend (comparing to previous period)
        $previousPeriodWaste = ProductionLog::whereBetween('production_date', [
            now()->subDays(60),
            now()->subDays(30)
        ])->avg('waste_percentage');
        
        $wasteTrend = $previousPeriodWaste > 0 
            ? (($avgWaste - $previousPeriodWaste) / $previousPeriodWaste) * 100 
            : 0;

        // 5. Waste by Category
        $wasteByCategory = ProductionWaste::getWasteByCategory(30);

        // Calculate Overall Lean Score
        $efficiencyScore = $materialEfficiency * 0.5; // 50% weight
        $preventableScore = (100 - $preventablePercentage) * 0.3; // 30% weight
        $trendScore = $wasteTrend < 0 ? 20 : max(20 - abs($wasteTrend), 0); // 20% weight
        
        $overallLeanScore = $efficiencyScore + $preventableScore + $trendScore;

        return [
            'overall_score' => round($overallLeanScore, 1),
            'grade' => $this->getLeanGrade($overallLeanScore),
            'material_efficiency' => [
                'percentage' => round($materialEfficiency, 1),
                'waste_percentage' => round($avgWaste, 1),
                'target' => 90, // Target 90% efficiency
                'status' => $materialEfficiency >= 90 ? 'excellent' : ($materialEfficiency >= 85 ? 'good' : 'needs_improvement'),
            ],
            'waste_analysis' => [
                'total_waste_incidents' => $totalWaste,
                'preventable_count' => $preventableWaste,
                'preventable_percentage' => round($preventablePercentage, 1),
                'total_cost_impact' => round($totalWasteCost, 2),
                'cost_percentage' => round($wasteCostPercentage, 2),
                'by_category' => $wasteByCategory,
            ],
            'trend' => [
                'current_waste' => round($avgWaste, 1),
                'previous_waste' => round($previousPeriodWaste, 1),
                'change_percentage' => round($wasteTrend, 1),
                'direction' => $wasteTrend < 0 ? 'improving' : 'worsening',
            ],
            'recommendations' => $this->generateLeanRecommendations($preventablePercentage, $avgWaste, $wasteByCategory),
        ];
    }

    /**
     * Helper Methods
     */
    private function calculateStockGrowth(): float
    {
        // Simplified - should use historical snapshots
        return 12.5; // Mock data
    }

    private function calculateTacitToExplicitRate(): float
    {
        $externalizationLessons = LessonLearned::published()
            ->where('seci_type', 'Eksternalisasi')
            ->where('created_at', '>=', now()->subMonths(3))
            ->count();
        
        // Target: min 5 eksternalisasi per month
        $target = 15; // 3 months * 5
        
        return min(($externalizationLessons / $target) * 100, 100);
    }

    private function calculateWasteCostPercentage(float $wasteCost): float
    {
        $totalProductionValue = ProductionLog::where('production_date', '>=', now()->subDays(30))
            ->sum(DB::raw('quantity_used * (SELECT price_per_unit FROM materials WHERE id = production_logs.material_id)'));
        
        return $totalProductionValue > 0 ? ($wasteCost / $totalProductionValue) * 100 : 0;
    }

    private function getReliabilityGrade(float $score): string
    {
        if ($score >= 9.0) return 'A+ - Excellent';
        if ($score >= 8.5) return 'A - Very Good';
        if ($score >= 8.0) return 'B+ - Good';
        if ($score >= 7.0) return 'B - Acceptable';
        return 'C - Needs Improvement';
    }

    private function getHealthGrade(float $score): string
    {
        if ($score >= 90) return 'A - Excellent';
        if ($score >= 80) return 'B - Good';
        if ($score >= 70) return 'C - Fair';
        if ($score >= 60) return 'D - Needs Improvement';
        return 'F - Critical';
    }

    private function getLeanGrade(float $score): string
    {
        if ($score >= 90) return 'A - World Class';
        if ($score >= 85) return 'B - Competitive';
        if ($score >= 80) return 'C - Average';
        return 'D - Needs Improvement';
    }

    private function generateVendorRecommendations(array $vendorScores): array
    {
        $recommendations = [];
        
        foreach ($vendorScores as $vendor) {
            if ($vendor['score'] < 7.0) {
                $recommendations[] = [
                    'type' => 'warning',
                    'supplier' => $vendor['name'],
                    'message' => "Reliability score rendah ({$vendor['score']}/10). Pertimbangkan evaluasi ulang atau cari alternatif.",
                ];
            }
            
            if ($vendor['on_time_percentage'] < 80) {
                $recommendations[] = [
                    'type' => 'action',
                    'supplier' => $vendor['name'],
                    'message' => "On-time delivery hanya {$vendor['on_time_percentage']}%. Lakukan renegosiasi lead time.",
                ];
            }
        }
        
        return $recommendations;
    }

    private function generateKnowledgeRecommendations(float $overallScore, array $components): array
    {
        $recommendations = [];
        
        if ($components['documentation'] < 70) {
            $recommendations[] = [
                'priority' => 'high',
                'area' => 'Documentation',
                'message' => 'Dokumentasi tacit knowledge kurang dari 70%. Target: setiap material harus punya explicit + tacit knowledge.',
                'action' => 'Lakukan workshop dengan penjahit senior untuk capture knowledge.',
            ];
        }
        
        if ($components['activity'] < 50) {
            $recommendations[] = [
                'priority' => 'high',
                'area' => 'SECI Activity',
                'message' => 'Lessons learned creation rate rendah. Target: min 2 lessons per minggu.',
                'action' => 'Berikan insentif untuk staf yang aktif berbagi knowledge.',
            ];
        }
        
        if ($components['freshness'] < 60) {
            $recommendations[] = [
                'priority' => 'medium',
                'area' => 'SOP Updates',
                'message' => 'Banyak SOP yang belum diperbarui >3 bulan.',
                'action' => 'Jadwalkan review rutin SOP setiap kuartal.',
            ];
        }
        
        return $recommendations;
    }

    private function generateLeanRecommendations(float $preventable, float $avgWaste, array $wasteByCategory): array
    {
        $recommendations = [];
        
        if ($preventable > 60) {
            $recommendations[] = [
                'priority' => 'critical',
                'area' => 'Preventable Waste',
                'message' => "{$preventable}% waste bisa dicegah. Potensi penghematan besar!",
                'action' => 'Focus pada training dan SOP untuk mengurangi human error.',
            ];
        }
        
        if ($avgWaste > 15) {
            $recommendations[] = [
                'priority' => 'high',
                'area' => 'Material Efficiency',
                'message' => "Waste {$avgWaste}% melebihi threshold 15%.",
                'action' => 'Analisis pola potong dan optimasi layout cutting.',
            ];
        }
        
        // Identify highest waste category
        usort($wasteByCategory, function ($a, $b) {
            return $b['total_cost'] <=> $a['total_cost'];
        });
        
        if (!empty($wasteByCategory)) {
            $topCategory = $wasteByCategory[0];
            $recommendations[] = [
                'priority' => 'medium',
                'area' => 'Waste Category',
                'message' => "Kategori waste terbesar: {$topCategory['waste_category']}",
                'action' => 'Focus improvement di area ini terlebih dahulu.',
            ];
        }
        
        return $recommendations;
    }
}
