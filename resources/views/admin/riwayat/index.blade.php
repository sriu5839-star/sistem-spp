@extends('layouts.admin')

@section('title', 'Riwayat Pembayaran')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rekap Pembayaran Per Kelas</h1>
            <p class="text-gray-600 mt-1">Format rekapitulasi iuran bulanan siswa</p>
            @php
                $kelasDipilih = request('kelas') ? $kelas->firstWhere('id', (int) request('kelas')) : null;
            @endphp
            <p class="text-gray-600 mt-1">
                Kelas: <span class="font-semibold">{{ $kelasDipilih ? $kelasDipilih->nama_kelas : 'Semua Kelas' }}</span>
            </p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.riwayat.index') }}" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px] max-w-xs">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Cari nama atau NISN..." 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
            </div>
            
            <div class="w-40">
                <select name="kelas" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Filter
            </button>

            <button type="button" onclick="window.print()" class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-gray-900 transition flex items-center gap-2 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Rekap
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border-b border-r border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-700 w-12 text-center">No</th>
                        <th class="border-b border-r border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-700 text-left min-w-[200px]">Nama Siswa</th>
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $bln)
                            <th class="border-b border-r border-gray-300 px-2 py-3 text-xs font-bold uppercase text-gray-700 text-center">{{ $bln }}</th>
                        @endforeach
                        <th class="border-b border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-800 bg-gray-100 text-center">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($riwayat as $index => $siswa)
                        @php $totalSiswa = 0; @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="border-r border-gray-300 px-4 py-3 text-sm text-center text-gray-600">
                                {{ $riwayat->firstItem() + $index }}
                            </td>
                            <td class="border-r border-gray-300 px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50/50">
                                {{ $siswa->nama }}
                            </td>
                            
                            @foreach($bulanList as $bulan)
                                @php
                                    $pembayaran = $siswa->pembayaran->where('bulan_dibayar', $bulan)->first();
                                    $nominal = $pembayaran ? $pembayaran->jumlah_bayar : 0;
                                    $totalSiswa += $nominal;
                                @endphp
                                <td class="border-r border-gray-300 px-2 py-3 text-sm text-center">
                                    @if($nominal > 0)
                                        <span class="text-green-600 font-bold">{{ number_format($nominal/1000, 0) }}k</span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-4 py-3 text-sm font-bold bg-gray-100 text-center text-gray-900">
                                {{ number_format($totalSiswa, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="px-6 py-12 text-center text-gray-500 italic">
                                Data siswa tidak ditemukan untuk kriteria ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 no-print">
        {{ $riwayat->links() }}
    </div>
</div>

<style>
    /* CSS Khusus agar saat di-print tampilan tetap rapi dan tombol hilang */
    @media print {
        .no-print, form, header, aside, .sidebar {
            display: none !important;
        }
        body {
            background: white !important;
            padding: 0 !important;
        }
        .bg-white {
            box-shadow: none !important;
            border: none !important;
        }
        table {
            width: 100% !important;
            border: 1px solid #000 !important;
        }
        th, td {
            border: 1px solid #000 !important;
            color: black !important;
        }
    }
</style>
@endsection
