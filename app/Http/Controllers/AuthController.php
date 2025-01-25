<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Kullanıcı Kayıt
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'integer|in:1,2', // Sadece 1 ve 2 değerlerine izin veriyoruz.
            'password' => 'required|string|confirmed|min:8',
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);

        // Kullanıcıyı oluştururken role_id'yi manuel olarak atıyoruz.
        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    // Kullanıcı Giriş
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }
    // Token Yenileme
    public function refresh()
    {
        return $this->respondWithToken(JWTAuth::refresh());
    }
    // Çıkış İşlemi
    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    // Token Yanıtı
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
