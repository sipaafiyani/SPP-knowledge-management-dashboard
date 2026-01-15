"use client"

import { Card } from "@/components/ui/card"
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer, Cell } from "recharts"

const stockTrendData = [
  { month: "Jan", value: 180000000, demand: "Rendah" },
  { month: "Feb", value: 195000000, demand: "Rendah" },
  { month: "Mar", value: 210000000, demand: "Mulai Naik" },
  { month: "Apr", value: 235000000, demand: "Tinggi" },
  { month: "Mei", value: 265000000, demand: "Peak (Lebaran)" },
  { month: "Jun", value: 280000000, demand: "Peak (Seragam)" },
]

const categoryData = [
  { name: "Kain Katun", value: 95000000, color: "#3b82f6" },
  { name: "Kain Polyester", value: 72000000, color: "#a855f7" },
  { name: "Benang & Aksesoris", value: 48000000, color: "#f59e0b" },
  { name: "Kain Drill", value: 40000000, color: "#10b981" },
]

export function InventoryAnalytics() {
  return (
    <div className="p-8">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Stock Trend */}
        <Card className="p-6 bg-card border-border">
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Tren Nilai Stok</h3>
          <ResponsiveContainer width="100%" height={300}>
            <LineChart data={stockTrendData}>
              <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--color-border))" />
              <XAxis stroke="hsl(var(--color-muted-foreground))" />
              <YAxis stroke="hsl(var(--color-muted-foreground))" />
              <Tooltip
                contentStyle={{
                  backgroundColor: "hsl(var(--color-card))",
                  border: "1px solid hsl(var(--color-border))",
                }}
              />
              <Line
                type="monotone"
                dataKey="value"
                stroke="hsl(var(--color-accent))"
                strokeWidth={2}
                dot={{ fill: "hsl(var(--color-accent))" }}
              />
            </LineChart>
          </ResponsiveContainer>
        </Card>

        {/* Category Distribution */}
        <Card className="p-6 bg-card border-border">
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Stok berdasarkan Kategori</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={categoryData}>
              <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--border))" opacity={0.2} />
              <XAxis 
                dataKey="name" 
                stroke="hsl(var(--muted-foreground))" 
                fontSize={12}
                angle={-15}
                textAnchor="end"
                height={80}
              />
              <YAxis 
                stroke="hsl(var(--muted-foreground))" 
                fontSize={12}
                tickFormatter={(value) => `${(value / 1000000).toFixed(0)}jt`}
              />
              <Tooltip
                contentStyle={{
                  backgroundColor: "hsl(var(--card))",
                  border: "1px solid hsl(var(--border))",
                  borderRadius: "8px",
                  color: "hsl(var(--card-foreground))",
                }}
                labelStyle={{ color: "hsl(var(--card-foreground))", fontWeight: 600 }}
                formatter={(value: number) => [
                  `Rp ${(value / 1000000).toFixed(1)} juta`,
                  "Nilai Stok"
                ]}
                cursor={{ fill: "hsl(var(--accent))", opacity: 0.1 }}
              />
              <Bar dataKey="value" radius={[8, 8, 0, 0]}>
                {categoryData.map((entry, index) => (
                  <Cell key={`cell-${index}`} fill={entry.color} />
                ))}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
          
          {/* Custom Legend with KM Context */}
          <div className="mt-4 grid grid-cols-2 gap-3">
            {categoryData.map((category) => (
              <div key={category.name} className="flex items-center gap-2">
                <div 
                  className="w-4 h-4 rounded" 
                  style={{ backgroundColor: category.color }}
                />
                <span className="text-xs text-muted-foreground">{category.name}</span>
              </div>
            ))}
          </div>
          
          {/* KM Insight Badge */}
          <div className="mt-4 p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg">
            <p className="text-xs text-blue-400">
              <strong>💡 KM Insight:</strong> Visualisasi berbasis warna mempercepat interpretasi data stok (Model 4I) 
              dan membantu organizing knowledge untuk pengambilan keputusan strategis.
            </p>
          </div>
        </Card>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        {[
          { label: "Total Kategori", value: "4" },
          { label: "Rata-rata Perputaran", value: "6.2 minggu" },
          { label: "Pertumbuhan YoY", value: "+18.3%" },
          { label: "Bulan Puncak", value: "Mei" },
        ].map((stat, i) => (
          <Card key={i} className="p-4 bg-card border-border text-center">
            <p className="text-sm text-muted-foreground mb-2">{stat.label}</p>
            <p className="text-2xl font-bold text-card-foreground">{stat.value}</p>
          </Card>
        ))}
      </div>
    </div>
  )
}
