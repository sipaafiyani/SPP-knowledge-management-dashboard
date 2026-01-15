"use client"

import { useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { ShieldCheck, Sparkles, Database, BookOpen } from "lucide-react"
import { Button } from "@/components/ui/button"

interface LoginSuccessModalProps {
  isOpen: boolean
  userName: string
  userRole: "admin" | "staff"
  onClose: () => void
}

const roleConfig = {
  admin: {
    title: "Akses Penuh Administrator",
    message: "Anda memiliki kontrol penuh untuk mengonfigurasi kebijakan dan prosedur Knowledge Management organisasi.",
    insight: "Kelola seluruh aset strategis dan distribusi pengetahuan di seluruh hierarki organisasi.",
    accessLevel: "Full Access",
    icon: ShieldCheck,
    color: "from-blue-500 to-cyan-500",
    badgeColor: "bg-blue-500/20 text-blue-400 border-blue-500/30",
  },
  staff: {
    title: "Akses Operasional",
    message: "Silakan lakukan eksternalisasi pengalaman dan lessons learned Anda ke dalam basis pengetahuan organisasi.",
    insight: "Kontribusi Anda membantu membangun competitive advantage melalui knowledge repository.",
    accessLevel: "Operational Access",
    icon: BookOpen,
    color: "from-green-500 to-emerald-500",
    badgeColor: "bg-green-500/20 text-green-400 border-green-500/30",
  },
}

export default function LoginSuccessModal({
  isOpen,
  userName,
  userRole,
  onClose,
}: LoginSuccessModalProps) {
  const config = roleConfig[userRole]
  const IconComponent = config.icon

  // Auto close after 5 seconds if user doesn't click
  useEffect(() => {
    if (isOpen) {
      const timer = setTimeout(() => {
        onClose()
      }, 8000)
      return () => clearTimeout(timer)
    }
  }, [isOpen, onClose])

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          {/* Backdrop */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50"
            onClick={onClose}
          />

          {/* Modal */}
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <motion.div
              initial={{ opacity: 0, scale: 0.9, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.9, y: 20 }}
              transition={{ type: "spring", duration: 0.5 }}
              className="w-full max-w-lg bg-gradient-to-b from-gray-800 to-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden"
              onClick={(e) => e.stopPropagation()}
            >
              {/* Header with gradient */}
              <div className={`relative bg-gradient-to-r ${config.color} p-6`}>
                <div className="absolute inset-0 bg-grid-pattern opacity-10" />
                <div className="relative flex items-center justify-center">
                  <motion.div
                    initial={{ scale: 0, rotate: -180 }}
                    animate={{ scale: 1, rotate: 0 }}
                    transition={{ delay: 0.2, type: "spring", stiffness: 200 }}
                    className="relative"
                  >
                    <div className="absolute inset-0 bg-white/30 rounded-full blur-xl animate-pulse" />
                    <div className="relative bg-white rounded-full p-4">
                      <IconComponent className="w-12 h-12 text-green-500" strokeWidth={2.5} />
                    </div>
                  </motion.div>
                </div>
              </div>

              {/* Content */}
              <div className="p-8 space-y-6">
                {/* Success Title */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.3 }}
                  className="text-center"
                >
                  <h2 className="text-3xl font-bold text-white mb-2">
                    Login Berhasil! ✓
                  </h2>
                  <p className="text-xl text-gray-300">
                    Selamat datang kembali, <span className="font-semibold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">{userName}</span>!
                  </p>
                </motion.div>

                {/* Access Level Badge */}
                <motion.div
                  initial={{ opacity: 0, scale: 0.8 }}
                  animate={{ opacity: 1, scale: 1 }}
                  transition={{ delay: 0.4 }}
                  className="flex justify-center"
                >
                  <div className={`inline-flex items-center gap-2 px-4 py-2 rounded-full border ${config.badgeColor}`}>
                    <Database className="w-4 h-4" />
                    <span className="text-sm font-semibold">{config.accessLevel}</span>
                  </div>
                </motion.div>

                {/* Main Message */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.5 }}
                  className="space-y-4"
                >
                  <div className="bg-gray-800/50 border border-gray-700 rounded-xl p-5 space-y-3">
                    <h3 className="text-lg font-semibold text-white flex items-center gap-2">
                      <Sparkles className="w-5 h-5 text-yellow-400" />
                      {config.title}
                    </h3>
                    <p className="text-gray-300 leading-relaxed">
                      {config.message}
                    </p>
                  </div>

                  {/* KM Insight */}
                  <div className="bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 rounded-xl p-4">
                    <p className="text-sm text-gray-300 leading-relaxed">
                      <span className="font-semibold text-blue-400">💡 KM Insight:</span> {config.insight}
                    </p>
                  </div>
                </motion.div>

                {/* Strategic Information */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.6 }}
                  className="text-center text-sm text-gray-400"
                >
                  Akses ke <span className="font-semibold text-emerald-400">Strategic Knowledge Repository</span> telah dibuka. Anda dapat mulai mengelola aset pengetahuan organisasi sekarang.
                </motion.div>

                {/* CTA Button */}
                <motion.div
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: 0.7 }}
                >
                  <Button
                    onClick={onClose}
                    className="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-semibold h-12 text-base rounded-xl shadow-lg hover:shadow-xl transition-all duration-300"
                  >
                    <ShieldCheck className="w-5 h-5 mr-2" />
                    Masuk ke Dashboard
                  </Button>
                </motion.div>

                {/* Footer note */}
                <motion.p
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  transition={{ delay: 0.8 }}
                  className="text-xs text-center text-gray-500"
                >
                  Mode Demo - Backend tidak terhubung
                </motion.p>
              </div>
            </motion.div>
          </div>
        </>
      )}
    </AnimatePresence>
  )
}
