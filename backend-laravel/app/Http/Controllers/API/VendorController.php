<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Vendor Controller - Supplier Intelligence & Knowledge Storage
 * 
 * Implementasi Knowledge-Based View untuk mengelola pengetahuan
 * tentang mitra pemasok strategis (external knowledge)
 */
class VendorController extends Controller
{
    /**
     * Display a listing of vendors
     */
    public function index()
    {
        try {
            $vendors = Vendor::with(['createdBy', 'lastUpdatedBy'])
                ->where('is_active', true)
                ->orderByDesc('is_pilihan_utama') // Strategic partners first
                ->orderByDesc('created_at')
                ->get()
                ->map(function ($vendor) {
                    return [
                        'id' => $vendor->id,
                        'nama_vendor' => $vendor->nama_vendor,
                        'kategori_bahan' => $vendor->kategori_bahan,
                        'rating_kualitas' => $vendor->rating_kualitas,
                        'rating_kecepatan' => $vendor->rating_kecepatan,
                        'indeks_keandalan' => $vendor->indeks_keandalan,
                        'kbv_insight' => $vendor->kbv_insight,
                        'is_pilihan_utama' => $vendor->is_pilihan_utama,
                        'last_delivery' => $vendor->last_delivery,
                        'overall_score' => round(($vendor->rating_kualitas + $vendor->rating_kecepatan + $vendor->indeks_keandalan) / 3, 1),
                        'contact_person' => $vendor->contact_person,
                        'phone' => $vendor->phone,
                        'email' => $vendor->email,
                        'created_at' => $vendor->created_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $vendors,
                'total' => $vendors->count(),
                'strategic_partners' => $vendors->where('is_pilihan_utama', true)->count(),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * STORE - Knowledge Storage untuk Vendor (External Knowledge)
     * 
     * Menyimpan pengetahuan tentang mitra pemasok sebagai aset intelektual
     * organisasi untuk meningkatkan daya saing dalam pemilihan supplier
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_vendor' => 'required|string|max:255',
            'kategori_bahan' => 'required|string|max:255',
            'rating_kualitas' => 'required|numeric|min:1|max:10',
            'rating_kecepatan' => 'required|numeric|min:1|max:10',
            'indeks_keandalan' => 'required|numeric|min:1|max:10',
            'kbv_insight' => 'required|string|min:10',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ], [
            'nama_vendor.required' => 'Nama vendor wajib diisi',
            'kategori_bahan.required' => 'Kategori bahan wajib diisi',
            'rating_kualitas.required' => 'Rating kualitas wajib diisi',
            'rating_kualitas.min' => 'Rating kualitas minimal 1',
            'rating_kualitas.max' => 'Rating kualitas maksimal 10',
            'rating_kecepatan.required' => 'Rating kecepatan wajib diisi',
            'rating_kecepatan.min' => 'Rating kecepatan minimal 1',
            'rating_kecepatan.max' => 'Rating kecepatan maksimal 10',
            'indeks_keandalan.required' => 'Indeks keandalan wajib diisi',
            'indeks_keandalan.min' => 'Indeks keandalan minimal 1',
            'indeks_keandalan.max' => 'Indeks keandalan maksimal 10',
            'kbv_insight.required' => 'KBV Insight wajib diisi',
            'kbv_insight.min' => 'KBV Insight minimal 10 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Calculate overall score to determine strategic partner
            $overallScore = ($request->rating_kualitas + $request->rating_kecepatan + $request->indeks_keandalan) / 3;
            $isStrategicPartner = $overallScore >= 8.5; // Auto-assign if score >= 8.5

            // KNOWLEDGE STORAGE: Simpan external knowledge tentang vendor
            $vendor = Vendor::create([
                'nama_vendor' => $request->nama_vendor,
                'kategori_bahan' => $request->kategori_bahan,
                'rating_kualitas' => $request->rating_kualitas,
                'rating_kecepatan' => $request->rating_kecepatan,
                'indeks_keandalan' => $request->indeks_keandalan,
                'kbv_insight' => $request->kbv_insight,
                'is_pilihan_utama' => $isStrategicPartner,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'last_delivery' => 'Belum ada pengiriman',
                'created_by' => Auth::id() ?? 1, // Default user 1 untuk demo
                'last_updated_by' => Auth::id() ?? 1,
                'is_active' => true,
            ]);

            // Load relationships
            $vendor->load(['createdBy', 'lastUpdatedBy']);

            $message = "✓ Vendor '{$vendor->nama_vendor}' berhasil ditambahkan!";
            if ($isStrategicPartner) {
                $message .= " 🌟 Vendor ini secara otomatis ditandai sebagai 'Pilihan Utama' karena skor keseluruhan {$overallScore}/10.";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $vendor->id,
                    'nama_vendor' => $vendor->nama_vendor,
                    'kategori_bahan' => $vendor->kategori_bahan,
                    'rating_kualitas' => $vendor->rating_kualitas,
                    'rating_kecepatan' => $vendor->rating_kecepatan,
                    'indeks_keandalan' => $vendor->indeks_keandalan,
                    'overall_score' => round($overallScore, 1),
                    'kbv_insight' => $vendor->kbv_insight,
                    'is_pilihan_utama' => $vendor->is_pilihan_utama,
                    'created_at' => $vendor->created_at->format('Y-m-d H:i:s'),
                ],
                'km_insight' => 'Pengetahuan tentang vendor telah tersimpan dalam repositori Knowledge-Based View untuk mendukung keputusan strategis organisasi.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan vendor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified vendor
     */
    public function show($id)
    {
        try {
            $vendor = Vendor::with(['createdBy', 'lastUpdatedBy'])->findOrFail($id);

            $overallScore = ($vendor->rating_kualitas + $vendor->rating_kecepatan + $vendor->indeks_keandalan) / 3;

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $vendor->id,
                    'nama_vendor' => $vendor->nama_vendor,
                    'kategori_bahan' => $vendor->kategori_bahan,
                    'rating_kualitas' => $vendor->rating_kualitas,
                    'rating_kecepatan' => $vendor->rating_kecepatan,
                    'indeks_keandalan' => $vendor->indeks_keandalan,
                    'overall_score' => round($overallScore, 1),
                    'kbv_insight' => $vendor->kbv_insight,
                    'is_pilihan_utama' => $vendor->is_pilihan_utama,
                    'contact_person' => $vendor->contact_person,
                    'phone' => $vendor->phone,
                    'email' => $vendor->email,
                    'address' => $vendor->address,
                    'last_delivery' => $vendor->last_delivery,
                    'created_by' => $vendor->createdBy?->name,
                    'last_updated_by' => $vendor->lastUpdatedBy?->name,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified vendor
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_vendor' => 'sometimes|required|string|max:255',
            'kategori_bahan' => 'sometimes|required|string|max:255',
            'rating_kualitas' => 'sometimes|required|numeric|min:1|max:10',
            'rating_kecepatan' => 'sometimes|required|numeric|min:1|max:10',
            'indeks_keandalan' => 'sometimes|required|numeric|min:1|max:10',
            'kbv_insight' => 'sometimes|required|string|min:10',
            'is_pilihan_utama' => 'sometimes|boolean',
            'last_delivery' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vendor = Vendor::findOrFail($id);

            $updateData = $request->only([
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
            ]);
            
            $updateData['last_updated_by'] = Auth::id() ?? 1;

            $vendor->update($updateData);
            $vendor->load(['createdBy', 'lastUpdatedBy']);

            $overallScore = ($vendor->rating_kualitas + $vendor->rating_kecepatan + $vendor->indeks_keandalan) / 3;

            return response()->json([
                'success' => true,
                'message' => 'Vendor berhasil diperbarui',
                'data' => [
                    'id' => $vendor->id,
                    'nama_vendor' => $vendor->nama_vendor,
                    'overall_score' => round($overallScore, 1),
                    'is_pilihan_utama' => $vendor->is_pilihan_utama,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui vendor'
            ], 500);
        }
    }

    /**
     * Remove the specified vendor (soft delete)
     */
    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->update(['is_active' => false]);
            $vendor->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Vendor berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus vendor'
            ], 500);
        }
    }

    /**
     * Get strategic partners only
     */
    public function strategicPartners()
    {
        try {
            $vendors = Vendor::where('is_pilihan_utama', true)
                ->where('is_active', true)
                ->orderByDesc('created_at')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $vendors,
                'total' => $vendors->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data strategic partners'
            ], 500);
        }
    }
}
