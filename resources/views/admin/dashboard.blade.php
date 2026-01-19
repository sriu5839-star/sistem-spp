@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
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
                    @if(isset($sppAktif))
                        <p class="text-xs text-gray-500 mt-1">SPP {{ $sppAktif->tahun }}: Rp {{ number_format($sppAktif->nominal, 0, ',', '.') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Siswa</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalSiswa ?? 0 }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua kelas</p>
                </div>
            </div>
        </div>

        <!-- Siswa Lunas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Siswa Lunas</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $siswaLunas ?? 0 }}</p>
                    @if(isset($totalSiswa) && $totalSiswa > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(($siswaLunas / $totalSiswa) * 100, 1) }}% dari total</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Siswa Belum Lunas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Siswa Belum Lunas</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $siswaBelumLunas ?? 0 }}</p>
                    @if(isset($totalSiswa) && $totalSiswa > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ number_format(($siswaBelumLunas / $totalSiswa) * 100, 1) }}% dari total</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Tambahan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Pembayaran Bulan Ini -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pembayaran Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">Rp {{ number_format($totalPembayaranBulanIni ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $jumlahTransaksiBulanIni ?? 0 }} transaksi</p>
                </div>
            </div>
        </div>

        <!-- Pembayaran Hari Ini -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pembayaran Hari Ini</p>
                    <p class="text-2xl font-bold text-blue-600 mt-2">Rp {{ number_format($totalPembayaranHariIni ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ now()->format('d F Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">{{ \App\Models\Pembayaran::count() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Semua waktu</p>
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
                    <p class="text-sm text-gray-500">Pembayaran per Bulan (6 Bulan Terakhir)</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Total: Rp {{ number_format($grafikPemasukan->sum('total'), 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="space-y-4">
                @php
                    $grafikData = isset($grafikPemasukan) ? $grafikPemasukan : collect();
                    $maxValue = $grafikData->count() > 0 ? max($grafikData->max('total') ?? 0, 1) : 1;
                @endphp
                
                @if($grafikData->count() > 0)
                    @foreach($grafikData as $index => $data)
                        @php
                            $bulanSingkat = date('M', mktime(0, 0, 0, $data->bulan ?? 1, 1, $data->tahun ?? date('Y')));
                            $totalValue = (float) ($data->total ?? 0);
                            $width = $maxValue > 0 ? ($totalValue / $maxValue) * 100 : 0;
                        @endphp
                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium text-gray-700 w-24">{{ $bulanSingkat }} {{ $data->tahun ?? date('Y') }}</span>
                                <span class="text-gray-600 font-semibold">Rp {{ number_format($totalValue, 0, ',', '.') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden relative">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-6 rounded-full flex items-center justify-end pr-2 transition-all duration-500" 
                                     style="width: {{ max($width, 2) }}%; min-width: 2%;">
                                    @if($totalValue > 0 && $width > 15)
                                        <span class="text-xs font-semibold text-white">{{ number_format(($totalValue / $maxValue) * 100, 0) }}%</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="w-full py-10 flex items-center justify-center text-gray-500">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p>Belum ada data pembayaran untuk 6 bulan terakhir</p>
                        </div>
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
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transaksi->status === 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
