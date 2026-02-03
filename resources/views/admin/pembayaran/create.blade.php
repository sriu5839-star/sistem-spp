@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold uppercase mb-4">Input Pembayaran Siswa</h1>
        
        <!-- Form Pencarian Siswa -->
        <form action="{{ route('admin.pembayaran.create') }}" method="GET" class="flex gap-4 items-end bg-white p-4 border-2 border-black">
            <div class="w-full max-w-md">
                <label for="id_siswa" class="block text-sm font-bold uppercase mb-1">Pilih Siswa</label>
                <select name="id_siswa" id="id_siswa" class="w-full border-2 border-black p-2 focus:ring-0">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswaList as $s)
                        <option value="{{ $s->id }}" {{ (isset($selectedSiswa) && $selectedSiswa->id == $s->id) ? 'selected' : '' }}>
                            {{ $s->nama }} ({{ $s->nisn }})
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-black text-white px-6 py-2 border-2 border-black font-bold uppercase hover:bg-gray-800 transition-all">
                Cari Data
            </button>
        </form>
    </div>

    @if(isset($selectedSiswa) && count($tagihan) > 0)
    <div class="flex justify-between items-end mb-4">
        <div>
            <h2 class="text-xl font-bold uppercase">Rincian Tagihan: <span class="text-blue-600">{{ $selectedSiswa->nama }}</span></h2>
        </div>
        <div class="text-[10px] font-bold italic">
            <span class="text-green-600">Lunas = Sudah Dibayar</span> | <span class="text-red-600">Belum Lunas = Belum Dibayar</span>
        </div>
    </div>

    <div class="bg-white border-2 border-black overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 uppercase text-xs font-bold border-b-2 border-black text-center">
                    <th class="border-r-2 border-black p-2 w-12">No</th>
                    <th class="border-r-2 border-black p-2">Bulan</th>
                    <th class="border-r-2 border-black p-2">Tahun</th>
                    <th class="border-r-2 border-black p-2">Nominal</th>
                    <th class="border-r-2 border-black p-2">Ket</th>
                    <th class="p-2 w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tagihan as $index => $item)
                <tr class="border-b border-gray-300 hover:bg-gray-50 {{ $item['status'] == 'Lunas' ? 'bg-green-50' : '' }}">
                    <td class="border-r-2 border-black p-2 text-center">{{ $index + 1 }}</td>
                    <td class="border-r-2 border-black p-2 text-center font-bold">{{ $item['bulan'] }}</td>
                    <td class="border-r-2 border-black p-2 text-center font-bold">{{ $item['tahun'] }}</td>
                    <td class="border-r-2 border-black p-2 text-center">Rp {{ number_format($item['nominal'], 0, ',', '.') }}</td>
                    <td class="border-r-2 border-black p-2 text-center font-bold">
                        @if($item['status'] == 'Lunas')
                            <span class="text-green-600">LUNAS</span>
                        @else
                            <span class="text-red-600">BELUM LUNAS</span>
                        @endif
                    </td>
                    <td class="p-2 text-center">
                        @if($item['status'] == 'Belum Lunas')
                        <form action="{{ route('admin.pembayaran.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="id_siswa" value="{{ $selectedSiswa->id }}">
                            <input type="hidden" name="bulan_dibayar" value="{{ $item['bulan'] }}">
                            <input type="hidden" name="tahun_dibayar" value="{{ $item['tahun'] }}">
                            <input type="hidden" name="jumlah_bayar" value="{{ $item['nominal'] }}">
                            
                            <button type="submit" class="border-2 border-black px-4 py-1 text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-black hover:text-white active:translate-y-0.5 active:shadow-none transition-all">
                                Bayar
                            </button>
                        </form>
                        @else
                            <button disabled class="text-gray-400 font-bold text-xs cursor-not-allowed">
                                TERBAYAR
                            </button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection