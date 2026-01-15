"use client"

import { useState, FormEvent } from "react"
import { useRouter } from "next/navigation"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Card } from "@/components/ui/card"
import { Shield, Lock, Mail, Loader2, Eye, EyeOff } from "lucide-react"
import axios from "axios"
import LoginSuccessModal from "@/components/login-success-modal"

interface LoginResponse {
  success: boolean
  message: string
  data?: {
    user: {
      id: number
      name: string
      email: string
      role: string
      position: string
      department: string
    }
    token: string
    permissions: Record<string, boolean>
  }
  km_insight?: string
}

export default function LoginPage() {
  const router = useRouter()
  const [loading, setLoading] = useState(false)
  const [showPassword, setShowPassword] = useState(false)
  const [showSuccessModal, setShowSuccessModal] = useState(false)
  const [loggedInUser, setLoggedInUser] = useState<{
    name: string
    role: "admin" | "staff"
  } | null>(null)
  const [formData, setFormData] = useState({
    email: "",
    password: "",
  })
  const [error, setError] = useState("")

  const handleSubmit = async (e: FormEvent) => {
    e.preventDefault()
    setLoading(true)
    setError("")

    try {
      // Try backend API first
      try {
        const response = await axios.post<LoginResponse>(
          "http://localhost:8000/api/auth/login",
          formData,
          { timeout: 3000 }
        )

        if (response.data.success && response.data.data) {
          // Save auth data
          localStorage.setItem("auth_token", response.data.data.token)
          localStorage.setItem("user", JSON.stringify(response.data.data.user))
          localStorage.setItem("permissions", JSON.stringify(response.data.data.permissions))
          
          // Show success modal
          setLoggedInUser({
            name: response.data.data.user.name,
            role: response.data.data.user.role as "admin" | "manager" | "staff",
          })
          setShowSuccessModal(true)
          return
        }
      } catch (apiError: any) {
        if (apiError.response) {
          // API responded with error
          throw new Error(apiError.response.data.message || "Login gagal")
        }
        console.log("Backend tidak tersedia, menggunakan mode demo")
      }

      // FALLBACK: Demo mode authentication
      const demoUsers = [
        {
          email: "admin",
          password: "admin",
          user: {
            id: 1,
            name: "Admin derras",
            email: "admin",
            role: "admin",
            position: "System Administrator",
            department: "IT",
          },
          permissions: {
            dashboard: true,
            inventaris: true,
            analitik: true,
            vendor: true,
            pengetahuan: true,
            users: true,
            settings: true,
          },
        },
        {
          email: "admin",
          password: "admin",
          user: {
            id: 3,
            name: "Staff Gudang",
            email: "admin",
            role: "staff",
            position: "Warehouse Staff",
            department: "Gudang",
          },
          permissions: {
            dashboard: false,
            inventaris: true,
            analitik: false,
            vendor: false,
            pengetahuan: true,
            users: false,
            settings: false,
          },
        },
      ]

      const user = demoUsers.find(
        (u) => u.email === formData.email && u.password === formData.password
      )

      if (user) {
        // Save demo auth data
        localStorage.setItem("auth_token", "demo_token_" + Date.now())
        localStorage.setItem("user", JSON.stringify(user.user))
        localStorage.setItem("permissions", JSON.stringify(user.permissions))

        // Show success modal
        setLoggedInUser({
          name: user.user.name,
          role: user.user.role as "admin" | "manager" | "staff",
        })
        setShowSuccessModal(true)
      } else {
        setError("Email atau password salah")
      }
    } catch (err: any) {
      setError(err.message || "Login gagal. Silakan coba lagi.")
    } finally {
      setLoading(false)
    }
  }

  const handleDemoLogin = async (role: "admin" | "staff") => {
    setLoading(true)
    setError("")

    const demoUsers = {
      admin: {
        user: {
          id: 1,
          name: "Admin derras",
          email: "admin",
          role: "admin",
          position: "System Administrator",
          department: "IT",
        },
        permissions: {
          dashboard: true,
          inventaris: true,
          analitik: true,
          vendor: true,
          pengetahuan: true,
          users: true,
          settings: true,
        },
      },
      staff: {
        user: {
          id: 3,
          name: "Staff Gudang",
          email: "admin",
          role: "staff",
          position: "Warehouse Staff",
          department: "Gudang",
        },
        permissions: {
          dashboard: false,
          inventaris: true,
          analitik: false,
          vendor: false,
          pengetahuan: true,
          users: false,
          settings: false,
        },
      },
    }

    const selectedUser = demoUsers[role]

    // Save demo auth data
    localStorage.setItem("auth_token", "demo_token_" + Date.now())
    localStorage.setItem("user", JSON.stringify(selectedUser.user))
    localStorage.setItem("permissions", JSON.stringify(selectedUser.permissions))

    // Show success modal
    setLoggedInUser({
      name: selectedUser.user.name,
      role: role,
    })
    setShowSuccessModal(true)
  }

  const handleModalClose = () => {
    setShowSuccessModal(false)
    setLoading(false)
    // Redirect to dashboard
    setTimeout(() => {
      window.location.href = "/"
    }, 100)
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 flex items-center justify-center p-4">
      {/* Background Pattern */}
      <div className="absolute inset-0 bg-grid-pattern opacity-5" />
      
      <Card className="w-full max-w-md bg-gray-800/50 backdrop-blur-sm border-gray-700 shadow-2xl relative z-10">
        <div className="p-8">
          {/* Logo & Header */}
          <div className="text-center mb-8">
            <div className="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 mb-4">
              <Shield className="w-8 h-8 text-white" />
            </div>
            <h1 className="text-3xl font-bold text-white mb-2">derras</h1>
            <p className="text-gray-400 text-sm">
              Strategic Knowledge Management System
            </p>
            <p className="text-blue-400 text-xs mt-2">
              🔐 Role-Based Access Control
            </p>
          </div>

          {/* Login Form */}
          <form onSubmit={handleSubmit} className="space-y-5">
            {/* Email Field */}
            <div className="space-y-2">
              <label htmlFor="email" className="text-sm font-medium text-gray-300">
                Email / Username
              </label>
              <div className="relative">
                <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" />
                <Input
                  id="email"
                  type="text"
                  placeholder="admin"
                  value={formData.email}
                  onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                  required
                  disabled={loading}
                  className="pl-10 bg-gray-700/50 border-gray-600 text-white placeholder:text-gray-500 focus:border-blue-500"
                />
              </div>
            </div>

            {/* Password Field */}
            <div className="space-y-2">
              <label htmlFor="password" className="text-sm font-medium text-gray-300">
                Password
              </label>
              <div className="relative">
                <Lock className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" />
                <Input
                  id="password"
                  type={showPassword ? "text" : "password"}
                  placeholder="••••••••"
                  value={formData.password}
                  onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                  required
                  disabled={loading}
                  className="pl-10 pr-10 bg-gray-700/50 border-gray-600 text-white placeholder:text-gray-500 focus:border-blue-500"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300"
                  disabled={loading}
                >
                  {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                </button>
              </div>
            </div>

            {/* Error Message */}
            {error && (
              <div className="p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                <p className="text-sm text-red-400">{error}</p>
              </div>
            )}

            {/* Submit Button */}
            <Button
              type="submit"
              disabled={loading}
              className="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold h-11"
            >
              {loading ? (
                <>
                  <Loader2 className="mr-2 h-5 w-5 animate-spin" />
                  Memverifikasi...
                </>
              ) : (
                <>
                  <Lock className="mr-2 h-5 w-5" />
                  Masuk ke Sistem
                </>
              )}
            </Button>
          </form>

          {/* Demo Accounts */}
          <div className="mt-6 pt-6 border-t border-gray-700">
            <p className="text-xs text-gray-400 text-center mb-3">
              🧪 Testing Mode - Kredensial Universal:
            </p>
            <div className="grid grid-cols-2 gap-3">
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={() => handleDemoLogin("admin")}
                disabled={loading}
                className="text-xs border-blue-500/30 text-blue-400 hover:bg-blue-500/10"
              >
                Admin
              </Button>
              <Button
                type="button"
                variant="outline"
                size="sm"
                onClick={() => handleDemoLogin("staff")}
                disabled={loading}
                className="text-xs border-green-500/30 text-green-400 hover:bg-green-500/10"
              >
                Staff
              </Button>
            </div>
            <p className="text-xs text-gray-500 text-center mt-3">
              Email: <code className="text-green-400">admin</code> | Password: <code className="text-green-400">admin</code>
            </p>
            <p className="text-xs text-gray-600 text-center mt-1">
              (Sistem akan auto-select role berdasarkan button yang diklik)
            </p>
          </div>

          {/* Footer */}
          <div className="mt-6 text-center">
            <p className="text-xs text-gray-500">
              © 2026 derras - Knowledge Management System
            </p>
          </div>
        </div>
      </Card>

      {/* Login Success Modal */}
      {loggedInUser && (
        <LoginSuccessModal
          isOpen={showSuccessModal}
          userName={loggedInUser.name}
          userRole={loggedInUser.role}
          onClose={handleModalClose}
        />
      )}
    </div>
  )
}
