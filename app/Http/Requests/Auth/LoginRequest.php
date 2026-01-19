<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     * Mengecek kredensial (email dan password) dari database menggunakan Auth::attempt()
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        // Cek rate limiting untuk mencegah brute force
        $this->ensureIsNotRateLimited();

        // Cek kredensial dari database
        // Auth::attempt() akan mengecek email dan password di tabel users
        // Jika cocok, user akan di-authenticate secara otomatis
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // Jika login gagal, hitung rate limit
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Ambil user yang sudah di-authenticate dari database
        $user = Auth::user();
        
        // Cek apakah user memiliki role yang valid
        if (!$user->role) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());
            
            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak memiliki role yang valid. Silakan hubungi administrator.',
            ]);
        }

        // Jika berhasil, clear rate limit
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
