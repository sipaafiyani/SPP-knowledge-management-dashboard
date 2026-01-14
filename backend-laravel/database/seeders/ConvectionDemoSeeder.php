<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Material;
use App\Models\LessonLearned;
use App\Models\ProductionLog;
use App\Models\ProductionWaste;
use App\Models\KnowledgeBase;
use App\Models\SeasonalPrediction;
use App\Models\PatternRepository;
use App\Models\VendorDelivery;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

/**
 * Convection Demo Seeder
 * 
 * Seeder untuk data demo UMKM Konveksi dengan Knowledge Management
 */
class ConvectionDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding demo data for Strategic KM Inventory Dashboard...');

        // 1. Create Users
        $this->command->info('Creating users...');
        $owner = User::create([
            'name' => 'Budi Santoso',
            'email' => 'owner@konveksi.com',
            'password' => Hash::make('password'),
            'role' => 'owner',
        ]);

        $manager = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'manager@konveksi.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        $staff1 = User::create([
            'name' => 'Ahmad Wijaya',
            'email' => 'ahmad@konveksi.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        $staff2 = User::create([
            'name' => 'Rina Kurniawati',
            'email' => 'rina@konveksi.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // 2. Create Suppliers with KBV data
        $this->command->info('Creating suppliers...');
        $suppliers = [
            [
                'name' => 'PT Tekstil Nusantara',
                'specialty' => 'Kain Katun Premium',
                'phone' => '021-5551234',
                'address' => 'Jl. Industri No. 15, Tangerang',
                'quality_score' => 9.2,
                'speed_score' => 8.5,
                'reliability_score' => 9.0,
                'kbv_insight' => 'Supplier terpercaya untuk kain katun berkualitas tinggi. Konsisten dalam pengiriman tepat waktu. Harga kompetitif untuk order bulk.',
            ],
            [
                'name' => 'CV Benang Jaya',
                'specialty' => 'Benang Jahit Polyester',
                'phone' => '021-5552345',
                'address' => 'Jl. Raya Bogor KM 25, Jakarta Timur',
                'quality_score' => 8.8,
                'speed_score' => 9.0,
                'reliability_score' => 8.9,
                'kbv_insight' => 'Supplier benang dengan response time cepat. Stok selalu tersedia. Kadang terjadi variasi warna minor antar batch.',
            ],
            [
                'name' => 'Toko Aksesoris Mandiri',
                'specialty' => 'Kancing dan Aksesoris',
                'phone' => '021-5553456',
                'address' => 'Pasar Tanah Abang Blok B No. 88, Jakarta Pusat',
                'quality_score' => 7.5,
                'speed_score' => 8.0,
                'reliability_score' => 7.8,
                'kbv_insight' => 'Supplier lokal dengan harga terjangkau. Perlu QC lebih ketat untuk kancing plastik. Kancing logam berkualitas baik.',
            ],
            [
                'name' => 'PT Fabric Indonesia',
                'specialty' => 'Kain Drill & Canvas',
                'phone' => '022-7771234',
                'address' => 'Jl. Raya Cimahi No. 45, Bandung',
                'quality_score' => 8.9,
                'speed_score' => 7.8,
                'reliability_score' => 8.4,
                'kbv_insight' => 'Kualitas kain drill sangat baik. Lead time agak lama (7-10 hari). Cocok untuk order terencana, bukan urgent.',
            ],
            [
                'name' => 'UD Polyester Mulia',
                'specialty' => 'Kain Polyester',
                'phone' => '031-8881234',
                'address' => 'Jl. Industri Raya No. 120, Surabaya',
                'quality_score' => 8.2,
                'speed_score' => 8.8,
                'reliability_score' => 8.5,
                'kbv_insight' => 'Polyester dengan harga kompetitif. Pengiriman cepat ke Jawa. Perlu tes shrinkage sebelum produksi massal.',
            ],
        ];

        foreach ($suppliers as $supplierData) {
            Supplier::create($supplierData);
        }

        // 3. Create Materials with knowledge
        $this->command->info('Creating materials...');
        $materials = [
            [
                'supplier_id' => 1,
                'name' => 'Kain Katun Combed 30s',
                'category' => 'Kain',
                'stock_quantity' => 250.00,
                'unit' => 'meter',
                'price_per_unit' => 35000,
                'threshold_min' => 50.00,
                'reorder_point' => 75.00,
                'avg_waste_percentage' => 8.5,
                'explicit_knowledge' => 'SOP: Cuci sample sebelum cutting untuk cek shrinkage. Gunting dengan rotary cutter untuk hasil rapi. Simpan di ruang AC untuk mencegah jamur.',
                'tacit_knowledge' => 'Katun combed 30s ini lebih mudah dijahit dibanding 24s. Kalau terlalu kencang tarikan benangnya bisa berkerut. Setrika dengan steam untuk hasil maksimal.',
                'created_by' => 3,
                'updated_by' => 3,
                'last_restocked_at' => Carbon::now()->subDays(5),
            ],
            [
                'supplier_id' => 4,
                'name' => 'Kain Drill Tebal',
                'category' => 'Kain',
                'stock_quantity' => 180.00,
                'unit' => 'meter',
                'price_per_unit' => 42000,
                'threshold_min' => 40.00,
                'reorder_point' => 60.00,
                'avg_waste_percentage' => 10.2,
                'explicit_knowledge' => 'SOP: Gunakan jarum mesin nomor 16 untuk ketebalan optimal. Potong dengan margin 2cm untuk sambungan. Pre-wash wajib untuk produk celana.',
                'tacit_knowledge' => 'Drill ini agak keras di mesin jahit lama. Pakai jarum baru setiap 5 potong celana biar gak patah. Kalau mau jahit lurus pake kaki jahit khusus denim.',
                'created_by' => 4,
                'updated_by' => 4,
                'last_restocked_at' => Carbon::now()->subDays(12),
            ],
            [
                'supplier_id' => 2,
                'name' => 'Benang Jahit Polyester 40/2',
                'category' => 'Benang',
                'stock_quantity' => 85.00,
                'unit' => 'cone',
                'price_per_unit' => 18000,
                'threshold_min' => 20.00,
                'reorder_point' => 30.00,
                'avg_waste_percentage' => 5.0,
                'explicit_knowledge' => 'SOP: Simpan di tempat kering jauh dari sinar matahari. Cek tension benang sebelum jahit. 1 cone untuk ~15 kemeja standar.',
                'tacit_knowledge' => 'Benang ini cocok untuk mesin jahit high speed. Kalau putus-putus cek dulu bobbin-nya, biasanya masalahnya di situ. Warna putih paling laku.',
                'created_by' => 3,
                'updated_by' => 3,
                'last_restocked_at' => Carbon::now()->subDays(8),
            ],
            [
                'supplier_id' => 3,
                'name' => 'Kancing Baju Plastik 14mm',
                'category' => 'Aksesoris',
                'stock_quantity' => 5000.00,
                'unit' => 'pcs',
                'price_per_unit' => 150,
                'threshold_min' => 1000.00,
                'reorder_point' => 1500.00,
                'avg_waste_percentage' => 3.5,
                'explicit_knowledge' => 'SOP: QC sebelum pasang - cek retak dan lubang. Simpan per warna dalam kotak terpisah. Standar 1 kemeja = 7 kancing.',
                'tacit_knowledge' => 'Kancing ini kadang warnanya sedikit beda antar batch. Kalau order banyak mending ambil sekaligus. Pasang pakai mesin khusus lebih rapi dari manual.',
                'created_by' => 4,
                'updated_by' => 4,
                'last_restocked_at' => Carbon::now()->subDays(15),
            ],
            [
                'supplier_id' => 5,
                'name' => 'Kain Polyester Dry-fit',
                'category' => 'Kain',
                'stock_quantity' => 120.00,
                'unit' => 'meter',
                'price_per_unit' => 38000,
                'threshold_min' => 30.00,
                'reorder_point' => 50.00,
                'avg_waste_percentage' => 9.0,
                'explicit_knowledge' => 'SOP: Jangan setrika langsung dengan suhu tinggi. Gunakan kain pelapis. Jahit dengan benang polyester matching. Cocok untuk jersey olahraga.',
                'tacit_knowledge' => 'Polyester ini licin banget, susah dipotong kalau gak pakai pemberat. Pakai roller cutter dengan mat khusus. Jahitnya pakai stretch stitch biar gak pecah.',
                'created_by' => 3,
                'updated_by' => 4,
                'last_restocked_at' => Carbon::now()->subDays(20),
            ],
        ];

        foreach ($materials as $materialData) {
            Material::create($materialData);
        }

        // 4. Create Lessons Learned (SECI Model)
        $this->command->info('Creating lessons learned...');
        $lessons = [
            [
                'title' => 'Teknik Pencegahan Shrinkage pada Katun Combed',
                'seci_type' => 'eksternalisasi',
                'category' => 'Produksi',
                'problem_description' => 'Sering terjadi komplain dari customer karena baju menyusut setelah dicuci pertama kali, terutama untuk bahan katun combed 30s.',
                'solution' => 'Implementasi pre-wash pada semua kain katun sebelum proses cutting. Rendam dalam air hangat 30-40°C selama 30 menit, keringkan, lalu setrika sebelum dipotong.',
                'recommendation' => 'Tambahkan label care instruction yang jelas pada produk. Edukasi customer tentang cara perawatan kain katun yang benar.',
                'impact_level' => 'tinggi',
                'estimated_savings' => 2500000,
                'material_id' => 1,
                'author_id' => 3,
                'status' => 'published',
                'view_count' => 45,
                'likes_count' => 12,
            ],
            [
                'title' => 'Optimasi Penggunaan Benang untuk Efisiensi Cost',
                'seci_type' => 'kombinasi',
                'category' => 'Efisiensi',
                'problem_description' => 'Biaya benang meningkat 15% dalam 3 bulan terakhir. Perlu strategi penghematan tanpa mengurangi kualitas.',
                'solution' => 'Kombinasi data historis penggunaan benang dengan teknik jahit yang lebih efisien. Gunakan setting tension yang optimal untuk mengurangi putus benang. Implementasi checklist maintenance mesin rutin.',
                'recommendation' => 'Buat database penggunaan benang per jenis produk. Training staff tentang setting mesin yang tepat. Negosiasi bulk order dengan supplier untuk harga lebih baik.',
                'impact_level' => 'tinggi',
                'estimated_savings' => 3200000,
                'material_id' => 3,
                'author_id' => 2,
                'status' => 'published',
                'view_count' => 38,
                'likes_count' => 15,
            ],
            [
                'title' => 'Sharing Pengalaman: Mengatasi Kain Drill yang Keras di Mesin Lama',
                'seci_type' => 'sosialisasi',
                'category' => 'Produksi',
                'problem_description' => 'Tim jahit sering kesulitan saat menjahit kain drill tebal menggunakan mesin jahit yang sudah berusia 5+ tahun. Jarum sering patah dan hasil jahitan tidak rapi.',
                'solution' => 'Sharing session dari operator senior: (1) Ganti jarum dengan nomor 18 khusus heavy duty, (2) Kurangi kecepatan mesin menjadi 60-70% dari normal, (3) Gunakan minyak pelumas lebih sering, (4) Jahit dengan tekanan kaki yang lebih kuat.',
                'recommendation' => 'Jadwalkan sharing session rutin antar operator senior dan junior. Dokumentasikan best practices dalam video tutorial singkat.',
                'impact_level' => 'sedang',
                'estimated_savings' => 1500000,
                'material_id' => 2,
                'supplier_id' => 4,
                'author_id' => 4,
                'status' => 'published',
                'view_count' => 52,
                'likes_count' => 18,
            ],
            [
                'title' => 'Internalisasi SOP Quality Control Kancing',
                'seci_type' => 'internalisasi',
                'category' => 'Kualitas',
                'problem_description' => 'Ditemukan 8% produk jadi dengan kancing cacat (retak atau lubang tidak presisi) yang lolos QC. Menyebabkan komplain customer dan retur.',
                'solution' => 'Training intensif QC kancing selama 2 minggu untuk semua staff finishing. Implementasi checklist visual dengan gambar contoh kancing OK vs NG. Setiap staff wajib cek 100% kancing sebelum pasang.',
                'recommendation' => 'Buat standar visual guide yang mudah dipahami. Lakukan random audit hasil QC. Berikan reward untuk staff dengan zero defect rate.',
                'impact_level' => 'tinggi',
                'estimated_savings' => 1800000,
                'material_id' => 4,
                'supplier_id' => 3,
                'author_id' => 2,
                'status' => 'published',
                'view_count' => 41,
                'likes_count' => 14,
            ],
        ];

        foreach ($lessons as $lessonData) {
            LessonLearned::create($lessonData);
        }

        // 5. Create Production Logs
        $this->command->info('Creating production logs...');
        for ($i = 0; $i < 30; $i++) {
            ProductionLog::create([
                'production_date' => Carbon::now()->subDays(rand(1, 30)),
                'product_type' => ['Kemeja', 'Celana', 'Jaket', 'Polo Shirt', 'Seragam'][rand(0, 4)],
                'quantity_produced' => rand(20, 100),
                'total_production_time' => rand(180, 480), // minutes
                'notes' => 'Produksi normal. ' . ['Tim A', 'Tim B', 'Tim C'][rand(0, 2)],
                'created_by' => [$staff1->id, $staff2->id][rand(0, 1)],
            ]);
        }

        // 6. Create Production Wastes
        $this->command->info('Creating production wastes...');
        $wasteCategories = ['kain', 'benang', 'aksesoris', 'rejected_product'];
        for ($i = 0; $i < 25; $i++) {
            $material = Material::inRandomOrder()->first();
            $wasteQty = rand(1, 20);
            
            ProductionWaste::create([
                'material_id' => $material->id,
                'waste_date' => Carbon::now()->subDays(rand(1, 30)),
                'waste_quantity' => $wasteQty,
                'waste_category' => $wasteCategories[rand(0, 3)],
                'is_preventable' => rand(0, 1) == 1,
                'cost_impact' => $wasteQty * $material->price_per_unit,
                'notes' => 'Sisa potongan pattern. ' . ['Minor', 'Moderate', 'Significant'][rand(0, 2)],
                'reported_by' => [$staff1->id, $staff2->id][rand(0, 1)],
            ]);
        }

        // 7. Create Knowledge Base entries
        $this->command->info('Creating knowledge base...');
        $knowledgeEntries = [
            [
                'title' => 'Panduan Lengkap Penggunaan Mesin Jahit High Speed',
                'category' => 'Tutorial',
                'content' => 'Tutorial lengkap cara mengoperasikan mesin jahit industrial high speed. Termasuk setting tension, perawatan rutin, troubleshooting masalah umum.',
                'tags' => json_encode(['mesin jahit', 'tutorial', 'high speed', 'maintenance']),
                'author_id' => 2,
            ],
            [
                'title' => 'Tabel Konversi Ukuran Pola Kemeja Pria',
                'category' => 'Referensi',
                'content' => 'Tabel lengkap ukuran kemeja pria dari S hingga XXXL. Termasuk lingkar dada, panjang lengan, lebar bahu, dan panjang badan. Format Excel tersedia.',
                'tags' => json_encode(['pola', 'ukuran', 'kemeja', 'referensi']),
                'author_id' => 2,
            ],
            [
                'title' => 'Best Practices: Negosiasi Harga dengan Supplier',
                'category' => 'Bisnis',
                'content' => 'Tips dan trik negosiasi harga bulk order dengan supplier kain. Mencakup timing order, minimum quantity, term of payment, dan building relationship.',
                'tags' => json_encode(['supplier', 'negosiasi', 'bisnis', 'procurement']),
                'author_id' => 1,
            ],
        ];

        foreach ($knowledgeEntries as $entry) {
            KnowledgeBase::create($entry);
        }

        // 8. Create Seasonal Predictions
        $this->command->info('Creating seasonal predictions...');
        $months = ['2024-06', '2024-07', '2024-08', '2024-09', '2024-10', '2024-11'];
        $products = ['Kemeja', 'Celana', 'Jaket', 'Seragam'];
        
        foreach ($months as $month) {
            foreach ($products as $product) {
                SeasonalPrediction::create([
                    'product_type' => $product,
                    'period_month' => $month,
                    'predicted_demand' => rand(100, 500),
                    'confidence_level' => ['rendah', 'sedang', 'tinggi'][rand(0, 2)],
                    'factors' => json_encode([
                        'historical_trend' => 'Berdasarkan data 6 bulan terakhir',
                        'seasonal_event' => $product == 'Seragam' ? 'Tahun ajaran baru' : 'Normal',
                        'market_trend' => 'Stable',
                    ]),
                    'created_by' => 1,
                ]);
            }
        }

        // 9. Create Pattern Repository
        $this->command->info('Creating pattern repository...');
        $patterns = [
            [
                'pattern_code' => 'PTN-KMJ-001',
                'pattern_name' => 'Kemeja Formal Lengan Panjang',
                'product_type' => 'Kemeja',
                'version' => 1,
                'size_variants' => json_encode(['S', 'M', 'L', 'XL', 'XXL']),
                'material_requirements' => json_encode([
                    ['material' => 'Kain Katun', 'quantity_per_unit' => 1.8, 'unit' => 'meter'],
                    ['material' => 'Benang', 'quantity_per_unit' => 0.05, 'unit' => 'cone'],
                    ['material' => 'Kancing', 'quantity_per_unit' => 7, 'unit' => 'pcs'],
                ]),
                'fabric_efficiency' => 87.5,
                'avg_cutting_time' => 12.5,
                'avg_sewing_time' => 45.0,
                'cutting_instructions' => 'Lipat kain menjadi 2 layer. Gunakan pattern marker untuk layout optimal. Potong dengan rotary cutter 60mm.',
                'sewing_instructions' => 'Mulai dari kerah, lanjut bahu, lengan, sisi badan, manset. Finishing dengan obras dan pasang kancing.',
                'quality_checkpoints' => json_encode([
                    'Kerah simetris',
                    'Lengan sama panjang',
                    'Jahitan lurus dan rapi',
                    'Kancing terpasang kuat'
                ]),
                'common_mistakes' => json_encode([
                    'Kerah tidak simetris - gunakan marking chalk',
                    'Lengan terbalik - beri tanda L/R sebelum jahit'
                ]),
                'status' => 'approved',
                'created_by' => 2,
                'approved_by' => 1,
                'usage_count' => 45,
                'last_used_at' => Carbon::now()->subDays(2),
            ],
            [
                'pattern_code' => 'PTN-CLN-001',
                'pattern_name' => 'Celana Kerja Standard',
                'product_type' => 'Celana',
                'version' => 2,
                'size_variants' => json_encode(['28', '30', '32', '34', '36', '38']),
                'material_requirements' => json_encode([
                    ['material' => 'Kain Drill', 'quantity_per_unit' => 1.5, 'unit' => 'meter'],
                    ['material' => 'Benang', 'quantity_per_unit' => 0.08, 'unit' => 'cone'],
                    ['material' => 'Resleting', 'quantity_per_unit' => 1, 'unit' => 'pcs'],
                ]),
                'fabric_efficiency' => 82.0,
                'avg_cutting_time' => 15.0,
                'avg_sewing_time' => 55.0,
                'cutting_instructions' => 'Pre-wash kain drill wajib dilakukan. Layout pattern dengan margin 3cm untuk sambungan.',
                'sewing_instructions' => 'Jahit saku depan, pasang resleting, jahit sisi luar dan dalam, finishing ban pinggang dan hem bawah.',
                'quality_checkpoints' => json_encode([
                    'Resleting berfungsi smooth',
                    'Kantong kuat',
                    'Jahitan ganda di stress point'
                ]),
                'common_mistakes' => json_encode([
                    'Lupa pre-wash - produk akan shrink',
                    'Resleting miring - gunakan guide foot'
                ]),
                'status' => 'approved',
                'created_by' => 2,
                'approved_by' => 1,
                'parent_pattern_id' => null,
                'usage_count' => 38,
                'last_used_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($patterns as $patternData) {
            PatternRepository::create($patternData);
        }

        // 10. Create Vendor Deliveries
        $this->command->info('Creating vendor deliveries...');
        $suppliers = Supplier::all();
        foreach ($suppliers as $supplier) {
            for ($i = 0; $i < rand(5, 10); $i++) {
                $onTime = rand(1, 10) > 2; // 80% on time
                $deliveryDate = Carbon::now()->subDays(rand(1, 60));
                
                VendorDelivery::create([
                    'supplier_id' => $supplier->id,
                    'material_type' => ['Kain', 'Benang', 'Aksesoris'][rand(0, 2)],
                    'order_date' => $deliveryDate->copy()->subDays(rand(5, 14)),
                    'expected_delivery_date' => $deliveryDate->copy()->subDays(rand(0, 3)),
                    'actual_delivery_date' => $deliveryDate,
                    'on_time_delivery' => $onTime,
                    'quantity_ordered' => rand(50, 500),
                    'quantity_received' => rand(48, 500),
                    'color_consistency' => ['sangat_baik', 'baik', 'cukup', 'kurang'][rand(0, 2)],
                    'material_quality' => ['premium', 'standard', 'ekonomis'][rand(0, 2)],
                    'defect_rate' => rand(0, 8),
                    'notes' => $onTime ? 'Pengiriman lancar.' : 'Terlambat ' . rand(1, 5) . ' hari. ' . ['Macet', 'Cuaca buruk', 'Keterlambatan produksi'][rand(0, 2)],
                    'recorded_by' => [$staff1->id, $staff2->id][rand(0, 1)],
                ]);
            }
            
            // Update supplier scores
            $supplier->updateScores();
        }

        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('');
        $this->command->info('Login credentials:');
        $this->command->info('Owner: owner@konveksi.com / password');
        $this->command->info('Manager: manager@konveksi.com / password');
        $this->command->info('Staff 1: ahmad@konveksi.com / password');
        $this->command->info('Staff 2: rina@konveksi.com / password');
    }
}
