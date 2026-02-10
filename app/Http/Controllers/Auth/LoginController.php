<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
   
    public function create()
    {
        return view('auth.login');
    }

   
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    
    public function store(LoginRequest $request)
    {
        
        $request->authenticate();

       
        $request->session()->regenerate();

       
        $user = Auth::user();

      
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'petugas') {
            return redirect()->route('petugas.dashboard');
        } elseif ($user->role === 'user') {
            return redirect()->route('user.dashboard');
        }

      
        return redirect()->intended('/');
    }

   
    public function checkNisn(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string'
        ]);

        $siswa = \App\Models\Siswa::where('nisn', $request->nisn)->first();

        if ($siswa) {
            return response()->json([
                'status' => 'success',
                'nama' => $siswa->nama
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Data siswa tidak ditemukan'
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'nisn' => ['required', 'string', 'exists:siswa,nisn'],
        ]);

        // Cek apakah NISN sudah terdaftar akun
        $siswa = \App\Models\Siswa::where('nisn', $validated['nisn'])->first();
        if ($siswa && $siswa->id_user) {
            return back()->withInput()->withErrors(['nisn' => 'NISN ini sudah terhubung dengan akun lain.']);
        }

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'user',
        ]);

        // Hubungkan siswa dengan user
        if ($siswa) {
            $siswa->update(['id_user' => $user->id]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }

   
    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
