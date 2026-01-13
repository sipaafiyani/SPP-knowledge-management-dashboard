"use client"

import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Info } from "lucide-react"
import { useState } from "react"
import { KnowledgePanel } from "./knowledge-panel"

const inventoryData = [
  {
    id: 1,
    name: "Premium Steel Components",
    category: "Raw Materials",
    stock: 450,
    threshold: 200,
    knowledge: { explicit: "Store in dry environment", tacit: "Supplier quality varies by season" },
  },
  {
    id: 2,
    name: "Aluminum Frames",
    category: "Components",
    stock: 120,
    threshold: 150,
    knowledge: { explicit: "Handle with care - oxidation risk", tacit: "Most orders arrive early with this vendor" },
  },
  {
    id: 3,
    name: "Fasteners Kit",
    category: "Accessories",
    stock: 890,
    threshold: 300,
    knowledge: { explicit: "Organize by size (M3-M8)", tacit: "Bulk ordering reduces cost by 15%" },
  },
  {
    id: 4,
    name: "Electronic Sensors",
    category: "Electronics",
    stock: 45,
    threshold: 100,
    knowledge: { explicit: "Calibration required before use", tacit: "Lead time increased to 3 weeks" },
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
                <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Item Name</th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Category</th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Stock Level</th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Status</th>
                <th className="px-6 py-4 text-left text-sm font-semibold text-muted-foreground">Knowledge</th>
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
                      <span className="text-sm font-medium text-card-foreground">{item.stock}</span>
                    </div>
                  </td>
                  <td className="px-6 py-4">
                    <span
                      className={`text-xs font-semibold px-3 py-1 rounded-full ${
                        item.stock > item.threshold ? "bg-green-500/20 text-green-400" : "bg-red-500/20 text-red-400"
                      }`}
                    >
                      {item.stock > item.threshold ? "Optimal" : "Low Stock"}
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
