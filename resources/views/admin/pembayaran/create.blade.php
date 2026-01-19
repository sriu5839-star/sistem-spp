@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-end mb-4">
        <div>
            <h1 class="text-2xl font-bold uppercase">Rekap Pembayaran Siswa</h1>
            <p class="text-gray-500 italic">Input pembayaran cepat per baris</p>
        </div>
        <div class="text-[10px] font-bold italic">
            <span class="text-green-600">Lunas = Lunas</span> | <span class="text-red-600">Belum Lunas= Belum Lunas</span>
        </div>
    </div>

    <div class="bg-white border-2 border-black overflow-hidden">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-100 uppercase text-xs font-bold border-b-2 border-black text-center">
                    <th class="border-r-2 border-black p-2 w-12">No</th>
                    <th class="border-r-2 border-black p-2 text-left">Nama Siswa</th>
                    <th class="border-r-2 border-black p-2 w-24">Bulan</th>
                    <th class="border-r-2 border-black p-2 w-24">Tahun</th>
                    <th class="border-r-2 border-black p-2 w-32">Nominal</th>
                    <th class="border-r-2 border-black p-2 w-16">Ket</th>
                    <th class="p-2 w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riwayat as $index => $item)
                <tr class="border-b border-gray-300 hover:bg-gray-50">
                    <form action="{{ route('admin.pembayaran.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_siswa" value="{{ $item->id }}">
                        
                        <td class="border-r-2 border-black p-2 text-center">{{ $riwayat->firstItem() + $index }}</td>
                        <td class="border-r-2 border-black p-2 uppercase font-bold text-sm">
                            {{ $item->nama }}
                            <div class="text-[9px] font-normal italic text-gray-500">NISN: {{ $item->nisn }}</div>
                        </td>
                        <td class="border-r-2 border-black p-2 text-center">
                            <select name="bulan_dibayar" class="w-full text-xs font-bold border-none bg-transparent focus:ring-0">
                                @foreach(['JAN','FEB','MAR','APR','MEI','JUN','JUL','AGU','SEP','OKT','NOV','DES'] as $b)
                                    <option value="{{ $b }}">{{ $b }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border-r-2 border-black p-2 text-center">
                            <select name="tahun_dibayar" class="w-full text-xs font-bold border-none bg-transparent focus:ring-0">
                                <option value="2024">2024</option>
                                <option value="2025" selected>2025</option>
                                <option value="2026">2026</option>
                            </select>
                        </td>
                        <td class="border-r-2 border-black p-2 text-center">
                            <input type="number" name="jumlah_bayar" value="150000" class="w-full text-xs font-bold border-none bg-transparent text-center focus:ring-0">
                        </td>
                        <td class="border-r-2 border-black p-2 text-center">
                            <div class="flex flex-col text-[10px] font-black italic leading-none">
                                <span class="text-gray-200">L</span>
                                <span class="text-red-600">BL</span>
                            </div>
                        </td>
                        <td class="p-2 text-center">
                            <button type="submit" class="border-2 border-black px-4 py-1 text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-black hover:text-white active:translate-y-0.5 active:shadow-none transition-all">
                                Bayar
                            </button>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $riwayat->links() }}
    </div>
</div>
@endsection