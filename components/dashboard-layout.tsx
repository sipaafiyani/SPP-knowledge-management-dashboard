"use client"

import type React from "react"

import { Sidebar } from "./sidebar"
import { Header } from "./header"

interface DashboardLayoutProps {
  children: React.ReactNode
  activeSection: string
  setActiveSection: (section: string) => void
}

export function DashboardLayout({ children, activeSection, setActiveSection }: DashboardLayoutProps) {
  return (
    <div className="flex h-screen bg-background">
      <Sidebar activeSection={activeSection} setActiveSection={setActiveSection} />
      <div className="flex-1 flex flex-col overflow-hidden">
        <Header activeSection={activeSection} />
        <main className="flex-1 overflow-auto">{children}</main>
      </div>
    </div>
  )
}
