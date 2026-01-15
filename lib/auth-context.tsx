"use client"

import { createContext, useContext, useState, useEffect, ReactNode } from "react"
import { useRouter, usePathname } from "next/navigation"

interface User {
  id: number
  name: string
  email: string
  role: string
  position: string
  department: string
}

interface Permissions {
  dashboard: boolean
  inventaris: boolean
  analitik: boolean
  vendor: boolean
  pengetahuan: boolean
  users: boolean
  settings: boolean
}

interface AuthContextType {
  user: User | null
  permissions: Permissions | null
  token: string | null
  isLoading: boolean
  isAuthenticated: boolean
  login: (token: string, user: User, permissions: Permissions) => void
  logout: () => void
  hasPermission: (permission: keyof Permissions) => boolean
  checkAuth: () => Promise<boolean>
}

const AuthContext = createContext<AuthContextType | undefined>(undefined)

export function AuthProvider({ children }: { children: ReactNode }) {
  const router = useRouter()
  const pathname = usePathname()
  const [user, setUser] = useState<User | null>(null)
  const [permissions, setPermissions] = useState<Permissions | null>(null)
  const [token, setToken] = useState<string | null>(null)
  const [isLoading, setIsLoading] = useState(true)

  // Check authentication on mount
  useEffect(() => {
    checkAuth()
  }, [])

  // Redirect unauthenticated users
  useEffect(() => {
    if (!isLoading && !user && pathname !== "/login") {
      router.push("/login")
    }
  }, [isLoading, user, pathname, router])

  const checkAuth = async (): Promise<boolean> => {
    try {
      const storedToken = localStorage.getItem("auth_token")
      const storedUser = localStorage.getItem("user")
      const storedPermissions = localStorage.getItem("permissions")

      if (storedToken && storedUser && storedPermissions) {
        setToken(storedToken)
        setUser(JSON.parse(storedUser))
        setPermissions(JSON.parse(storedPermissions))
        setIsLoading(false)
        return true
      }

      setIsLoading(false)
      return false
    } catch (error) {
      console.error("Auth check failed:", error)
      setIsLoading(false)
      return false
    }
  }

  const login = (newToken: string, newUser: User, newPermissions: Permissions) => {
    localStorage.setItem("auth_token", newToken)
    localStorage.setItem("user", JSON.stringify(newUser))
    localStorage.setItem("permissions", JSON.stringify(newPermissions))
    
    setToken(newToken)
    setUser(newUser)
    setPermissions(newPermissions)
  }

  const logout = () => {
    localStorage.removeItem("auth_token")
    localStorage.removeItem("user")
    localStorage.removeItem("permissions")
    
    setToken(null)
    setUser(null)
    setPermissions(null)
    
    router.push("/login")
  }

  const hasPermission = (permission: keyof Permissions): boolean => {
    return permissions?.[permission] ?? false
  }

  const value: AuthContextType = {
    user,
    permissions,
    token,
    isLoading,
    isAuthenticated: !!user,
    login,
    logout,
    hasPermission,
    checkAuth,
  }

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>
}

export function useAuth() {
  const context = useContext(AuthContext)
  if (context === undefined) {
    throw new Error("useAuth must be used within an AuthProvider")
  }
  return context
}
