"use client"

import { cn } from "@/lib/utils"
import { LayoutDashboard, Package, Users, BookOpen, TrendingUp, Settings } from "lucide-react"

interface SidebarProps {
  activeSection: string
  setActiveSection: (section: string) => void
}

const navigation = [
  { id: "overview", label: "Dashboard", icon: LayoutDashboard },
  { id: "inventory", label: "Inventory", icon: Package },
  { id: "vendors", label: "Vendors", icon: Users },
  { id: "knowledge", label: "Knowledge Base", icon: BookOpen },
  { id: "analytics", label: "Analytics", icon: TrendingUp },
]

export function Sidebar({ activeSection, setActiveSection }: SidebarProps) {
  return (
    <aside className="w-64 bg-sidebar border-r border-sidebar-border flex flex-col">
      {/* Logo */}
      <div className="p-6 border-b border-sidebar-border">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-lg bg-sidebar-primary flex items-center justify-center">
            <BookOpen className="w-6 h-6 text-sidebar-primary-foreground" />
          </div>
          <div>
            <h1 className="font-semibold text-sidebar-foreground">KM Hub</h1>
            <p className="text-xs text-sidebar-foreground/60">Enterprise</p>
          </div>
        </div>
      </div>

      {/* Navigation */}
      <nav className="flex-1 p-4 space-y-2">
        {navigation.map((item) => {
          const Icon = item.icon
          return (
            <button
              key={item.id}
              onClick={() => setActiveSection(item.id)}
              className={cn(
                "w-full flex items-center gap-3 px-4 py-3 rounded-lg transition-colors text-left",
                activeSection === item.id
                  ? "bg-sidebar-primary text-sidebar-primary-foreground"
                  : "text-sidebar-foreground hover:bg-sidebar-accent/50",
              )}
            >
              <Icon className="w-5 h-5" />
              <span className="font-medium">{item.label}</span>
            </button>
          )
        })}
      </nav>

      {/* Settings Footer */}
      <div className="p-4 border-t border-sidebar-border">
        <button className="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sidebar-foreground hover:bg-sidebar-accent/50 transition-colors">
          <Settings className="w-5 h-5" />
          <span className="font-medium">Settings</span>
        </button>
      </div>
    </aside>
  )
}
