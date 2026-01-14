"use client"

import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Info } from "lucide-react"
import { useState } from "react"
import { KnowledgePanel } from "./knowledge-panel"

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

export function InventoryTable() {
  const [selectedItem, setSelectedItem] = useState<(typeof inventoryData)[0] | null>(null)

  return (
    <div className="p-8">
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
              {inventoryData.map((item) => (
                <tr key={item.id} className="border-b border-border hover:bg-background/50 transition-colors">
                  <td className="px-6 py-4 text-sm font-medium text-card-foreground">{item.name}</td>
                  <td className="px-6 py-4 text-sm text-muted-foreground">{item.category}</td>
                  <td className="px-6 py-4">
                    <div className="flex items-center gap-2">
                      <div className="flex-1 h-2 bg-background rounded-full overflow-hidden">
                        <div
                          className="h-full bg-accent rounded-full"
                          style={{ width: `${Math.min((item.stock / 1000) * 100, 100)}%` }}
                        />
                      </div>
                      <span className="text-sm font-medium text-card-foreground">{item.stock} {item.unit}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span
                      className={`text-xs font-semibold px-3 py-1 rounded-full ${
                        item.stock > item.threshold ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"
                      }`}
                    >
                      {item.stock > item.threshold ? "Optimal" : "Stok Rendah"}
                    </span>
                  </td>
                  <td className="px-6 py-4">
                    <Button
                      variant="ghost"
                      size="icon"
                      onClick={() => setSelectedItem(item)}
                      className="text-muted-foreground hover:text-foreground"
                    >
                      <Info className="w-5 h-5" />
                    </Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </Card>

      {selectedItem && <KnowledgePanel item={selectedItem} onClose={() => setSelectedItem(null)} />}
    </div>
  )
}
