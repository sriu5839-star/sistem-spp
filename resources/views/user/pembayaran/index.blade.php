<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <aside class="w-64 bg-indigo-900 text-white flex flex-col">
            <div class="p-6 text-2xl font-bold border-b border-indigo-800">
                Aplikasi SPP
            </div>
            
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('user.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-home mr-3"></i> Dashboard
                </a>
                
                <a href="{{ route('user.pembayaran.index') }}" class="flex items-center p-3 rounded-lg bg-indigo-800 text-white">
                    <i class="fas fa-credit-card mr-3"></i> Pembayaran
                </a>
                
                <a href="{{ route('user.riwayat.index') }}" class="flex items-center p-3 rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-history mr-3"></i> Riwayat Transaksi
                </a>
            </nav>

            <div class="p-4 border-t border-indigo-800">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="flex items-center w-full p-3 rounded-lg hover:bg-red-600 transition">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Pembayaran</h2>
                <div class="flex items-center">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">
                        {{ Auth::user()->role === 'user' ? 'SISWA' : strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </header>

            <div class="p-6 overflow-y-auto space-y-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Daftar Pembayaran Saya</h3>
                        <p class="text-sm text-gray-600 mt-1">Tampilkan transaksi pembayaran milik Anda.</p>
                    </div>
                    <a href="{{ route('user.pembayaran.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah Pembayaran
                    </a>
                </div>

                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                    <form method="GET" action="{{ route('user.pembayaran.index') }}" class="flex items-center gap-3">
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
                                        <th class="px-6 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if(isset($riwayat) && $riwayat->count() > 0)
                                        @foreach($riwayat as $row)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $row->bulan_dibayar }} / {{ $row->tahun_dibayar }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($row->spp->nominal ?? 0, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($row->jumlah_bayar, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $row->status === 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $row->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ optional($row->tgl_bayar)->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <a href="{{ route('user.riwayat.nota', ['pembayaran' => $row->id, 'auto' => '1']) }}" class="inline-block px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-xs">
                                                        Cetak Nota
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data transaksi</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4">
                            @if(method_exists($riwayat, 'links'))
                                {{ $riwayat->links('components.pagination') }}
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>

