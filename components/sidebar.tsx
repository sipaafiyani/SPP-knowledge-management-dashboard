"use client"

import { cn } from "@/lib/utils"
import { LayoutDashboard, Package, Users, BookOpen, TrendingUp, Settings, LogOut, User } from "lucide-react"
import { useAuth } from "@/lib/auth-context"

interface SidebarProps {
  activeSection: string
  setActiveSection: (section: string) => void
}

const navigation = [
  { id: "overview", label: "Dasbor", icon: LayoutDashboard, permission: "dashboard" },
  { id: "inventory", label: "Inventaris", icon: Package, permission: "inventaris" },
  { id: "vendors", label: "Vendor", icon: Users, permission: "vendor" },
  { id: "knowledge", label: "Basis Pengetahuan", icon: BookOpen, permission: "pengetahuan" },
  { id: "analytics", label: "Analitik", icon: TrendingUp, permission: "analitik" },
]

export function Sidebar({ activeSection, setActiveSection }: SidebarProps) {
  const { user, permissions, logout, hasPermission } = useAuth()

  // Filter navigation based on permissions
  const filteredNavigation = navigation.filter((item) =>
    hasPermission(item.permission as any)
  )
  return (
    <aside className="w-64 bg-sidebar border-r border-sidebar-border flex flex-col">
      {/* Logo */}
      <div className="p-6 border-b border-sidebar-border">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-lg bg-sidebar-primary flex items-center justify-center">
            <BookOpen className="w-6 h-6 text-sidebar-primary-foreground" />
          </div>
          <div>
            <h1 className="font-semibold text-sidebar-foreground">derras</h1>
            <p className="text-xs text-sidebar-foreground/60">Perusahaan</p>
          </div>
        </div>
      </div>

      {/* Navigation */}
      <nav className="flex-1 p-4 space-y-2">
        {filteredNavigation.map((item) => {
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

      {/* User Profile & Logout */}
      <div className="p-4 border-t border-sidebar-border space-y-2">
        {/* User Info */}
        <div className="px-4 py-3 bg-sidebar-accent/30 rounded-lg">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
              <User className="w-5 h-5 text-white" />
            </div>
            <div className="flex-1 min-w-0">
              <p className="text-sm font-semibold text-sidebar-foreground truncate">
                {user?.name || "Guest"}
              </p>
              <p className="text-xs text-sidebar-foreground/60 truncate">
                {user?.role === "admin" && "Administrator"}
                {user?.role === "manager" && "Manager"}
                {user?.role === "staff" && "Staff"}
              </p>
            </div>
          </div>
        </div>

        {/* Logout Button */}
        <button
          onClick={logout}
          className="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-500/10 transition-colors"
        >
          <LogOut className="w-5 h-5" />
          <span className="font-medium">Logout</span>
        </button>
      </div>
    </aside>
  )
}
