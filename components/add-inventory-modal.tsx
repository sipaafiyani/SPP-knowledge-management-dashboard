"use client"

import { useState } from "react"
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Plus, Loader2 } from "lucide-react"
import axios from "axios"

interface AddInventoryModalProps {
  onSuccess: () => void
}

export function AddInventoryModal({ onSuccess }: AddInventoryModalProps) {
  const [open, setOpen] = useState(false)
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    nama_bahan: "",
    kategori: "Bahan Utama",
    stok: "",
    satuan: "",
    harga_per_unit: "",
    threshold_min: "",
    explicit_knowledge: "",
  })

  const categories = ["Bahan Utama", "Bahan Pendukung", "Aksesoris", "Alat Produksi"]

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)

    try {
      // Try backend API first
      try {
        const response = await axios.post("http://localhost:8000/api/inventaris", {
          nama_bahan: formData.nama_bahan,
          kategori: formData.kategori,
          stok: parseFloat(formData.stok),
          satuan: formData.satuan,
          harga_per_unit: formData.harga_per_unit ? parseFloat(formData.harga_per_unit) : 0,
          threshold_min: formData.threshold_min ? parseFloat(formData.threshold_min) : null,
          explicit_knowledge: formData.explicit_knowledge || null,
        }, { timeout: 2000 })

        if (response.data.success) {
          // Reset form
          setFormData({
            nama_bahan: "",
            kategori: "Bahan Utama",
            stok: "",
            satuan: "",
            harga_per_unit: "",
            threshold_min: "",
            explicit_knowledge: "",
          })
          setOpen(false)
          onSuccess() // Trigger refresh
          alert(response.data.message || "✓ Barang berhasil ditambahkan!")
          return
        }
      } catch (apiError: any) {
        console.log("Backend tidak tersedia, menggunakan mode demo")
      }

      // FALLBACK: Demo mode - simpan ke localStorage
      const newItem = {
        id: Date.now(),
        nama_bahan: formData.nama_bahan,
        kategori: formData.kategori,
        stok: parseFloat(formData.stok),
        satuan: formData.satuan,
        status: getStatus(parseFloat(formData.stok), parseFloat(formData.threshold_min || "0")),
        threshold_min: parseFloat(formData.threshold_min || "0"),
        harga_per_unit: parseFloat(formData.harga_per_unit || "0"),
        explicit_knowledge: formData.explicit_knowledge,
        tacit_knowledge: "",
        supplier: "-",
        last_updated: "Baru saja",
      }

      // Get existing items from localStorage
      const existingItems = JSON.parse(localStorage.getItem("inventoryItems") || "[]")
      existingItems.push(newItem)
      localStorage.setItem("inventoryItems", JSON.stringify(existingItems))

      // Reset form
      setFormData({
        nama_bahan: "",
        kategori: "Bahan Utama",
        stok: "",
        satuan: "",
        harga_per_unit: "",
        threshold_min: "",
        explicit_knowledge: "",
      })
      setOpen(false)
      onSuccess() // Trigger refresh
      alert("✓ Barang berhasil ditambahkan! (Mode Demo - Data tersimpan di browser)")

    } catch (error: any) {
      const errorMsg = error.response?.data?.message || "Gagal menambahkan barang"
      alert(errorMsg)
      console.error("Error adding inventory:", error)
    } finally {
      setLoading(false)
    }
  }

  const getStatus = (stok: number, threshold: number) => {
    if (stok <= 0) return "Habis"
    if (stok <= threshold) return "Rendah"
    if (stok <= threshold * 2) return "Cukup"
    return "Optimal"
  }

  const handleChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }))
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button className="bg-blue-600 hover:bg-blue-700 text-white">
          <Plus className="w-4 h-4 mr-2" />
          Tambah Barang
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[500px]">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle className="text-xl font-bold">Tambah Barang Inventaris</DialogTitle>
            <DialogDescription>
              Masukkan data bahan baku baru ke dalam sistem manajemen pengetahuan inventaris.
            </DialogDescription>
          </DialogHeader>
          
          <div className="grid gap-4 py-4">
            {/* Nama Bahan */}
            <div className="grid gap-2">
              <label htmlFor="nama_bahan" className="text-sm font-medium">
                Nama Bahan <span className="text-red-500">*</span>
              </label>
              <Input
                id="nama_bahan"
                placeholder="Contoh: Kulit Sintetis Premium"
                value={formData.nama_bahan}
                onChange={(e) => handleChange("nama_bahan", e.target.value)}
                required
                disabled={loading}
              />
            </div>

            {/* Kategori */}
            <div className="grid gap-2">
              <label htmlFor="kategori" className="text-sm font-medium">
                Kategori <span className="text-red-500">*</span>
              </label>
              <select
                id="kategori"
                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                value={formData.kategori}
                onChange={(e) => handleChange("kategori", e.target.value)}
                required
                disabled={loading}
              >
                {categories.map(cat => (
                  <option key={cat} value={cat}>{cat}</option>
                ))}
              </select>
            </div>

            {/* Stok & Satuan */}
            <div className="grid grid-cols-2 gap-4">
              <div className="grid gap-2">
                <label htmlFor="stok" className="text-sm font-medium">
                  Jumlah Stok <span className="text-red-500">*</span>
                </label>
                <Input
                  id="stok"
                  type="number"
                  step="0.01"
                  placeholder="100"
                  value={formData.stok}
                  onChange={(e) => handleChange("stok", e.target.value)}
                  required
                  disabled={loading}
                />
              </div>
              <div className="grid gap-2">
                <label htmlFor="satuan" className="text-sm font-medium">
                  Satuan <span className="text-red-500">*</span>
                </label>
                <Input
                  id="satuan"
                  placeholder="meter"
                  value={formData.satuan}
                  onChange={(e) => handleChange("satuan", e.target.value)}
                  required
                  disabled={loading}
                />
              </div>
            </div>

            {/* Harga & Threshold */}
            <div className="grid grid-cols-2 gap-4">
              <div className="grid gap-2">
                <label htmlFor="harga" className="text-sm font-medium">
                  Harga/Unit (Rp)
                </label>
                <Input
                  id="harga"
                  type="number"
                  step="0.01"
                  placeholder="50000"
                  value={formData.harga_per_unit}
                  onChange={(e) => handleChange("harga_per_unit", e.target.value)}
                  disabled={loading}
                />
              </div>
              <div className="grid gap-2">
                <label htmlFor="threshold" className="text-sm font-medium">
                  Batas Minimal
                </label>
                <Input
                  id="threshold"
                  type="number"
                  step="0.01"
                  placeholder="20"
                  value={formData.threshold_min}
                  onChange={(e) => handleChange("threshold_min", e.target.value)}
                  disabled={loading}
                />
              </div>
            </div>

            {/* Pengetahuan Eksplisit */}
            <div className="grid gap-2">
              <label htmlFor="knowledge" className="text-sm font-medium">
                Catatan Pengetahuan (Opsional)
              </label>
              <Textarea
                id="knowledge"
                placeholder="Dokumentasi cara penyimpanan, tips penggunaan, atau informasi penting lainnya..."
                rows={3}
                value={formData.explicit_knowledge}
                onChange={(e) => handleChange("explicit_knowledge", e.target.value)}
                disabled={loading}
              />
            </div>
          </div>

          <DialogFooter>
            <Button
              type="button"
              variant="outline"
              onClick={() => setOpen(false)}
              disabled={loading}
            >
              Batal
            </Button>
            <Button type="submit" disabled={loading} className="bg-blue-600 hover:bg-blue-700">
              {loading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Menyimpan...
                </>
              ) : (
                <>
                  <Plus className="mr-2 h-4 w-4" />
                  Simpan Barang
                </>
              )}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
