<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthApiController extends Controller
{
    /**
     * Login - kembalikan Sanctum token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $request->username)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah.',
            ], 401);
        }

        if (! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        // Hapus token lama (opsional)
        $user->tokens()->where('name', 'flutter-app')->delete();

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user'  => $this->formatUser($user),
            ],
        ]);
    }

    /**
     * Register siswa baru
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users',
            'email'    => 'required|email|unique:users',
            'nis'      => 'required|string|max:20|unique:users',
            'kelas'    => 'required|string|max:20',
            'no_hp'    => 'nullable|string|max:20',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'email.unique'    => 'Email sudah terdaftar.',
            'nis.unique'      => 'NIS sudah terdaftar.',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'siswa',
            'nis'       => $validated['nis'],
            'kelas'     => $validated['kelas'],
            'no_hp'     => $validated['no_hp'] ?? null,
        ]);

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil.',
            'data' => [
                'token' => $token,
                'user'  => $this->formatUser($user),
            ],
        ], 201);
    }

    /**
     * Logout - hapus token saat ini
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Get profil user yang sedang login
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $this->formatUser($request->user()),
        ]);
    }

    // ── Helper ──────────────────────────────────────────
    private function formatUser(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'username'   => $user->username,
            'email'      => $user->email,
            'role'       => $user->role,
            'nis'        => $user->nis,
            'kelas'      => $user->kelas,
            'no_hp'      => $user->no_hp,
            'is_active'  => $user->is_active,
            'created_at' => $user->created_at?->format('Y-m-d'),
        ];
    }
}
