@extends('layouts.user')

@section('header', 'Hubungkan Data Siswa')

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-xl mx-auto">
    @if(session('success'))
        <div class="mb-4 rounded-md bg-green-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-5a1 1 0 112 0 1 1 0 01-2 0zm1-9a1 1 0 00-.894.553l-3 6A1 1 0 007 12h6a1 1 0 00.894-1.447l-3-6A1 1 0 0010 4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Terjadi kesalahan pada input Anda.
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($siswa)
        <div class="rounded-lg bg-blue-50 p-4 border border-blue-200 mb-4">
            <p class="text-sm text-blue-800">
                Akun Anda sudah terhubung dengan data siswa: <span class="font-semibold">{{ $siswa->nama }}</span>.
            </p>
        </div>
        <div class="flex justify-end">
            <a href="{{ route('user.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                Kembali ke Dashboard
            </a>
        </div>
    @else
        <form action="{{ route('user.siswa.link.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="nisn" class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
                <input
                    type="text"
                    id="nisn"
                    name="nisn"
                    value="{{ old('nisn') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    placeholder="Masukkan NISN Anda"
                >
                <p class="mt-1 text-xs text-gray-500">Pastikan NISN sesuai data di sekolah.</p>
            </div>

            <div class="pt-4 flex items-center justify-between">
                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                    Kembali ke Dashboard
                </a>
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 text-sm font-medium transition-colors">
                    Hubungkan Data Siswa
                </button>
            </div>
        </form>
    @endif
</div>
@endsection