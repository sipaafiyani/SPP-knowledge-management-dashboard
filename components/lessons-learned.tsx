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
    title: "Kain katun menyusut 5% setelah pencucian pertama",
    category: "Tacit to Explicit",
    author: "Bu Siti (Penjahit Senior)",
    date: "2 hari lalu",
    solution: "Tambahkan toleransi 7-8 cm saat memotong pola untuk baju ukuran M-XL",
    impact: "Tinggi",
    seciType: "Eksternalisasi",
  },
  {
    id: 2,
    title: "Supplier kain lokal sama kualitasnya dengan import",
    category: "Knowledge-Based View",
    author: "Pak Budi (Purchasing)",
    date: "1 minggu lalu",
    solution: "Ganti supplier import ke lokal merk 'Primisima' untuk hemat 35% tanpa turun kualitas",
    impact: "Tinggi",
    seciType: "Kombinasi",
  },
  {
    id: 3,
    title: "Teknik jahit obras lebih rapi untuk kaos rajut",
    category: "Sosialisasi",
    author: "Ibu Ani (QC)",
    date: "2 minggu lalu",
    solution: "Gunakan setingan mesin obras lebar 4mm dan tension 3-4 untuk hasil jahitan rapi",
    impact: "Sedang",
    seciType: "Sosialisasi",
  },
  {
    id: 4,
    title: "Pola potong V-Shape hemat 12% bahan untuk kemeja",
    category: "Lean KM",
    author: "Mas Joko (Cutting)",
    date: "3 minggu lalu",
    solution: "Susun pola dengan teknik interlocking sebelum potong untuk minimalkan waste",
    impact: "Tinggi",
    seciType: "Internalisasi",
  },
]

export function LessonsLearned() {
  const [showForm, setShowForm] = useState(false)

  return (
    <div className="p-8">
      <div className="mb-6">
        <Button onClick={() => setShowForm(!showForm)} className="bg-primary hover:bg-primary/90">
          <Plus className="w-4 h-4 mr-2" />
          Tambah Pelajaran
        </Button>
      </div>

      {showForm && (
        <Card className="p-6 bg-card border-border mb-6">
          <h3 className="text-lg font-semibold text-card-foreground mb-4">Catat Pengetahuan Baru (SECI Model)</h3>
          <div className="space-y-4">
            <Input
              placeholder="Contoh: Teknik jahit lubang kancing yang lebih kuat..."
              className="bg-input border-border text-foreground placeholder:text-muted-foreground"
            />
            <Textarea
              placeholder="Jelaskan masalah, solusi, dan dampaknya terhadap produksi..."
              className="bg-input border-border text-foreground placeholder:text-muted-foreground min-h-24"
            />
            <div className="flex gap-2">
              <Button className="bg-primary hover:bg-primary/90">Simpan Pengetahuan</Button>
              <Button variant="outline" onClick={() => setShowForm(false)}>
                Batal
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
                  <Badge variant={lesson.impact === "Tinggi" ? "destructive" : "secondary"}>Dampak {lesson.impact}</Badge>
                  {lesson.seciType && (
                    <Badge className="bg-blue-500/20 text-blue-400 border-blue-500/30">SECI: {lesson.seciType}</Badge>
                  )}
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
