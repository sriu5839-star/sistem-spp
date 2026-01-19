@extends('layouts.petugas')

@section('title', 'Dashboard Petugas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Petugas</h1>
        <p class="text-gray-600 mt-1">Welcome Back, {{ Auth::user()->username }}</p>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Pembayaran -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pembayaran Masuk</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalPembayaran ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalTransaksi ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pembayaran Hari Ini -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pembayaran Hari Ini</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $pembayaranHariIni ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($totalHariIni ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Action -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Aksi Cepat</p>
                    <a href="{{ route('petugas.pembayaran.create') }}" class="mt-2 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                        Input Pembayaran
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Activity Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Grafik Pemasukan -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Grafik Pemasukan</h3>
                    <p class="text-sm text-gray-500">Pembayaran per Bulan</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Last 6 Months</p>
                </div>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2">
                @if(isset($grafikPemasukan) && $grafikPemasukan->count() > 0)
                    @php
                        $maxValue = $grafikPemasukan->max('total') ?: 1;
                    @endphp
                    @foreach($grafikPemasukan as $index => $data)
                        @php
                            $height = ($data->total / $maxValue) * 100;
                            $bulanNama = date('M', mktime(0, 0, 0, $data->bulan, 1, $data->tahun));
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-blue-600 rounded-t" style="height: {{ max($height, 10) }}%"></div>
                            <span class="text-xs text-gray-600 mt-2">{{ $bulanNama }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="w-full text-center text-gray-500 py-10">
                        <p>Belum ada data pembayaran</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Notifikasi Transaksi Terbaru -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Notifikasi Transaksi Terbaru</h3>
            <div class="space-y-4">
                @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                    @foreach($transaksiTerbaru as $transaksi)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">
                                    Pembayaran oleh {{ $transaksi->siswa->nama ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }} - {{ $transaksi->tgl_bayar->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-gray-500 py-4">
                        <p class="text-sm">Belum ada transaksi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tabel Pembayaran Terbaru -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Pembayaran Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan/Tahun</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Bayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($transaksiTerbaru) && $transaksiTerbaru->count() > 0)
                        @foreach($transaksiTerbaru as $transaksi)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $transaksi->siswa->nama ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->siswa->kelas->nama_kelas ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->bulan_dibayar }} / {{ $transaksi->tahun_dibayar }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($transaksi->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaksi->status === 'Lunas' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaksi->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaksi->tgl_bayar->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data pembayaran</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
