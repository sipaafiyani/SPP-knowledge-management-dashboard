"use client"

import { Card } from "@/components/ui/card"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Textarea } from "@/components/ui/textarea"
import { Badge } from "@/components/ui/badge"
import { Plus, MessageSquare } from "lucide-react"
import { useState } from "react"

const lessonsData = [
  {
    id: 1,
    title: "Vendor A delivery delays on rainy days",
    category: "Vendor Relations",
    author: "John Doe",
    date: "2 days ago",
    solution: "Order 2 days earlier during monsoon season",
    impact: "High",
  },
  {
    id: 2,
    title: "Bulk ordering reduces component cost",
    category: "Procurement",
    author: "Sarah Smith",
    date: "1 week ago",
    solution: "Negotiate bulk rates with suppliers for 15% savings",
    impact: "High",
  },
  {
    id: 3,
    title: "Sensor calibration checklist",
    category: "Quality",
    author: "Mike Chen",
    date: "2 weeks ago",
    solution: "Always calibrate electronic sensors before deployment",
    impact: "Medium",
  },
]

export function LessonsLearned() {
  const [showForm, setShowForm] = useState(false)

  return (
    <div className="p-8">
      <div className="mb-6">
        <Button onClick={() => setShowForm(!showForm)} className="bg-primary hover:bg-primary/90">
          <Plus className="w-4 h-4 mr-2" />
          Add Lesson Learned
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 bg-card border-border mb-6">
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Record New Insight</h3>
          <div className="space-y-4">
            <Input
              placeholder="Brief title..."
              className="bg-input border-border text-foreground placeholder:text-muted-foreground"
            />
            <Textarea
              placeholder="Describe the problem and solution..."
              className="bg-input border-border text-foreground placeholder:text-muted-foreground min-h-24"
            />
            <div className="flex gap-2">
              <Button className="bg-primary hover:bg-primary/90">Save Insight</Button>
              <Button variant="outline" onClick={() => setShowForm(false)}>
                Cancel
              </Button>
            </div>
          </div>
        </Card>
      )}

      <div className="space-y-4">
        {lessonsData.map((lesson) => (
          <Card key={lesson.id} className="p-6 bg-card border-border hover:border-border/80 transition-colors">
            <div className="flex items-start justify-between mb-3">
              <div>
                <h3 className="font-semibold text-card-foreground">{lesson.title}</h3>
                <div className="flex items-center gap-2 mt-2">
                  <Badge variant="outline">{lesson.category}</Badge>
                  <Badge variant={lesson.impact === "High" ? "destructive" : "secondary"}>{lesson.impact} Impact</Badge>
                </div>
              </div>
              <Button variant="ghost" size="icon" className="text-muted-foreground">
                <MessageSquare className="w-5 h-5" />
              </Button>
            </div>
            <p className="text-sm text-muted-foreground mb-3">{lesson.solution}</p>
            <div className="flex items-center justify-between text-xs text-muted-foreground">
              <span>
                {lesson.author} • {lesson.date}
              </span>
            </div>
          </Card>
        ))}
      </div>
    </div>
  )
}
