<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login pengguna dan berikan token.
     */
    public function login(Request $request)
    {
        // Validasi input login
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|max:8',
        ]);

        // Temukan pengguna berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Periksa apakah pengguna ditemukan dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 401);
        }

        // Hapus token lama, jika ada
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Logout pengguna dan hapus token.
     */
    public function logout(Request $request)
    {
        // Hapus token pengguna yang sedang aktif
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Mendapatkan data pengguna yang sedang login.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}
