@extends('layouts.petugas')

@section('title', 'Input Pembayaran SPP')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Input Pembayaran SPP</h1>
        <p class="text-gray-600 mt-1">Input pembayaran SPP siswa</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('petugas.pembayaran.store') }}" method="POST" id="pembayaranForm">
            @csrf

            <div class="space-y-4">
                <!-- Pencarian Siswa -->
                <div>
                    <label for="search_siswa" class="block text-sm font-medium text-gray-700 mb-2">Cari Siswa (NISN / Nama) *</label>
                    <input 
                        type="text" 
                        id="search_siswa" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ketik NISN atau nama siswa..."
                        autocomplete="off"
                    >
                    <div id="siswa-results" class="hidden mt-2 border border-gray-200 rounded-lg bg-white shadow-lg max-h-60 overflow-y-auto"></div>
                    <input type="hidden" id="id_siswa" name="id_siswa" required>
                    <div id="siswa-info" class="mt-2 p-3 bg-blue-50 rounded-lg hidden">
                        <p class="text-sm font-medium text-gray-900" id="siswa-nama"></p>
                        <p class="text-xs text-gray-600" id="siswa-kelas"></p>
                    </div>
                    @error('id_siswa')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- SPP -->
                <div>
                    <label for="id_spp" class="block text-sm font-medium text-gray-700 mb-2">Tahun SPP *</label>
                    <select 
                        id="id_spp" 
                        name="id_spp" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Tahun SPP</option>
                        @foreach($spp as $s)
                            <option value="{{ $s->id }}" data-nominal="{{ $s->nominal }}">
                                {{ $s->tahun }} - Rp {{ number_format($s->nominal, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_spp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bulan Dibayar -->
                <div>
                    <label for="bulan_dibayar" class="block text-sm font-medium text-gray-700 mb-2">Bulan Dibayar *</label>
                    <select 
                        id="bulan_dibayar" 
                        name="bulan_dibayar" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Pilih Bulan</option>
                        <option value="Januari">Januari</option>
                        <option value="Februari">Februari</option>
                        <option value="Maret">Maret</option>
                        <option value="April">April</option>
                        <option value="Mei">Mei</option>
                        <option value="Juni">Juni</option>
                        <option value="Juli">Juli</option>
                        <option value="Agustus">Agustus</option>
                        <option value="September">September</option>
                        <option value="Oktober">Oktober</option>
                        <option value="November">November</option>
                        <option value="Desember">Desember</option>
                    </select>
                    @error('bulan_dibayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tahun Dibayar -->
                <div>
                    <label for="tahun_dibayar" class="block text-sm font-medium text-gray-700 mb-2">Tahun Dibayar *</label>
                    <input 
                        type="number" 
                        id="tahun_dibayar" 
                        name="tahun_dibayar" 
                        value="{{ old('tahun_dibayar', date('Y')) }}"
                        required
                        min="2020"
                        max="2099"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('tahun_dibayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Bayar -->
                <div>
                    <label for="tgl_bayar" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Bayar *</label>
                    <input 
                        type="date" 
                        id="tgl_bayar" 
                        name="tgl_bayar" 
                        value="{{ old('tgl_bayar', date('Y-m-d')) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    @error('tgl_bayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Jumlah Bayar -->
                <div>
                    <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Bayar (Rp) *</label>
                    <input 
                        type="number" 
                        id="jumlah_bayar" 
                        name="jumlah_bayar" 
                        value="{{ old('jumlah_bayar') }}"
                        required
                        min="0"
                        step="0.01"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Masukkan jumlah pembayaran"
                    >
                    <p class="mt-1 text-xs text-gray-500">Nominal SPP: <span id="nominal-spp">-</span></p>
                    @error('jumlah_bayar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
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
                                        <button type="submit" class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                            Bayar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


            <!-- Actions -->
            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end">
                <a href="{{ route('petugas.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
            </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search_siswa');
    const searchInput = document.getElementById('search_siswa');
    const resultsDiv = document.getElementById('siswa-results');
    const siswaIdInput = document.getElementById('id_siswa');
    const siswaInfo = document.getElementById('siswa-info');
    const siswaNama = document.getElementById('siswa-nama');
    const siswaKelas = document.getElementById('siswa-kelas');
    const sppSelect = document.getElementById('id_spp');
    const nominalSpp = document.getElementById('nominal-spp');
    const bulanSelect = document.getElementById('bulan_dibayar');
    const tahunInput = document.getElementById('tahun_dibayar');
    const jumlahInput = document.getElementById('jumlah_bayar');
    const summaryBulan = document.getElementById('summary-bulan');
    const summaryTahun = document.getElementById('summary-tahun');
    const summaryNominal = document.getElementById('summary-nominal');
    const summaryKet = document.getElementById('summary-ket');

    // Update nominal SPP saat dipilih
    sppSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const nominal = selected.getAttribute('data-nominal');
        if (nominal) {
            nominalSpp.textContent = 'Rp ' + parseInt(nominal).toLocaleString('id-ID');
        } else {
            nominalSpp.textContent = '-';
        }
        updateSummary();
    });

    bulanSelect.addEventListener('change', updateSummary);
    tahunInput.addEventListener('input', updateSummary);
    jumlahInput.addEventListener('input', updateSummary);

    function updateSummary() {
        const bulan = bulanSelect.value || '-';
        const tahun = tahunInput.value || '-';
        const selected = sppSelect.options[sppSelect.selectedIndex];
        const nominal = selected ? parseFloat(selected.getAttribute('data-nominal') || '0') : 0;
        const jumlah = parseFloat(jumlahInput.value || '0');

        summaryBulan.textContent = bulan;
        summaryTahun.textContent = tahun;
        summaryNominal.textContent = 'Rp ' + nominal.toLocaleString('id-ID');

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

    // Initial fill
    updateSummary();

    // Search siswa
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length < 2) {
            resultsDiv.classList.add('hidden');
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`{{ route('petugas.pembayaran.search-siswa') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        resultsDiv.innerHTML = '';
                        data.forEach(siswa => {
                            const item = document.createElement('div');
                            item.className = 'p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100';
                            item.innerHTML = `
                                <p class="font-medium text-gray-900">${siswa.nama}</p>
                                <p class="text-sm text-gray-500">NISN: ${siswa.nisn} | ${siswa.kelas ? siswa.kelas.nama_kelas : '-'}</p>
                            `;
                            item.addEventListener('click', () => {
                                selectSiswa(siswa);
                            });
                            resultsDiv.appendChild(item);
                        });
                        resultsDiv.classList.remove('hidden');
                    } else {
                        resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500">Tidak ada hasil</div>';
                        resultsDiv.classList.remove('hidden');
                    }
                });
        }, 300);
    });

    function selectSiswa(siswa) {
        siswaIdInput.value = siswa.id;
        searchInput.value = `${siswa.nama} (${siswa.nisn})`;
        siswaNama.textContent = siswa.nama;
        siswaKelas.textContent = `NISN: ${siswa.nisn} | Kelas: ${siswa.kelas ? siswa.kelas.nama_kelas : '-'}`;
        siswaInfo.classList.remove('hidden');
        resultsDiv.classList.add('hidden');
    }

    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection
