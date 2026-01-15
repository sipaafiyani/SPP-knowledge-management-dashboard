"use client"

import { Card } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Star, TrendingUp } from "lucide-react"
import { useState, useEffect } from "react"
import { AddVendorModal } from "./add-vendor-modal"
import axios from "axios"

const vendors = [
  {
    id: 1,
    name: "PT Primisima Textile",
    quality: 9.2,
    speed: 8.8,
    reliability: 9.0,
    recommended: true,
    lastDelivery: "2 hari lalu",
    specialty: "Kain Katun Premium & Drill",
    kbvNote: "Konsistensi warna terbaik untuk order >100m",
  },
  {
    id: 2,
    name: "Toko Kain Maju Jaya",
    quality: 8.1,
    speed: 9.5,
    reliability: 8.3,
    recommended: true,
    lastDelivery: "1 hari lalu",
    specialty: "Kain Polyester & Aksesoris",
    kbvNote: "Respon cepat untuk order mendadak",
  },
  {
    id: 3,
    name: "CV Tekstil Nusantara",
    quality: 7.8,
    speed: 7.2,
    reliability: 7.5,
    recommended: false,
    lastDelivery: "5 hari lalu",
    specialty: "Benang & Kancing",
    kbvNote: "Harga kompetitif tapi kadang warna tidak match",
  },
  {
    id: 4,
    name: "UD Sumber Kain",
    quality: 8.9,
    speed: 8.0,
    reliability: 8.6,
    recommended: false,
    lastDelivery: "3 hari lalu",
    specialty: "Kain Lokal Berkualitas",
    kbvNote: "Alternatif import dengan harga 35% lebih murah",
  },
]

interface VendorData {
  id: number
  nama_vendor: string
  kategori_bahan: string
  rating_kualitas: number
  rating_kecepatan: number
  indeks_keandalan: number
  kbv_insight: string
  is_pilihan_utama: boolean
  last_delivery: string
}

export function VendorIntelligence() {
  const [vendorList, setVendorList] = useState<VendorData[]>([])
  const [loading, setLoading] = useState(true)

  const fetchVendors = async () => {
    try {
      setLoading(true)
      
      // Try backend API first
      try {
        const response = await axios.get("http://localhost:8000/api/vendors", { timeout: 2000 })
        
        if (response.data.success) {
          setVendorList(response.data.data)
          setLoading(false)
          return
        }
      } catch (apiError) {
        console.log("Backend tidak tersedia, menggunakan localStorage")
      }

      // FALLBACK: Load from localStorage (demo mode)
      const localVendors = JSON.parse(localStorage.getItem("vendorData") || "[]")
      
      // If localStorage is empty, use dummy data
      if (localVendors.length === 0) {
        const dummyData: VendorData[] = vendors.map(v => ({
          id: v.id,
          nama_vendor: v.name,
          kategori_bahan: v.specialty,
          rating_kualitas: v.quality,
          rating_kecepatan: v.speed,
          indeks_keandalan: v.reliability,
          kbv_insight: v.kbvNote,
          is_pilihan_utama: v.recommended,
          last_delivery: v.lastDelivery,
        }))
        localStorage.setItem("vendorData", JSON.stringify(dummyData))
        setVendorList(dummyData)
      } else {
        setVendorList(localVendors)
      }
      
    } catch (err: any) {
      console.error("Error fetching vendors:", err)
      setVendorList([])
    } finally {
      setLoading(false)
    }
  }

  useEffect(() => {
    fetchVendors()
  }, [])

  const handleVendorAdded = () => {
    fetchVendors() // Refresh data after adding new vendor
  }

  return (
    <div className="p-8">
      {/* Header with Add Button */}
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h2 className="text-2xl font-bold text-card-foreground">Intelijen Vendor</h2>
          <p className="text-sm text-muted-foreground mt-1">
            Knowledge-Based View untuk Mitra Pemasok Strategis | Total Vendor: {vendorList.length}
          </p>
        </div>
        <AddVendorModal onSuccess={handleVendorAdded} />
      </div>

      {/* Loading State */}
      {loading && (
        <div className="flex items-center justify-center py-12">
          <div className="flex items-center gap-2">
            <div className="w-4 h-4 border-2 border-purple-500 border-t-transparent rounded-full animate-spin" />
            <span className="text-muted-foreground">Memuat data vendor...</span>
          </div>
        </div>
      )}

      {/* Vendor Grid */}
      {!loading && (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          {vendorList.length === 0 ? (
            <div className="col-span-2 text-center py-12">
              <p className="text-lg font-medium text-muted-foreground mb-2">Belum ada data vendor</p>
              <p className="text-sm text-muted-foreground">Klik tombol "Tambah Vendor" untuk menambahkan mitra pemasok strategis</p>
            </div>
          ) : (
            vendorList.map((vendor) => {
              const overallScore = (vendor.rating_kualitas + vendor.rating_kecepatan + vendor.indeks_keandalan) / 3
              return (
                <Card key={vendor.id} className="p-6 bg-card border-border">
                  <div className="flex items-start justify-between mb-4">
                    <div>
                      <h3 className="font-semibold text-card-foreground">{vendor.nama_vendor}</h3>
                      <p className="text-xs text-blue-400 mt-1">{vendor.kategori_bahan}</p>
                      <p className="text-xs text-muted-foreground mt-1">Pengiriman terakhir: {vendor.last_delivery}</p>
                    </div>
                    {vendor.is_pilihan_utama && (
                      <Badge className="bg-accent text-accent-foreground flex items-center gap-1">
                        <TrendingUp className="w-3 h-3" />
                        Pilihan Utama
                      </Badge>
                    )}
                  </div>

                  <div className="space-y-3 mb-4">
                    <div>
                      <div className="flex items-center justify-between mb-1">
                        <span className="text-xs text-muted-foreground">Kualitas</span>
                        <span className="text-sm font-semibold text-card-foreground">{vendor.rating_kualitas}/10</span>
                      </div>
                      <div className="h-2 bg-background rounded-full overflow-hidden">
                        <div className="h-full bg-blue-500 rounded-full" style={{ width: `${vendor.rating_kualitas * 10}%` }} />
                      </div>
                    </div>

                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-muted-foreground">Kecepatan Pengiriman</span>
                    <span className="text-sm font-semibold text-card-foreground">{vendor.rating_kecepatan}/10</span>
                  </div>
                  <div className="h-2 bg-background rounded-full overflow-hidden">
                    <div className="h-full bg-cyan-500 rounded-full" style={{ width: `${vendor.rating_kecepatan * 10}%` }} />
                  </div>
                </div>

                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-muted-foreground">Indeks Keandalan</span>
                    <span className="text-sm font-semibold text-card-foreground">{vendor.indeks_keandalan}/10</span>
                  </div>
                  <div className="h-2 bg-background rounded-full overflow-hidden">
                    <div
                      className="h-full bg-green-500 rounded-full"
                      style={{ width: `${vendor.indeks_keandalan * 10}%` }}
                    />
                  </div>
                </div>
              </div>

              <div className="pt-4 border-t border-border">
                <div className="flex items-center justify-between mb-2">
                  <div className="flex items-center gap-1">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-4 h-4 ${
                          i < Math.round(overallScore / 2) ? "fill-yellow-500 text-yellow-500" : "text-muted-foreground"
                        }`}
                      />
                    ))}
                  </div>
                  <span className="text-sm font-semibold text-card-foreground">{overallScore.toFixed(1)}</span>
                </div>
                <p className="text-xs text-muted-foreground italic mt-2">
                  💡 KBV Insight: {vendor.kbv_insight}
                </p>
              </div>
            </Card>
          )
        }))}
      </div>
      )}
    </div>
  )
}

