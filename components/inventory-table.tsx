"use client"

import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Info } from "lucide-react"
import { useState, useEffect } from "react"
import { KnowledgePanel } from "./knowledge-panel"
import { AddInventoryModal } from "./add-inventory-modal"
import axios from "axios"

const inventoryData = [
  {
    id: 1,
    name: "Kain Katun Combed 30s",
    category: "Bahan Utama",
    stock: 450,
    threshold: 200,
    unit: "meter",
    knowledge: { 
      explicit: "Simpan di ruangan ber-AC, hindari kelembaban tinggi", 
      tacit: "Menyusut 3-5% setelah dicuci pertama kali. Tambahkan toleransi saat memotong pola" 
    },
  },
  {
    id: 2,
    name: "Kain Drill Twill",
    category: "Bahan Utama",
    stock: 280,
    threshold: 150,
    unit: "meter",
    knowledge: { 
      explicit: "Material tahan lama untuk seragam kerja dan celana", 
      tacit: "Supplier A memberikan konsistensi warna lebih baik untuk order >100m" 
    },
  },
  {
    id: 3,
    name: "Benang Jahit Polyester",
    category: "Bahan Pendukung",
    stock: 890,
    threshold: 300,
    unit: "cone",
    knowledge: { 
      explicit: "Atur berdasarkan kode warna, simpan dalam box kedap udara", 
      tacit: "Benang lokal merk X sama kuatnya dengan import tapi 40% lebih murah" 
    },
  },
  {
    id: 4,
    name: "Kancing Plastik Variasi",
    category: "Aksesoris",
    stock: 45,
    threshold: 100,
    unit: "gross",
    knowledge: { 
      explicit: "Pisahkan berdasarkan ukuran dan warna", 
      tacit: "Stok ini sering habis mendadak saat musim seragam sekolah (Juni-Juli)" 
    },
  },
  {
    id: 5,
    name: "Kain Polyester Premium",
    category: "Bahan Utama",
    stock: 95,
    threshold: 120,
    unit: "meter",
    knowledge: { 
      explicit: "Material ringan untuk kemeja dan baju olahraga", 
      tacit: "Hindari supplier B - warna mudah luntur setelah 3x cuci" 
    },
  },
]

interface KnowledgePanelProps {
  item: (typeof inventoryData)[0]
  onClose: () => void
}

interface InventoryItem {
  id: number
  nama_bahan: string
  kategori: string
  stok: number
  satuan: string
  status: string
  threshold_min?: number
  harga_per_unit?: number
  explicit_knowledge?: string
  tacit_knowledge?: string
  supplier?: string
  last_updated?: string
}

export function InventoryTable() {
  const [selectedItem, setSelectedItem] = useState<(typeof inventoryData)[0] | null>(null)
  const [inventoryItems, setInventoryItems] = useState<InventoryItem[]>([])
  const [loading, setLoading] = useState(true)
  const [error, setError] = useState<string | null>(null)

  const fetchInventory = async () => {
    try {
      setLoading(true)
      setError(null)
      
      // Try backend API first
      try {
        const response = await axios.get("http://localhost:8000/api/inventaris", { timeout: 2000 })
        
        if (response.data.success) {
          setInventoryItems(response.data.data)
          setLoading(false)
          return
        }
      } catch (apiError) {
        console.log("Backend tidak tersedia, menggunakan localStorage")
      }

      // FALLBACK: Load from localStorage (demo mode)
      const localItems = JSON.parse(localStorage.getItem("inventoryItems") || "[]")
      
      // If localStorage is empty, use dummy data
      if (localItems.length === 0) {
        const dummyData: InventoryItem[] = [
          {
            id: 1,
            nama_bahan: "Kain Katun Combed 30s",
            kategori: "Bahan Utama",
            stok: 450,
            satuan: "meter",
            status: "Optimal",
            threshold_min: 200,
            harga_per_unit: 45000,
            explicit_knowledge: "Simpan di ruangan ber-AC, hindari kelembaban tinggi",
            tacit_knowledge: "Menyusut 3-5% setelah dicuci pertama kali",
            supplier: "Supplier A",
            last_updated: "2 hari lalu"
          },
          {
            id: 2,
            nama_bahan: "Kain Drill Twill",
            kategori: "Bahan Utama",
            stok: 280,
            satuan: "meter",
            status: "Optimal",
            threshold_min: 150,
            harga_per_unit: 52000,
            explicit_knowledge: "Material tahan lama untuk seragam kerja",
            tacit_knowledge: "Supplier A memberikan konsistensi warna lebih baik",
            supplier: "Supplier A",
            last_updated: "1 minggu lalu"
          },
          {
            id: 3,
            nama_bahan: "Benang Jahit Polyester",
            kategori: "Bahan Pendukung",
            stok: 890,
            satuan: "cone",
            status: "Optimal",
            threshold_min: 300,
            harga_per_unit: 8500,
            explicit_knowledge: "Atur berdasarkan kode warna",
            tacit_knowledge: "Benang lokal merk X sama kuatnya tapi 40% lebih murah",
            supplier: "Supplier B",
            last_updated: "3 hari lalu"
          },
          {
            id: 4,
            nama_bahan: "Kancing Plastik Variasi",
            kategori: "Aksesoris",
            stok: 45,
            satuan: "gross",
            status: "Rendah",
            threshold_min: 100,
            harga_per_unit: 15000,
            explicit_knowledge: "Pisahkan berdasarkan ukuran dan warna",
            tacit_knowledge: "Stok habis mendadak saat musim seragam sekolah",
            supplier: "Supplier C",
            last_updated: "5 hari lalu"
          },
          {
            id: 5,
            nama_bahan: "Kain Polyester Premium",
            kategori: "Bahan Utama",
            stok: 95,
            satuan: "meter",
            status: "Rendah",
            threshold_min: 120,
            harga_per_unit: 48000,
            explicit_knowledge: "Material ringan untuk kemeja",
            tacit_knowledge: "Hindari supplier B - warna mudah luntur",
            supplier: "Supplier A",
            last_updated: "1 hari lalu"
          },
        ]
        localStorage.setItem("inventoryItems", JSON.stringify(dummyData))
        setInventoryItems(dummyData)
      } else {
        setInventoryItems(localItems)
      }
      
    } catch (err: any) {
      console.error("Error fetching inventory:", err)
      setError("Gagal memuat data inventaris")
      setInventoryItems([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchInventory()
  }, [])

  const handleInventoryAdded = () => {
    fetchInventory() // Refresh data after adding new item
  }

  return (
    <div className="p-8">
      {/* Header with Add Button */}
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-card-foreground">Manajemen Inventaris</h2>
          <p className="text-sm text-muted-foreground mt-1">
            Total Barang: {inventoryItems.length} | {inventoryItems.filter(i => i.status === 'Rendah' || i.status === 'Habis').length} membutuhkan perhatian
          </p>
        </div>
        <AddInventoryModal onSuccess={handleInventoryAdded} />
      </div>

      {/* Error State */}
      {error && (
        <div className="mb-4 p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
          <p className="text-sm text-red-400">{error}</p>
          <Button 
            onClick={fetchInventory} 
            variant="outline" 
            size="sm" 
            className="mt-2"
          >
            Coba Lagi
          </Button>
        </div>
      )}

      {/* Loading State */}
      {loading && (
        <Card className="bg-card border-border p-8 text-center">
          <div className="flex items-center justify-center gap-2">
            <div className="w-4 h-4 border-2 border-accent border-t-transparent rounded-full animate-spin" />
            <span className="text-muted-foreground">Memuat data inventaris...</span>
          </div>
        </Card>
      )}

      {/* Table */}
      {!loading && (
        <Card className="bg-card border-border overflow-hidden">
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="border-b border-border bg-background">
                  <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Nama Bahan</th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Kategori</th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Stok</th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Status</th>
                  <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Pengetahuan</th>
                </tr>
              </thead>
              <tbody>
                {inventoryItems.length === 0 ? (
                  <tr>
                    <td colSpan={5} className="px-6 py-12 text-center">
                      <div className="text-muted-foreground">
                        <p className="text-lg font-medium mb-2">Belum ada data inventaris</p>
                        <p className="text-sm">Klik tombol "Tambah Barang" untuk menambahkan bahan baku</p>
                      </div>
                    </td>
                  </tr>
                ) : (
                  inventoryItems.map((item) => (
                    <tr key={item.id} className="border-b border-border hover:bg-background/50 transition-colors">
                      <td className="px-6 py-4 text-sm font-medium text-card-foreground">{item.nama_bahan}</td>
                      <td className="px-6 py-4 text-sm text-muted-foreground">{item.kategori}</td>
                      <td className="px-6 py-4">
                        <div className="flex items-center gap-2">
                          <div className="flex-1 h-2 bg-background rounded-full overflow-hidden">
                            <div
                              className="h-full bg-accent rounded-full"
                              style={{ width: `${Math.min((item.stok / 1000) * 100, 100)}%` }}
                            />
                          </div>
                          <span className="text-sm font-medium text-card-foreground">{item.stok} {item.satuan}</span>
                        </div>
                      </td>
                      <td className="px-6 py-4">
                        <span
                          className={`text-xs font-semibold px-3 py-1 rounded-full ${
                            item.status === 'Optimal' ? "bg-green-500/20 text-green-400" :
                            item.status === 'Cukup' ? "bg-blue-500/20 text-blue-400" :
                            item.status === 'Rendah' ? "bg-amber-500/20 text-amber-400" :
                            "bg-red-500/20 text-red-400"
                          }`}
                        >
                          {item.status}
                        </span>
                      </td>
                      <td className="px-6 py-4">
                        <Button
                          variant="ghost"
                          size="icon"
                          onClick={() => setSelectedItem({
                            id: item.id,
                            name: item.nama_bahan,
                            category: item.kategori,
                            stock: item.stok,
                            threshold: item.threshold_min || 0,
                            unit: item.satuan,
                            knowledge: {
                              explicit: item.explicit_knowledge || "",
                              tacit: item.tacit_knowledge || ""
                            }
                          })}
                          className="text-muted-foreground hover:text-foreground"
                        >
                          <Info className="w-5 h-5" />
                        </Button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </Card>
      )}

      {selectedItem && <KnowledgePanel item={selectedItem} onClose={() => setSelectedItem(null)} />}
    </div>
  )
}
