@extends('layouts.app')

@section('title', 'Register Siswa')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Daftar Akun Siswa</h1>
    
    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Buat Akun Baru</h2>
        <p class="text-sm text-gray-600 mb-6">Isi data di bawah untuk mendaftar.</p>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="username" class="block text-sm font-medium text-gray-900 mb-2">Nama Lengkap</label>
                <input
                    id="username"
                    name="username"
                    type="text"
                    value="{{ old('username') }}"
                    required
                    autofocus
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Nama siswa"
                >
            </div>

            <div>
                <label for="nisn" class="block text-sm font-medium text-gray-900 mb-2">NISN</label>
                <input
                    id="nisn"
                    name="nisn"
                    type="text"
                    value="{{ old('nisn') }}"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Nomor Induk Siswa Nasional"
                >
                <small id="nisn-feedback" class="text-xs mt-1 block h-4"></small>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="email@example.com"
                >
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Minimal 6 karakter"
                >
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">Konfirmasi Password</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Ulangi password"
                >
            </div>

            <div>
                <button
                    type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Daftar
                </button>
            </div>

            <div class="text-center text-sm text-gray-600">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">Login di sini</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nisnInput = document.getElementById('nisn');
        const feedbackElement = document.getElementById('nisn-feedback');
        const usernameInput = document.getElementById('username');
        let timeout = null;

        nisnInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const nisn = this.value;
            
            if (nisn.length < 5) {
                feedbackElement.textContent = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('check-nisn') }}?nisn=${nisn}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            feedbackElement.textContent = `Nama Siswa: ${data.nama}`;
                            feedbackElement.className = 'text-xs mt-1 block h-4 text-green-600 font-bold';
                            // Auto fill username if empty
                            if (!usernameInput.value) {
                                usernameInput.value = data.nama;
                            }
                        } else {
                            feedbackElement.textContent = data.message;
                            feedbackElement.className = 'text-xs mt-1 block h-4 text-red-600';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        feedbackElement.textContent = '';
                    });
            }, 500);
        });
    });
</script>
@endsection

