<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Inventory Controller - Material Management
 * 
 * Implementasi Knowledge Creation & Storage untuk inventaris bahan baku
 */
class InventoryController extends Controller
{
    /**
     * Display a listing of materials
     */
    public function index()
    {
        try {
            $materials = Material::with(['supplier', 'lastUpdatedBy'])
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($material) {
                    return [
                        'id' => $material->id,
                        'nama_bahan' => $material->name,
                        'kategori' => $material->category,
                        'stok' => $material->stock_quantity,
                        'satuan' => $material->unit,
                        'status' => $this->getStockStatus($material),
                        'supplier' => $material->supplier?->name ?? '-',
                        'harga_per_unit' => $material->price_per_unit,
                        'threshold_min' => $material->threshold_min,
                        'explicit_knowledge' => $material->explicit_knowledge,
                        'tacit_knowledge' => $material->tacit_knowledge,
                        'last_updated' => $material->updated_at->diffForHumans(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $materials,
                'total' => $materials->count()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data inventaris',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * STORE - Penciptaan Pengetahuan (Knowledge Creation)
     * 
     * Menyimpan data bahan baru ke repositori digital sebagai explicit knowledge
     * yang dapat diakses oleh seluruh organisasi untuk monitoring aset
     */
    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'required|string|max:255',
            'kategori' => 'required|in:Bahan Utama,Bahan Pendukung,Aksesoris,Alat Produksi',
            'stok' => 'required|numeric|min:0',
            'satuan' => 'required|string|max:20',
            'harga_per_unit' => 'nullable|numeric|min:0',
            'threshold_min' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'explicit_knowledge' => 'nullable|string',
            'tacit_knowledge' => 'nullable|string',
        ], [
            'nama_bahan.required' => 'Nama bahan wajib diisi',
            'kategori.required' => 'Kategori wajib dipilih',
            'kategori.in' => 'Kategori tidak valid',
            'stok.required' => 'Jumlah stok wajib diisi',
            'stok.numeric' => 'Stok harus berupa angka',
            'satuan.required' => 'Satuan wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // KNOWLEDGE CREATION: Simpan explicit knowledge ke database
            $material = Material::create([
                'name' => $request->nama_bahan,
                'category' => $request->kategori,
                'stock_quantity' => $request->stok,
                'unit' => $request->satuan,
                'price_per_unit' => $request->harga_per_unit ?? 0,
                'threshold_min' => $request->threshold_min ?? ($request->stok * 0.2), // Default 20% dari stok awal
                'supplier_id' => $request->supplier_id,
                'explicit_knowledge' => $request->explicit_knowledge,
                'tacit_knowledge' => $request->tacit_knowledge,
                'reorder_point' => $request->threshold_min ? ($request->threshold_min * 1.5) : ($request->stok * 0.3),
                'last_updated_by' => Auth::id() ?? 1, // Default user 1 untuk demo
                'last_restocked_at' => now(),
                'is_active' => true,
            ]);

            // Load relationships
            $material->load(['supplier', 'lastUpdatedBy']);

            // Determine status
            $status = $this->getStockStatus($material);

            return response()->json([
                'success' => true,
                'message' => '✓ Barang berhasil ditambahkan! Knowledge tentang "' . $material->name . '" telah tersimpan dalam repositori digital.',
                'data' => [
                    'id' => $material->id,
                    'nama_bahan' => $material->name,
                    'kategori' => $material->category,
                    'stok' => $material->stock_quantity,
                    'satuan' => $material->unit,
                    'status' => $status,
                    'harga_per_unit' => $material->price_per_unit,
                    'threshold_min' => $material->threshold_min,
                    'supplier' => $material->supplier?->name ?? '-',
                    'created_at' => $material->created_at->format('Y-m-d H:i:s'),
                ],
                'km_insight' => 'Data telah disimpan sebagai Explicit Knowledge dalam sistem untuk memudahkan akses dan monitoring oleh manajemen.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified material
     */
    public function show($id)
    {
        try {
            $material = Material::with(['supplier', 'lastUpdatedBy'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $material->id,
                    'nama_bahan' => $material->name,
                    'kategori' => $material->category,
                    'stok' => $material->stock_quantity,
                    'satuan' => $material->unit,
                    'status' => $this->getStockStatus($material),
                    'harga_per_unit' => $material->price_per_unit,
                    'threshold_min' => $material->threshold_min,
                    'supplier' => $material->supplier,
                    'explicit_knowledge' => $material->explicit_knowledge,
                    'tacit_knowledge' => $material->tacit_knowledge,
                    'avg_waste_percentage' => $material->avg_waste_percentage,
                    'last_updated_by' => $material->lastUpdatedBy?->name,
                    'last_restocked_at' => $material->last_restocked_at,
                    'created_at' => $material->created_at,
                    'updated_at' => $material->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Update the specified material
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_bahan' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|required|in:Bahan Utama,Bahan Pendukung,Aksesoris,Alat Produksi',
            'stok' => 'sometimes|required|numeric|min:0',
            'satuan' => 'sometimes|required|string|max:20',
            'harga_per_unit' => 'nullable|numeric|min:0',
            'threshold_min' => 'nullable|numeric|min:0',
            'supplier_id' => 'nullable|exists:suppliers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $material = Material::findOrFail($id);

            $updateData = [];
            if ($request->has('nama_bahan')) $updateData['name'] = $request->nama_bahan;
            if ($request->has('kategori')) $updateData['category'] = $request->kategori;
            if ($request->has('stok')) {
                $updateData['stock_quantity'] = $request->stok;
                $updateData['last_restocked_at'] = now();
            }
            if ($request->has('satuan')) $updateData['unit'] = $request->satuan;
            if ($request->has('harga_per_unit')) $updateData['price_per_unit'] = $request->harga_per_unit;
            if ($request->has('threshold_min')) $updateData['threshold_min'] = $request->threshold_min;
            if ($request->has('supplier_id')) $updateData['supplier_id'] = $request->supplier_id;
            
            $updateData['last_updated_by'] = Auth::id() ?? 1;

            $material->update($updateData);
            $material->load(['supplier', 'lastUpdatedBy']);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil diperbarui',
                'data' => [
                    'id' => $material->id,
                    'nama_bahan' => $material->name,
                    'kategori' => $material->category,
                    'stok' => $material->stock_quantity,
                    'satuan' => $material->unit,
                    'status' => $this->getStockStatus($material),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui barang'
            ], 500);
        }
    }

    /**
     * Remove the specified material (soft delete)
     */
    public function destroy($id)
    {
        try {
            $material = Material::findOrFail($id);
            $material->update(['is_active' => false]);
            $material->delete(); // Soft delete

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dihapus'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus barang'
            ], 500);
        }
    }

    /**
     * Helper: Determine stock status
     */
    private function getStockStatus($material)
    {
        if ($material->stock_quantity <= 0) {
            return 'Habis';
        } elseif ($material->stock_quantity <= $material->threshold_min) {
            return 'Rendah';
        } elseif ($material->stock_quantity <= ($material->threshold_min * 2)) {
            return 'Cukup';
        } else {
            return 'Optimal';
        }
    }
}
