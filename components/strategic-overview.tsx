"use client"

import { Card } from "@/components/ui/card"
import { TrendingUp, AlertCircle, CheckCircle, Activity } from "lucide-react"
import { Badge } from "@/components/ui/badge"

const metrics = [
  {
    title: "Total Nilai Stok Bahan",
    value: "Rp 245.000.000",
    change: "+12.5%",
    positive: true,
    icon: TrendingUp,
    description: "Nilai total bahan baku & pendukung",
  },
  {
    title: "Efisiensi Bahan (Lean KM)",
    value: "87%",
    change: "Waste 13% (Target <10%)",
    positive: false,
    icon: AlertCircle,
    description: "Persentase utilisasi bahan vs sisa potongan",
  },
  {
    title: "Indeks Keandalan Supplier",
    value: "8.7/10",
    change: "Ketepatan waktu & kualitas warna",
    positive: true,
    icon: CheckCircle,
    description: "Berdasarkan on-time delivery dan konsistensi",
  },
  {
    title: "Skor Kesehatan Pengetahuan",
    value: "76%",
    change: "SOP & QC diperbarui 2 hari lalu",
    positive: true,
    icon: Activity,
    description: "Tingkat kelengkapan dokumentasi tacit to explicit",
  },
]

export function StrategicOverview() {
  return (
    <div className="p-8">
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {metrics.map((metric) => {
          const Icon = metric.icon
          return (
            <Card key={metric.title} className="p-6 bg-card border-border">
              <div className="flex items-start justify-between mb-4">
                <div className="p-2 rounded-lg bg-primary/10">
                  <Icon className="w-6 h-6 text-primary" />
                </div>
                <Badge variant={metric.positive ? "default" : "destructive"}>{metric.change}</Badge>
              </div>
              <h3 className="text-sm text-muted-foreground mb-1">{metric.title}</h3>
              <p className="text-3xl font-bold text-card-foreground">{metric.value}</p>
              {metric.description && (
                <p className="text-xs text-muted-foreground mt-2">{metric.description}</p>
              )}
            </Card>
          )
        })}
      </div>

      {/* Recently Updated Knowledge */}
      <Card className="p-6 bg-card border-border">
        <h3 className="text-lg font-semibold text-card-foreground mb-4">Pembaruan Pengetahuan Terbaru (Tacit to Explicit)</h3>
        <div className="space-y-4">
          {[
            { title: "SOP: Teknik Jahit Obras untuk Kaos Rajut", updated: "2 jam lalu", type: "Sosialisasi" },
            { title: "Lesson: Kain Katun Supplier A menyusut 5% setelah cuci", updated: "1 hari lalu", type: "Eksternalisasi" },
            { title: "Best Practice: Pola potong efisien untuk seragam sekolah", updated: "3 hari lalu", type: "Kombinasi" },
          ].map((item, i) => (
            <div
              key={i}
              className="flex items-center justify-between p-3 bg-background rounded-lg border border-border"
            >
              <div>
                <p className="font-medium text-card-foreground">{item.title}</p>
                <p className="text-xs text-muted-foreground mt-1">{item.updated} • SECI: {item.type}</p>
              </div>
              <Badge variant="outline">Diperbarui</Badge>
            </div>
          ))}
        </div>
      </Card>
    </div>
  )
}
