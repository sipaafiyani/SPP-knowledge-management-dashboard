"use client"

import { Card } from "@/components/ui/card"
import { Badge } from "@/components/ui/badge"
import { Star, TrendingUp } from "lucide-react"

const vendors = [
  {
    id: 1,
    name: "Global Supplies Ltd",
    quality: 9.2,
    speed: 8.8,
    reliability: 9.0,
    recommended: true,
    lastDelivery: "2 days ago",
  },
  {
    id: 2,
    name: "Fast Import Co",
    quality: 8.1,
    speed: 9.5,
    reliability: 8.3,
    recommended: true,
    lastDelivery: "1 day ago",
  },
  {
    id: 3,
    name: "Standard Parts Inc",
    quality: 7.8,
    speed: 7.2,
    reliability: 7.5,
    recommended: false,
    lastDelivery: "5 days ago",
  },
  {
    id: 4,
    name: "Premium Materials",
    quality: 8.9,
    speed: 8.0,
    reliability: 8.6,
    recommended: false,
    lastDelivery: "3 days ago",
  },
]

export function VendorIntelligence() {
  return (
    <div className="p-8">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {vendors.map((vendor) => {
          const overallScore = (vendor.quality + vendor.speed + vendor.reliability) / 3
          return (
            <Card key={vendor.id} className="p-6 bg-card border-border">
              <div className="flex items-start justify-between mb-4">
                <div>
                  <h3 className="font-semibold text-card-foreground">{vendor.name}</h3>
                  <p className="text-xs text-muted-foreground mt-1">Last delivery: {vendor.lastDelivery}</p>
                </div>
                {vendor.recommended && (
                  <Badge className="bg-accent text-accent-foreground flex items-center gap-1">
                    <TrendingUp className="w-3 h-3" />
                    Top Pick
                  </Badge>
                )}
              </div>

              <div className="space-y-3 mb-4">
                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-muted-foreground">Quality</span>
                    <span className="text-sm font-semibold text-card-foreground">{vendor.quality}/10</span>
                  </div>
                  <div className="h-2 bg-background rounded-full overflow-hidden">
                    <div className="h-full bg-blue-500 rounded-full" style={{ width: `${vendor.quality * 10}%` }} />
                  </div>
                </div>

                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-muted-foreground">Delivery Speed</span>
                    <span className="text-sm font-semibold text-card-foreground">{vendor.speed}/10</span>
                  </div>
                  <div className="h-2 bg-background rounded-full overflow-hidden">
                    <div className="h-full bg-cyan-500 rounded-full" style={{ width: `${vendor.speed * 10}%` }} />
                  </div>
                </div>

                <div>
                  <div className="flex items-center justify-between mb-1">
                    <span className="text-xs text-muted-foreground">Reliability Index</span>
                    <span className="text-sm font-semibold text-card-foreground">{vendor.reliability}/10</span>
                  </div>
                  <div className="h-2 bg-background rounded-full overflow-hidden">
                    <div
                      className="h-full bg-green-500 rounded-full"
                      style={{ width: `${vendor.reliability * 10}%` }}
                    />
                  </div>
                </div>
              </div>

              <div className="pt-4 border-t border-border flex items-center justify-between">
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
            </Card>
          )
        })}
      </div>
    </div>
  )
}
