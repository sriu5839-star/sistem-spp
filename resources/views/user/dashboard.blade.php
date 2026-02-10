@extends('layouts.user')

@section('header')
Selamat Datang, {{ Auth::user()->username }}
@endsection

@section('content')
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Rekap Pembayaran Saya</h3>
            <p class="text-sm text-gray-600 mt-1">
                Rekap iuran bulanan berdasarkan tahun pembayaran.
            </p>
            @if($siswa)
                <p class="text-sm text-gray-600 mt-1">
                    Siswa: <span class="font-semibold">{{ $siswa->nama }}</span>
                </p>
            @endif
        </div>
        @if($tahunList && $tahunList->count() > 0)
            <form method="GET" action="{{ route('user.dashboard') }}" class="flex items-center gap-3">
                <div>
                    <label for="tahun" class="block text-xs font-semibold text-gray-500 mb-1">Pilih Tahun</label>
                    <select id="tahun" name="tahun" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($tahunList as $tahun)
                            <option value="{{ $tahun }}" {{ (int) $tahunDipilih === (int) $tahun ? 'selected' : '' }}>
                                {{ $tahun }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        @endif
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        @if(!$siswa)
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-3">
                    Data siswa untuk akun ini belum terhubung.
                </p>
                <a href="{{ route('user.siswa.link') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors">
                    Hubungkan Data Siswa
                </a>
            </div>
        @elseif(!$tahunDipilih || $pembayaranTahun->isEmpty())
            <div class="p-6 text-center text-gray-500">
                Belum ada data pembayaran untuk tahun yang dipilih.
            </div>
        @else
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="border-b border-r border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-700 w-12 text-center">No</th>
                        <th class="border-b border-r border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-700 text-left min-w-[200px]">Nama Siswa</th>
                        @foreach(['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'] as $bln)
                            <th class="border-b border-r border-gray-300 px-2 py-3 text-xs font-bold uppercase text-gray-700 text-center">{{ $bln }}</th>
                        @endforeach
                        <th class="border-b border-gray-300 px-4 py-3 text-xs font-bold uppercase text-gray-800 bg-gray-100 text-center">Total {{ $tahunDipilih }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $totalTahun = 0; @endphp
                    <tr class="hover:bg-gray-50 transition">
                        <td class="border-r border-gray-300 px-4 py-3 text-sm text-center text-gray-600">
                            1
                        </td>
                        <td class="border-r border-gray-300 px-4 py-3 text-sm font-medium text-gray-900 bg-gray-50/50">
                            {{ $siswa->nama }}
                        </td>

                        @foreach($bulanList as $bulan)
                            @php
                                $pembayaran = $pembayaranTahun->firstWhere('bulan_dibayar', $bulan);
                                $nominal = $pembayaran ? $pembayaran->jumlah_bayar : 0;
                                $totalTahun += $nominal;
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
                            {{ number_format($totalTahun, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
