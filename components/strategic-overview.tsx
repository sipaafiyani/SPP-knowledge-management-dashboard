"use client"

import { Card } from "@/components/ui/card"
import { TrendingUp, AlertCircle, CheckCircle, Activity } from "lucide-react"
import { Badge } from "@/components/ui/badge"

const metrics = [
  {
    title: "Total Stock Value",
    value: "$2,450,000",
    change: "+12.5%",
    positive: true,
    icon: TrendingUp,
  },
  {
    title: "Low Stock Alerts",
    value: "23",
    change: "Items below threshold",
    positive: false,
    icon: AlertCircle,
  },
  {
    title: "Vendor Reliability Index",
    value: "8.7/10",
    change: "+0.3 this month",
    positive: true,
    icon: CheckCircle,
  },
  {
    title: "Knowledge Health Score",
    value: "76%",
    change: "Last updated 2 days ago",
    positive: true,
    icon: Activity,
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
            </Card>
          )
        })}
      </div>

      {/* Recently Updated Knowledge */}
      <Card className="p-6 bg-card border-border">
        <h3 className="text-lg font-semibold text-card-foreground mb-4">Recent Knowledge Updates</h3>
        <div className="space-y-4">
          {[
            { title: "SOP: Quality Assurance Process", updated: "2 hours ago" },
            { title: "Lesson Learned: Vendor A delivery delays", updated: "1 day ago" },
            { title: "Best Practice: Seasonal inventory planning", updated: "3 days ago" },
          ].map((item, i) => (
            <div
              key={i}
              className="flex items-center justify-between p-3 bg-background rounded-lg border border-border"
            >
              <div>
                <p className="font-medium text-card-foreground">{item.title}</p>
                <p className="text-xs text-muted-foreground mt-1">{item.updated}</p>
              </div>
              <Badge variant="outline">Updated</Badge>
            </div>
          ))}
        </div>
      </Card>
    </div>
  )
}
