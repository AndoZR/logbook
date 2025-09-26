<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseFormatter;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // MOBILE SESSION
    // Login
    public function login(Request $request)
    {
        try {
            // 🔹 Validasi input
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ], [
                'username.required' => 'Username wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // 🔹 Ambil credentials dari input yang tervalidasi
            $credentials = [
                'username' => $validated['username'],
                'password' => $validated['password']
            ];

            // 🔹 Cek login
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('auth_token')->plainTextToken;

                return ResponseFormatter::success($token, 'Login Berhasil');
            }

            // 🔹 Jika gagal login
            return ResponseFormatter::error(null, 'Username atau Password Salah', 401);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            // 🔹 Catat error ke log
            return ResponseFormatter::error(null, $e->getMessage(), 500);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        return ResponseFormatter::success(null, 'Logout berhasil');
    }

    public function profile(Request $request)
    {
        $siswa = Auth::user()->siswa()->get(); // kalau hasMany

        return ResponseFormatter::success($siswa, 'Data siswa berhasil diambil');  
    }

    // WEB SESSION
    // Register
    public function registerWeb(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect('dashboard')->with(['message' => 'Register & login success', 'user' => $user]);
    }

    // Login
    public function loginWeb(Request $request)
    {
        try {
            // 🔹 Validasi input
            $validated = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ], [
                'nik.required' => 'Email wajib diisi.',
                'password.required' => 'Password wajib diisi.',
            ]);

            // 🔹 Ambil credentials dari input yang tervalidasi
            $credentials = [
                'email' => $validated['email'],
                'password' => $validated['password']
            ];

            // 🔹 Cek login
            if (Auth::attempt($credentials)) {
                $user = Auth::user();

                return redirect('dashboard')->with(['message' => 'Login success', 'user' => $user]);
            }

            // 🔹 Jika gagal login
            return back()->withErrors([
                'nik' => 'Email atau password salah.',
            ]);

        } catch (Exception $e) {
            // 🔹 Catat error ke log
            Log::error('Login error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server saat login',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Logout
    public function logoutWeb(Request $request)
    {
        Auth::logout();
        return view('Auth.Login');
    }
}
