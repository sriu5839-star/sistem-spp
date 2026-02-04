@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Input Pembayaran SPP</h1>
        <p class="text-gray-600 mt-1">Pilih siswa, lalu lunaskan tagihan per bulan dan tahun</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.pembayaran.create') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="id_siswa" class="block text-sm font-medium text-gray-700 mb-2">Pilih Siswa</label>
                <select name="id_siswa" id="id_siswa" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}" {{ (isset($selectedSiswa) && $selectedSiswa->id == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} ({{ $s->nisn }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex items-end gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Cari Data
                </button>
                @if(isset($selectedSiswa))
                <a href="{{ route('admin.pembayaran.create') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 text-sm">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        (function(){
            const form = document.querySelector('form[action*="admin/pembayaran/create"]');
            const siswa = document.getElementById('id_siswa');
            if (form && siswa) {
                siswa.addEventListener('change', function(){
                    if (siswa.value) form.submit();
                });
            }
        })();
    </script>
    @endpush

    @if(isset($selectedSiswa) && count($tagihan) > 0)
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Rincian Tagihan: <span class="text-blue-600">{{ $selectedSiswa->nama }}</span></h2>
            <p class="text-sm text-gray-500">NISN {{ $selectedSiswa->nisn }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Bayar Tagihan</h3>
        <form action="{{ route('admin.pembayaran.store') }}" method="POST" id="payForm" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <input type="hidden" name="id_siswa" value="{{ $selectedSiswa->id }}">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan_dibayar" id="pf_bulan" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun_dibayar" id="pf_tahun" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                    @foreach($sppList as $spp)
                        <option value="{{ $spp->tahun }}" data-nominal="{{ $spp->nominal }}" {{ (isset($tahunFilter) && (int)$tahunFilter === (int)$spp->tahun) ? 'selected' : '' }}>{{ $spp->tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar (Rp)</label>
                <input type="number" name="jumlah_bayar" id="pf_jumlah" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" min="0" required>
                <p class="mt-1 text-xs text-gray-500">Nominal SPP: <span id="pf_nominal">-</span></p>
            </div>
            <div class="md:col-span-4 flex items-center gap-3">
                <span id="pf_status" class="text-sm px-3 py-1 rounded bg-gray-100 text-gray-700">Pilih data untuk cek status</span>
                <button type="submit" id="pf_submit" class="ml-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Lunaskan
                </button>
            </div>
        </form>
        @push('scripts')
        <script>
        (function(){
            const bulan = document.getElementById('pf_bulan');
            const tahun = document.getElementById('pf_tahun');
            const jumlah = document.getElementById('pf_jumlah');
            const nominalEl = document.getElementById('pf_nominal');
            const statusEl = document.getElementById('pf_status');
            const submitBtn = document.getElementById('pf_submit');
            function updateNominal() {
                const opt = tahun.options[tahun.selectedIndex];
                const nominal = opt ? parseInt(opt.getAttribute('data-nominal') || '0', 10) : 0;
                nominalEl.textContent = 'Rp ' + (nominal.toLocaleString('id-ID'));
                if (!jumlah.value || parseInt(jumlah.value,10) === 0) {
                    jumlah.value = nominal;
                }
            }
            function checkStatus() {
                const sid = {{ $selectedSiswa->id }};
                const bln = bulan.value;
                const th = tahun.value;
                if (!sid || !bln || !th) return;
                fetch(`{{ route('admin.pembayaran.check-status') }}?id_siswa=${encodeURIComponent(sid)}&bulan=${encodeURIComponent(bln)}&tahun=${encodeURIComponent(th)}`)
                    .then(r => r.json())
                    .then(d => {
                        statusEl.textContent = `Status: ${d.status} | Sisa: Rp ${Number(d.sisa||0).toLocaleString('id-ID')}`;
                        if (d.status === 'Lunas') {
                            submitBtn.textContent = 'Sudah Lunas';
                            submitBtn.disabled = true;
                            submitBtn.classList.add('opacity-60','cursor-not-allowed');
                        } else {
                            submitBtn.textContent = 'Lunaskan';
                            submitBtn.disabled = false;
                            submitBtn.classList.remove('opacity-60','cursor-not-allowed');
                            if (!jumlah.value || parseInt(jumlah.value,10) === 0) {
                                jumlah.value = d.nominal || 0;
                            }
                        }
                    }).catch(()=>{});
            }
            tahun.addEventListener('change', ()=> { 
                updateNominal(); 
                checkStatus(); 
                const base = "{{ route('admin.pembayaran.create') }}";
                const qs = new URLSearchParams({
                    id_siswa: {{ $selectedSiswa->id }},
                    tahun: tahun.value
                });
                window.location.href = base + '?' + qs.toString();
            });
            bulan.addEventListener('change', checkStatus);
            document.addEventListener('DOMContentLoaded', function(){
                updateNominal();
                checkStatus();
            });
        })();
        </script>
        @endpush
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bulan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php $no = 1; @endphp
                @foreach($tagihan as $index => $item)
                <tr class="{{ $item['status'] == 'Lunas' ? 'bg-green-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $no++ }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['bulan'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['tahun'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item['status'] == 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ strtoupper($item['status']) }}
                            </span>
                            @if($item['status'] == 'Lunas' && !empty($item['pembayaran_id']))
                                <a href="{{ route('admin.riwayat.nota', $item['pembayaran_id']) }}" target="_blank"
                                   class="inline-flex items-center px-5 py-2 text-xs rounded bg-black text-white hover:bg-gray-900">
                                    Cetak Struk
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="bg-white rounded-lg shadow p-6 mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-3">Lunaskan Semua di Tahun</h3>
        <div class="flex flex-wrap items-center gap-3">
            @foreach([2024, 2025, 2026] as $yr)
                @if(collect($sppList)->pluck('tahun')->contains($yr))
                    <form method="POST" action="{{ route('admin.pembayaran.lunaskan-tahun') }}">
                        @csrf
                        <input type="hidden" name="id_siswa" value="{{ $selectedSiswa->id }}">
                        <input type="hidden" name="tahun" value="{{ $yr }}">
                        <button type="submit" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-900 text-sm">
                            Lunaskan Tahun {{ $yr }}
                        </button>
                    </form>
                @endif
            @endforeach
        </div>
        <p class="mt-2 text-xs text-gray-500">Semua bulan pada tahun yang dipilih akan disetel LUNAS dan struk tersedia per bulan.</p>
    </div>
    <div class="bg-white rounded-lg shadow mt-4 p-4">
        @php
            $totalNominalLunas = collect($tagihan)->filter(function($t){ return $t['status'] === 'Lunas'; })->sum('nominal');
            $totalSisaBelum = collect($tagihan)->filter(function($t){ return $t['status'] !== 'Lunas'; })->sum('sisa');
        @endphp
        <div class="flex items-center justify-center gap-8 text-sm font-semibold text-gray-800">
            <div>Total Nominal Lunas: Rp {{ number_format($totalNominalLunas, 0, ',', '.') }}</div>
            <div>Total Nominal Belum Lunas (Sisa): Rp {{ number_format($totalSisaBelum, 0, ',', '.') }}</div>
        </div>
    </div>
    @endif
</div>
@endsection
