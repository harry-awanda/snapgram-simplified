<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class authController extends Controller {
  // Menampilkan halaman login
  public function showLoginForm() {
    return view('auth.login');
  }
  // Menghandle postLogin
  public function postLogin(Request $request) {
    $request->validate([
      'username' => 'required|string',
      'password' => 'required|string',
    ]);
    $credentials = $request->only('username', 'password');
    if (Auth::attempt($credentials)) {
      // Login berhasil
      return redirect()->intended('/home')->with('success', 'Anda berhasil login.');
    }
    // Login gagal
    return back()->withErrors([
      'username' => 'Username atau password salah.',
    ]);
  }
  // Menampilkan halaman registrasi
  public function showRegistrationForm() {
    return view('auth.register');
  }
  // Menghandle proses registrasi
  public function register(Request $request) {
    $request->validate([
      'username' => 'required|string|unique:users,username|max:255',
      'password' => 'required|string|confirmed|min:8', // Memastikan password sesuai konfirmasi
    ]);
    // Membuat pengguna baru
    User::create([
      'username' => $request->username,
      'password' => Hash::make($request->password), // Hash password
    ]);
   // Mengalihkan ke halaman login setelah registrasi berhasil
    return redirect()->route('login')->with('success', 'Anda berhasil mendaftar, silakan login.');
  }
  // Menghandle logout
  public function logout(Request $request) {
    Auth::logout();
    return redirect()->route('login')->with('success', 'Anda berhasil logout.');
  }
}