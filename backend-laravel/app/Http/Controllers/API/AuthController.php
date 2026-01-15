<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

/**
 * Auth Controller - Knowledge Distribution Gateway
 * 
 * Mengatur akses ke sistem Knowledge Management berdasarkan role:
 * - Admin: Full access to all strategic knowledge
 * - Manager: Access to strategic insights & analytics
 * - Staff: Access to operational data only
 */
class AuthController extends Controller
{
    /**
     * LOGIN - Authenticate user and generate token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi input - Allow non-standard email format for testing
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string|min:5',
        ], [
            'email.required' => 'Email/Username wajib diisi',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 5 karakter',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Validasi kredensial
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Check if account is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
            ], 403);
        }

        // Generate token (Laravel Sanctum)
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        // Return response dengan token dan user data
        return response()->json([
            'success' => true,
            'message' => "Selamat datang, {$user->name}!",
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'position' => $user->position,
                    'department' => $user->department,
                    'avatar' => $user->avatar,
                ],
                'token' => $token,
                'permissions' => $this->getRolePermissions($user->role),
            ],
            'km_insight' => $this->getKMInsight($user->role)
        ], 200);
    }

    /**
     * LOGOUT - Revoke current token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout. Sampai jumpa!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ME - Get current authenticated user
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        try {
            $user = $request->user();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'position' => $user->position,
                    'department' => $user->department,
                    'phone' => $user->phone,
                    'avatar' => $user->avatar,
                    'is_active' => $user->is_active,
                    'last_login_at' => $user->last_login_at,
                    'created_at' => $user->created_at,
                ],
                'permissions' => $this->getRolePermissions($user->role),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user'
            ], 500);
        }
    }

    /**
     * REFRESH TOKEN - Generate new token
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $user = $request->user();
            
            // Revoke old token
            $request->user()->currentAccessToken()->delete();
            
            // Generate new token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil di-refresh',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal refresh token'
            ], 500);
        }
    }

    /**
     * Get role-based permissions
     * 
     * @param string $role
     * @return array
     */
    private function getRolePermissions($role)
    {
        $permissions = [
            'admin' => [
                'dashboard' => true,
                'inventaris' => true,
                'analitik' => true,
                'vendor' => true,
                'pengetahuan' => true,
                'users' => true,
                'settings' => true,
            ],
            'manager' => [
                'dashboard' => true,
                'inventaris' => true,
                'analitik' => true,
                'vendor' => true,
                'pengetahuan' => true,
                'users' => false,
                'settings' => false,
            ],
            'staff' => [
                'dashboard' => false,
                'inventaris' => true,
                'analitik' => false,
                'vendor' => false,
                'pengetahuan' => true,
                'users' => false,
                'settings' => false,
            ],
        ];

        return $permissions[$role] ?? $permissions['staff'];
    }

    /**
     * Get KM insight based on role
     * 
     * @param string $role
     * @return string
     */
    private function getKMInsight($role)
    {
        $insights = [
            'admin' => 'Anda memiliki akses penuh ke seluruh knowledge repository untuk pengambilan keputusan strategis.',
            'manager' => 'Anda dapat mengakses strategic insights dan analytics untuk mendukung keputusan manajerial.',
            'staff' => 'Anda memiliki akses ke data operasional inventaris dan basis pengetahuan untuk mendukung tugas harian.',
        ];

        return $insights[$role] ?? $insights['staff'];
    }
}
