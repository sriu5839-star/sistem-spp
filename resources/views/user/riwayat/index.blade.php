@extends('layouts.user')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Transaksi')

@section('content')
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900">Daftar Transaksi Pembayaran</h3>
                <p class="text-sm text-gray-600 mt-1">Hanya menampilkan transaksi milik akun Anda.</p>
            </div>
            <form method="GET" action="{{ route('user.riwayat.index') }}" class="flex items-center gap-3">
                <div>
                    <label for="bulan" class="block text-xs font-semibold text-gray-500 mb-1">Bulan</label>
                    <select id="bulan" name="bulan" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        @foreach($bulanList as $bln)
                            <option value="{{ $bln }}" @selected(request('bulan') === $bln)>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tahun" class="block text-xs font-semibold text-gray-500 mb-1">Tahun</label>
                    <select id="tahun" name="tahun" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua</option>
                        @foreach($tahunList as $th)
                            <option value="{{ $th }}" @selected((string)request('tahun') === (string)$th)>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                    Terapkan
                </button>
                <a href="{{ route('user.riwayat.cetak', array_filter(['bulan' => request('bulan'), 'tahun' => request('tahun'), 'auto' => '1'])) }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 text-sm">
                    Cetak Semua
                </a>
            </form>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @if(!$siswa)
            <div class="p-6 text-center">
                <p class="text-gray-600 mb-3">
                    Data siswa belum terhubung.
                </p>
                <a href="{{ route('user.siswa.link') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Hubungkan Data Siswa
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan/Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal SPP</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Bayar</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($pembayaran as $p)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $p->bulan_dibayar }} {{ $p->tahun_dibayar }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($p->spp->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        LUNAS
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($p->tgl_bayar)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('user.riwayat.nota', $p->id) }}" class="text-indigo-600 hover:text-indigo-900" target="_blank">
                                        <i class="fas fa-print"></i> Cetak Nota
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada data transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($pembayaran instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="p-4 border-t border-gray-200">
                    {{ $pembayaran->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
