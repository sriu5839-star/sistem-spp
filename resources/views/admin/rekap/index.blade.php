@extends('layouts.admin')

@section('title', 'Rekap Pembayaran Per Kelas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Rekap Pembayaran Per Kelas</h1>
        <p class="text-gray-600 mt-1">Rekap pembayaran SPP berdasarkan kelas</p>
        @if($sppAktif)
            <p class="text-sm text-gray-500 mt-1">Tahun SPP Aktif: <span class="font-semibold">{{ $sppAktif->tahun }}</span> - Nominal: <span class="font-semibold">Rp {{ number_format($sppAktif->nominal, 0, ',', '.') }}</span></p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="w-60">
                <select name="kelas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                Filter
            </button>
            @if(request('kelas'))
                <a href="{{ route('admin.rekap.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Rekap Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa Lunas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa Belum Lunas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pembayaran Masuk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tunggakan</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rekap as $r)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $r['kelas']->nama_kelas }}</p>
                                    <p class="text-xs text-gray-500">{{ $r['kelas']->kompetensi_keahlian }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $r['total_siswa'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $r['siswa_lunas'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ $r['siswa_belum_lunas'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                                Rp {{ number_format($r['total_pembayaran'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-red-600">
                                Rp {{ number_format($r['total_tunggakan'], 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada data kelas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary Card -->
    @if(count($rekap) > 0)
        @php
            $totalSiswaAll = array_sum(array_column($rekap, 'total_siswa'));
            $totalLunasAll = array_sum(array_column($rekap, 'siswa_lunas'));
            $totalBelumLunasAll = array_sum(array_column($rekap, 'siswa_belum_lunas'));
            $totalPembayaranAll = array_sum(array_column($rekap, 'total_pembayaran'));
            $totalTunggakanAll = array_sum(array_column($rekap, 'total_tunggakan'));
        @endphp
        <div class="bg-blue-50 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Keseluruhan</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSiswaAll }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Siswa Lunas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $totalLunasAll }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Siswa Belum Lunas</p>
                    <p class="text-2xl font-bold text-red-600">{{ $totalBelumLunasAll }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Pembayaran</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPembayaranAll, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Tunggakan</p>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalTunggakanAll, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
