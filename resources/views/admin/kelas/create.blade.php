@extends('layouts.admin')

@section('title', 'Tambah Kelas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tambah Kelas</h1>
        <p class="text-gray-600 mt-1">Tambah data kelas baru</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.kelas.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Nama Kelas -->
                <div>
                    <label for="nama_kelas" class="block text-sm font-medium text-gray-700 mb-2">Nama Kelas *</label>
                    <input 
                        type="text" 
                        id="nama_kelas" 
                        name="nama_kelas" 
                        value="{{ old('nama_kelas') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: X RPL 1, XI TKJ 2, XII MM 1"
                    >
                    @error('nama_kelas')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kompetensi Keahlian -->
                <div>
                    <label for="kompetensi_keahlian" class="block text-sm font-medium text-gray-700 mb-2">Kompetensi Keahlian *</label>
                    <input 
                        type="text" 
                        id="kompetensi_keahlian" 
                        name="kompetensi_keahlian" 
                        value="{{ old('kompetensi_keahlian') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Contoh: Rekayasa Perangkat Lunak, Teknik Komputer dan Jaringan"
                    >
                    @error('kompetensi_keahlian')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.kelas.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

