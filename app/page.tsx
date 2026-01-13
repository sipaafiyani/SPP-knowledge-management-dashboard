"use client"

import { useState } from "react"
import { DashboardLayout } from "@/components/dashboard-layout"
import { StrategicOverview } from "@/components/strategic-overview"
import { InventoryTable } from "@/components/inventory-table"
import { VendorIntelligence } from "@/components/vendor-intelligence"
import { LessonsLearned } from "@/components/lessons-learned"
import { InventoryAnalytics } from "@/components/inventory-analytics"

export default function DashboardPage() {
  const [activeSection, setActiveSection] = useState("overview")

  return (
    <DashboardLayout activeSection={activeSection} setActiveSection={setActiveSection}>
      {activeSection === "overview" && <StrategicOverview />}
      {activeSection === "inventory" && <InventoryTable />}
      {activeSection === "vendors" && <VendorIntelligence />}
      {activeSection === "knowledge" && <LessonsLearned />}
      {activeSection === "analytics" && <InventoryAnalytics />}
    </DashboardLayout>
  )
}
