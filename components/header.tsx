"use client"

import { Bell, Search, User } from "lucide-react"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"

interface HeaderProps {
  activeSection: string
}

const sectionTitles: Record<string, string> = {
  overview: "Ikhtisar Strategis",
  inventory: "Manajemen Inventaris",
  vendors: "Intelijen Vendor",
  knowledge: "Basis Pengetahuan",
  analytics: "Analitik Inventaris",
}

export function Header({ activeSection }: HeaderProps) {
  return (
    <header className="border-b border-border bg-card h-16 flex items-center justify-between px-8">
      <div>
        <h2 className="text-xl font-semibold text-card-foreground">{sectionTitles[activeSection] || "Dasbor"}</h2>
      </div>

      <div className="flex items-center gap-4">
        <div className="hidden md:block relative w-64">
          <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
          <Input
            placeholder="Cari..."
            className="pl-10 bg-input border-border text-foreground placeholder:text-muted-foreground"
          />
        </div>

        <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
          <Bell className="w-5 h-5" />
        </Button>

        <Button variant="ghost" size="icon" className="text-muted-foreground hover:text-foreground">
          <User className="w-5 h-5" />
        </Button>
      </div>
    </header>
  )
}
