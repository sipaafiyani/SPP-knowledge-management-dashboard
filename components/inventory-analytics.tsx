"use client"

import { Card } from "@/components/ui/card"
import { LineChart, Line, BarChart, Bar, XAxis, YAxis, CartesianGrid, Tooltip, ResponsiveContainer } from "recharts"

const stockTrendData = [
  { month: "Jan", value: 2100000 },
  { month: "Feb", value: 2250000 },
  { month: "Mar", value: 2180000 },
  { month: "Apr", value: 2320000 },
  { month: "May", value: 2450000 },
  { month: "Jun", value: 2380000 },
]

const categoryData = [
  { name: "Raw Materials", value: 850000 },
  { name: "Components", value: 620000 },
  { name: "Accessories", value: 520000 },
  { name: "Electronics", value: 460000 },
]

export function InventoryAnalytics() {
  return (
    <div className="p-8">
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Stock Trend */}
        <Card className="p-6 bg-card border-border">
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Stock Value Trend</h3>
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
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Stock by Category</h3>
          <ResponsiveContainer width="100%" height={300}>
            <BarChart data={categoryData}>
              <CartesianGrid strokeDasharray="3 3" stroke="hsl(var(--color-border))" />
              <XAxis dataKey="name" stroke="hsl(var(--color-muted-foreground))" />
              <YAxis stroke="hsl(var(--color-muted-foreground))" />
              <Tooltip
                contentStyle={{
                  backgroundColor: "hsl(var(--color-card))",
                  border: "1px solid hsl(var(--color-border))",
                }}
              />
              <Bar dataKey="value" fill="hsl(var(--color-primary))" />
            </BarChart>
          </ResponsiveContainer>
        </Card>
      </div>

      {/* Summary Stats */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        {[
          { label: "Total Categories", value: "4" },
          { label: "Avg Turnover", value: "6.2 weeks" },
          { label: "YoY Growth", value: "+18.3%" },
          { label: "Peak Month", value: "May" },
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
