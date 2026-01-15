"use client"

import { useState } from "react"
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Plus, Loader2, Star } from "lucide-react"
import axios from "axios"

interface AddVendorModalProps {
  onSuccess: () => void
}

export function AddVendorModal({ onSuccess }: AddVendorModalProps) {
  const [open, setOpen] = useState(false)
  const [loading, setLoading] = useState(false)
  const [formData, setFormData] = useState({
    nama_vendor: "",
    kategori_bahan: "",
    rating_kualitas: "8",
    rating_kecepatan: "8",
    indeks_keandalan: "8",
    kbv_insight: "",
  })

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setLoading(true)

    try {
      // Try backend API first
      try {
        const response = await axios.post("http://localhost:8000/api/vendors", {
          nama_vendor: formData.nama_vendor,
          kategori_bahan: formData.kategori_bahan,
          rating_kualitas: parseFloat(formData.rating_kualitas),
          rating_kecepatan: parseFloat(formData.rating_kecepatan),
          indeks_keandalan: parseFloat(formData.indeks_keandalan),
          kbv_insight: formData.kbv_insight,
        }, { timeout: 2000 })

        if (response.data.success) {
          resetAndClose()
          onSuccess()
          alert(response.data.message || "✓ Vendor berhasil ditambahkan!")
          return
        }
      } catch (apiError: any) {
        console.log("Backend tidak tersedia, menggunakan mode demo")
      }

      // FALLBACK: Demo mode - simpan ke localStorage
      const newVendor = {
        id: Date.now(),
        nama_vendor: formData.nama_vendor,
        kategori_bahan: formData.kategori_bahan,
        rating_kualitas: parseFloat(formData.rating_kualitas),
        rating_kecepatan: parseFloat(formData.rating_kecepatan),
        indeks_keandalan: parseFloat(formData.indeks_keandalan),
        kbv_insight: formData.kbv_insight,
        is_pilihan_utama: false,
        last_delivery: "Baru ditambahkan",
      }

      // Get existing vendors from localStorage
      const existingVendors = JSON.parse(localStorage.getItem("vendorData") || "[]")
      existingVendors.push(newVendor)
      localStorage.setItem("vendorData", JSON.stringify(existingVendors))

      resetAndClose()
      onSuccess()
      alert("✓ Vendor berhasil ditambahkan! (Mode Demo - Data tersimpan di browser)")

    } catch (error: any) {
      const errorMsg = error.response?.data?.message || "Gagal menambahkan vendor"
      alert(errorMsg)
      console.error("Error adding vendor:", error)
    } finally {
      setLoading(false)
    }
  }

  const resetAndClose = () => {
    setFormData({
      nama_vendor: "",
      kategori_bahan: "",
      rating_kualitas: "8",
      rating_kecepatan: "8",
      indeks_keandalan: "8",
      kbv_insight: "",
    })
    setOpen(false)
  }

  const handleChange = (field: string, value: string) => {
    setFormData(prev => ({ ...prev, [field]: value }))
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        <Button className="bg-purple-600 hover:bg-purple-700 text-white">
          <Plus className="w-4 h-4 mr-2" />
          Tambah Vendor
        </Button>
      </DialogTrigger>
      <DialogContent className="sm:max-w-[550px] max-h-[90vh] overflow-y-auto">
        <form onSubmit={handleSubmit}>
          <DialogHeader>
            <DialogTitle className="text-xl font-bold flex items-center gap-2">
              <Star className="w-5 h-5 text-purple-500" />
              Tambah Vendor Baru
            </DialogTitle>
            <DialogDescription>
              Simpan pengetahuan tentang mitra pemasok strategis untuk meningkatkan daya saing organisasi dalam pemilihan bahan baku terbaik.
            </DialogDescription>
          </DialogHeader>
          
          <div className="grid gap-4 py-4">
            {/* Nama Vendor */}
            <div className="grid gap-2">
              <label htmlFor="nama_vendor" className="text-sm font-medium">
                Nama Vendor <span className="text-red-500">*</span>
              </label>
              <Input
                id="nama_vendor"
                placeholder="Contoh: PT Primisima Textile"
                value={formData.nama_vendor}
                onChange={(e) => handleChange("nama_vendor", e.target.value)}
                required
                disabled={loading}
              />
            </div>

            {/* Kategori Bahan */}
            <div className="grid gap-2">
              <label htmlFor="kategori_bahan" className="text-sm font-medium">
                Kategori Bahan <span className="text-red-500">*</span>
              </label>
              <Input
                id="kategori_bahan"
                placeholder="Contoh: Kain Katun & Drill"
                value={formData.kategori_bahan}
                onChange={(e) => handleChange("kategori_bahan", e.target.value)}
                required
                disabled={loading}
              />
              <p className="text-xs text-muted-foreground">
                Spesialisasi bahan yang disediakan vendor
              </p>
            </div>

            {/* Rating Kualitas */}
            <div className="grid gap-2">
              <label htmlFor="rating_kualitas" className="text-sm font-medium">
                Rating Kualitas: {formData.rating_kualitas}/10
              </label>
              <div className="flex items-center gap-3">
                <input
                  id="rating_kualitas"
                  type="range"
                  min="1"
                  max="10"
                  step="0.1"
                  value={formData.rating_kualitas}
                  onChange={(e) => handleChange("rating_kualitas", e.target.value)}
                  disabled={loading}
                  className="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-500"
                />
                <span className="text-sm font-semibold w-12 text-right">{formData.rating_kualitas}</span>
              </div>
              <div className="h-2 bg-background rounded-full overflow-hidden">
                <div 
                  className="h-full bg-blue-500 rounded-full transition-all" 
                  style={{ width: `${parseFloat(formData.rating_kualitas) * 10}%` }} 
                />
              </div>
            </div>

            {/* Rating Kecepatan Pengiriman */}
            <div className="grid gap-2">
              <label htmlFor="rating_kecepatan" className="text-sm font-medium">
                Rating Kecepatan Pengiriman: {formData.rating_kecepatan}/10
              </label>
              <div className="flex items-center gap-3">
                <input
                  id="rating_kecepatan"
                  type="range"
                  min="1"
                  max="10"
                  step="0.1"
                  value={formData.rating_kecepatan}
                  onChange={(e) => handleChange("rating_kecepatan", e.target.value)}
                  disabled={loading}
                  className="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-cyan-500"
                />
                <span className="text-sm font-semibold w-12 text-right">{formData.rating_kecepatan}</span>
              </div>
              <div className="h-2 bg-background rounded-full overflow-hidden">
                <div 
                  className="h-full bg-cyan-500 rounded-full transition-all" 
                  style={{ width: `${parseFloat(formData.rating_kecepatan) * 10}%` }} 
                />
              </div>
            </div>

            {/* Indeks Keandalan */}
            <div className="grid gap-2">
              <label htmlFor="indeks_keandalan" className="text-sm font-medium">
                Indeks Keandalan: {formData.indeks_keandalan}/10
              </label>
              <div className="flex items-center gap-3">
                <input
                  id="indeks_keandalan"
                  type="range"
                  min="1"
                  max="10"
                  step="0.1"
                  value={formData.indeks_keandalan}
                  onChange={(e) => handleChange("indeks_keandalan", e.target.value)}
                  disabled={loading}
                  className="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-green-500"
                />
                <span className="text-sm font-semibold w-12 text-right">{formData.indeks_keandalan}</span>
              </div>
              <div className="h-2 bg-background rounded-full overflow-hidden">
                <div 
                  className="h-full bg-green-500 rounded-full transition-all" 
                  style={{ width: `${parseFloat(formData.indeks_keandalan) * 10}%` }} 
                />
              </div>
            </div>

            {/* KBV Insight (Tacit Knowledge) */}
            <div className="grid gap-2">
              <label htmlFor="kbv_insight" className="text-sm font-medium flex items-center gap-2">
                💡 KBV Insight (Penting) <span className="text-red-500">*</span>
              </label>
              <Textarea
                id="kbv_insight"
                placeholder="Masukkan pengetahuan tacit tentang vendor, misal: 'Respon cepat untuk order mendadak' atau 'Konsistensi warna terbaik untuk order >100m'"
                rows={4}
                value={formData.kbv_insight}
                onChange={(e) => handleChange("kbv_insight", e.target.value)}
                required
                disabled={loading}
              />
              <p className="text-xs text-muted-foreground">
                🔑 <strong>Knowledge-Based View:</strong> Dokumentasikan pengalaman dan pengetahuan tacit tentang vendor yang tidak tercatat di dokumen formal
              </p>
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
            <Button type="submit" disabled={loading} className="bg-purple-600 hover:bg-purple-700">
              {loading ? (
                <>
                  <Loader2 className="mr-2 h-4 w-4 animate-spin" />
                  Menyimpan...
                </>
              ) : (
                <>
                  <Plus className="mr-2 h-4 w-4" />
                  Simpan Vendor
                </>
              )}
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  )
}
