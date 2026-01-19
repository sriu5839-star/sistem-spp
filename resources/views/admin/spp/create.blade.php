@extends('layouts.admin')

@section('title', 'Tambah SPP')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Tambah SPP</h1>
        <p class="text-gray-600 mt-1">Tambah data SPP baru</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.spp.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <!-- Tahun -->
                <div>
                    <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">Tahun *</label>
                    <input 
                        type="number" 
                        id="tahun" 
                        name="tahun" 
                        value="{{ old('tahun', date('Y')) }}"
                        required
                        min="2000"
                        max="2099"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan tahun SPP"
                    >
                    @error('tahun')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nominal -->
                <div>
                    <label for="nominal" class="block text-sm font-medium text-gray-700 mb-2">Nominal (Rp) *</label>
                    <input 
                        type="number" 
                        id="nominal" 
                        name="nominal" 
                        value="{{ old('nominal') }}"
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan nominal SPP"
                    >
                    @error('nominal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.spp.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
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

