<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() { return view('auth.login'); }
    public function login(Request $r) {
        $cred = $r->validate(['email'=>'required|email','password'=>'required']);
        if (Auth::attempt($cred, $r->boolean('remember'))) {
            $r->session()->regenerate();
            if (! $r->user()->aktif) { Auth::logout(); return back()->withErrors(['email'=>'Akun tidak aktif']); }
            return redirect()->intended('/dashboard');
        }
        return back()->withErrors(['email'=>'Email atau password salah'])->onlyInput('email');
    }
    public function logout(Request $r) {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect('/login');
    }
}
