"use client"

import { useState, useEffect } from "react"
import { useRouter } from "next/navigation"
import { DashboardLayout } from "@/components/dashboard-layout"
import { StrategicOverview } from "@/components/strategic-overview"
import { InventoryTable } from "@/components/inventory-table"
import { VendorIntelligence } from "@/components/vendor-intelligence"
import { LessonsLearned } from "@/components/lessons-learned"
import { InventoryAnalytics } from "@/components/inventory-analytics"
import { useAuth } from "@/lib/auth-context"

export default function DashboardPage() {
  const router = useRouter()
  const { isAuthenticated, isLoading, hasPermission } = useAuth()
  const [activeSection, setActiveSection] = useState("overview")

  // Redirect to login if not authenticated
  useEffect(() => {
    if (!isLoading && !isAuthenticated) {
      router.push("/login")
    }
  }, [isLoading, isAuthenticated, router])

  // Auto-select first available section based on permissions
  useEffect(() => {
    if (isAuthenticated) {
      if (!hasPermission("dashboard") && hasPermission("inventaris")) {
        setActiveSection("inventory")
      }
    }
  }, [isAuthenticated, hasPermission])

  // Show loading state
  if (isLoading) {
    return (
      <div className="min-h-screen bg-background flex items-center justify-center">
        <div className="text-center">
          <div className="w-12 h-12 border-4 border-accent border-t-transparent rounded-full animate-spin mx-auto mb-4" />
          <p className="text-muted-foreground">Memuat dashboard...</p>
        </div>
      </div>
    )
  }

  // Don't render if not authenticated (will redirect)
  if (!isAuthenticated) {
    return null
  }

  return (
    <DashboardLayout activeSection={activeSection} setActiveSection={setActiveSection}>
      {activeSection === "overview" && hasPermission("dashboard") && <StrategicOverview />}
      {activeSection === "inventory" && hasPermission("inventaris") && <InventoryTable />}
      {activeSection === "vendors" && hasPermission("vendor") && <VendorIntelligence />}
      {activeSection === "knowledge" && hasPermission("pengetahuan") && <LessonsLearned />}
      {activeSection === "analytics" && hasPermission("analitik") && <InventoryAnalytics />}
      
      {/* No Permission Message */}
      {((activeSection === "overview" && !hasPermission("dashboard")) ||
        (activeSection === "inventory" && !hasPermission("inventaris")) ||
        (activeSection === "vendors" && !hasPermission("vendor")) ||
        (activeSection === "knowledge" && !hasPermission("pengetahuan")) ||
        (activeSection === "analytics" && !hasPermission("analitik"))) && (
        <div className="p-8">
          <div className="max-w-md mx-auto text-center py-12">
            <div className="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg className="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
            </div>
            <h3 className="text-lg font-semibold text-card-foreground mb-2">
              Akses Ditolak
            </h3>
            <p className="text-sm text-muted-foreground">
              Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator untuk mendapatkan akses.
            </p>
          </div>
        </div>
      )}
    </DashboardLayout>
  )
}
