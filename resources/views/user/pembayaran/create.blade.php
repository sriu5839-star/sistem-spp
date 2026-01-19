<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembayaran SPP</title>
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
                
                <a href="#" class="flex items-center p-3 rounded-lg hover:bg-indigo-700 transition">
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
                <h2 class="text-xl font-semibold text-gray-800">Pembayaran SPP</h2>
                <div class="flex items-center">
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full uppercase">
                        {{ Auth::user()->role === 'user' ? 'SISWA' : strtoupper(Auth::user()->role) }}
                    </span>
                </div>
            </header>

            <div class="p-6 overflow-y-auto">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-2xl mx-auto">
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

                    <div class="mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Form Pembayaran SPP</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Silakan isi form berikut untuk melakukan pembayaran SPP.
                        </p>
                    </div>

                    @if(!$siswa)
                        <div class="rounded-lg bg-yellow-50 p-4 border border-yellow-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l6.518 11.59C19.021 16.92 18.264 18 17.018 18H2.982c-1.246 0-2.003-1.08-1.243-2.31l6.518-11.59zM11 14a1 1 0 10-2 0 1 1 0 002 0zm-1-2a1 1 0 01-1-1V8a1 1 0 112 0v3a1 1 0 01-1 1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-800">
                                        Data siswa untuk akun ini belum terhubung. Silakan hubungi admin sekolah untuk menghubungkan akun dengan data siswa.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('user.pembayaran.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Siswa</label>
                                <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    {{ $siswa->nama }}
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                                <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </div>
                            </div>

                            <div>
                                <label for="id_spp" class="block text-sm font-medium text-gray-700 mb-1">Tahun SPP</label>
                                <select
                                    id="id_spp"
                                    name="id_spp"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Pilih Tahun SPP</option>
                                    @foreach($spp as $item)
                                        <option value="{{ $item->id }}" data-nominal="{{ $item->nominal }}" @selected(old('id_spp') == $item->id)>
                                            {{ $item->tahun }} - Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="bulan_dibayar" class="block text-sm font-medium text-gray-700 mb-1">Bulan Dibayar</label>
                                <select
                                    id="bulan_dibayar"
                                    name="bulan_dibayar"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">Pilih Bulan</option>
                                    <option value="Januari" @selected(old('bulan_dibayar') === 'Januari')>Januari</option>
                                    <option value="Februari" @selected(old('bulan_dibayar') === 'Februari')>Februari</option>
                                    <option value="Maret" @selected(old('bulan_dibayar') === 'Maret')>Maret</option>
                                    <option value="April" @selected(old('bulan_dibayar') === 'April')>April</option>
                                    <option value="Mei" @selected(old('bulan_dibayar') === 'Mei')>Mei</option>
                                    <option value="Juni" @selected(old('bulan_dibayar') === 'Juni')>Juni</option>
                                    <option value="Juli" @selected(old('bulan_dibayar') === 'Juli')>Juli</option>
                                    <option value="Agustus" @selected(old('bulan_dibayar') === 'Agustus')>Agustus</option>
                                    <option value="September" @selected(old('bulan_dibayar') === 'September')>September</option>
                                    <option value="Oktober" @selected(old('bulan_dibayar') === 'Oktober')>Oktober</option>
                                    <option value="November" @selected(old('bulan_dibayar') === 'November')>November</option>
                                    <option value="Desember" @selected(old('bulan_dibayar') === 'Desember')>Desember</option>
                                </select>
                            </div>

                            <div>
                                <label for="tahun_dibayar" class="block text-sm font-medium text-gray-700 mb-1">Tahun Dibayar</label>
                                <input
                                    type="number"
                                    id="tahun_dibayar"
                                    name="tahun_dibayar"
                                    value="{{ old('tahun_dibayar', date('Y')) }}"
                                    required
                                    min="2020"
                                    max="2099"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label for="tgl_bayar" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                                <input
                                    type="date"
                                    id="tgl_bayar"
                                    name="tgl_bayar"
                                    value="{{ old('tgl_bayar', date('Y-m-d')) }}"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                            </div>

                            <div>
                                <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                                <input
                                    type="number"
                                    id="jumlah_bayar"
                                    name="jumlah_bayar"
                                    value="{{ old('jumlah_bayar') }}"
                                    required
                                    min="0"
                                    step="0.01"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    placeholder="Masukkan jumlah pembayaran"
                                >
                                <p class="mt-1 text-xs text-gray-500">Nominal SPP: <span id="nominal-spp">-</span></p>
                            </div>

                            <!-- Ringkasan Pembayaran (Tabel) -->
                            <div class="mt-6">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Ringkasan</h3>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full border border-gray-200 rounded-lg">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">No</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">Bulan</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">Tahun</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">Nominal</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">Ket</th>
                                                <th class="px-4 py-2 text-left text-xs font-bold text-gray-600 border-b">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-700 border-t">1</td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-t"><span id="summary-bulan">-</span></td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-t"><span id="summary-tahun">-</span></td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-t"><span id="summary-nominal">Rp 0</span></td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-t">
                                                    <span id="summary-ket" class="inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-700">-</span>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-900 border-t">
                                                    <button type="submit" class="px-4 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                                        Bayar
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="pt-4 flex items-center justify-between">
                                <a href="{{ route('user.dashboard') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                                    Kembali ke Dashboard
                                </a>
                                <!-- Submit via tombol Bayar di tabel -->
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </main>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sppSelect = document.getElementById('id_spp');
    const nominalSpp = document.getElementById('nominal-spp');
    const bulanSelect = document.getElementById('bulan_dibayar');
    const tahunInput = document.getElementById('tahun_dibayar');
    const jumlahInput = document.getElementById('jumlah_bayar');
    const summaryBulan = document.getElementById('summary-bulan');
    const summaryTahun = document.getElementById('summary-tahun');
    const summaryNominal = document.getElementById('summary-nominal');
    const summaryKet = document.getElementById('summary-ket');

    function updateSummary() {
        const bulan = bulanSelect.value || '-';
        const tahun = tahunInput.value || '-';
        const selected = sppSelect.options[sppSelect.selectedIndex];
        const nominal = selected ? parseFloat(selected.getAttribute('data-nominal') || '0') : 0;
        const jumlah = parseFloat(jumlahInput.value || '0');

        summaryBulan.textContent = bulan;
        summaryTahun.textContent = tahun;
        summaryNominal.textContent = 'Rp ' + nominal.toLocaleString('id-ID');
        nominalSpp.textContent = nominal ? ('Rp ' + nominal.toLocaleString('id-ID')) : '-';

        if (nominal === 0) {
            summaryKet.textContent = '-';
            summaryKet.className = 'inline-block px-2 py-1 rounded text-xs bg-gray-100 text-gray-700';
        } else if (jumlah >= nominal) {
            summaryKet.textContent = 'L';
            summaryKet.className = 'inline-block px-2 py-1 rounded text-xs bg-green-100 text-green-800';
        } else {
            summaryKet.textContent = 'BL';
            summaryKet.className = 'inline-block px-2 py-1 rounded text-xs bg-red-100 text-red-800';
        }
    }

    sppSelect.addEventListener('change', updateSummary);
    bulanSelect.addEventListener('change', updateSummary);
    tahunInput.addEventListener('input', updateSummary);
    jumlahInput.addEventListener('input', updateSummary);
    updateSummary();
});
</script>
</body>
</html>
