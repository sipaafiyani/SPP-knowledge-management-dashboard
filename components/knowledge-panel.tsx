"use client"

import { Dialog, DialogContent, DialogHeader, DialogTitle } from "@/components/ui/dialog"
import { Card } from "@/components/ui/card"
import { BookOpen, Users } from "lucide-react"

interface KnowledgePanelProps {
  item: any
  onClose: () => void
}

export function KnowledgePanel({ item, onClose }: KnowledgePanelProps) {
  return (
    <Dialog open={true} onOpenChange={onClose}>
      <DialogContent className="max-w-2xl bg-card border-border">
        <DialogHeader>
          <DialogTitle className="text-card-foreground">{item.name}</DialogTitle>
        </DialogHeader>

        <div className="grid grid-cols-2 gap-6 mt-6">
          <Card className="p-6 bg-background border-border">
            <div className="flex items-center gap-2 mb-4">
              <BookOpen className="w-5 h-5 text-primary" />
              <h3 className="font-semibold text-card-foreground">Pengetahuan Eksplisit</h3>
            </div>
            <p className="text-sm text-muted-foreground">{item.knowledge.explicit}</p>
          </Card>

          <Card className="p-6 bg-background border-border">
            <div className="flex items-center gap-2 mb-4">
              <Users className="w-5 h-5 text-accent" />
              <h3 className="font-semibold text-card-foreground">Pengetahuan Tacit</h3>
            </div>
            <p className="text-sm text-muted-foreground">{item.knowledge.tacit}</p>
          </Card>
        </div>
      </DialogContent>
    </Dialog>
  )
}
