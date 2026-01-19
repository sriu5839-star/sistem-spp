<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Riwayat Transaksi - {{ $siswa->nama ?? 'N/A' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: white; }
        .container { max-width: 1000px; margin: 0 auto; background: white; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 20px; }
        .header h1 { color: #2563eb; font-size: 22px; margin-bottom: 5px; }
        .header p { color: #666; font-size: 14px; }
        .info { display: flex; justify-content: space-between; margin: 20px 0; font-size: 14px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #e5e7eb; font-size: 14px; }
        th { background: #f3f4f6; text-align: left; }
        .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
        @media print { .no-print { display: none; } body { padding: 0; } .container { padding: 0; } }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            if (new URLSearchParams(window.location.search).get('auto') === '1') {
                window.print();
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>REKAP RIWAYAT TRANSAKSI SPP</h1>
            <p>Sistem Pembayaran SPP</p>
        </div>

        <div class="info">
            <div>
                <div><strong>Nama:</strong> {{ $siswa->nama ?? 'N/A' }}</div>
                <div><strong>NISN:</strong> {{ $siswa->nisn ?? 'N/A' }}</div>
                <div><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</div>
            </div>
            <div style="text-align: right;">
                <div><strong>Filter Bulan:</strong> {{ $bulan ?? 'Semua' }}</div>
                <div><strong>Filter Tahun:</strong> {{ $tahun ?? 'Semua' }}</div>
                <div><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bulan/Tahun</th>
                    <th>Nominal SPP</th>
                    <th>Jumlah Bayar</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @php $i = 1; $total = 0; @endphp
                @forelse($riwayat as $row)
                    @php $total += (float) $row->jumlah_bayar; @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $row->bulan_dibayar }} / {{ $row->tahun_dibayar }}</td>
                        <td>Rp {{ number_format($row->spp->nominal ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>{{ $row->status }}</td>
                        <td>{{ optional($row->tgl_bayar)->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #666;">Tidak ada data untuk filter ini</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right;">Total</th>
                    <th colspan="3">Rp {{ number_format($total, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <div class="no-print" style="text-align: center; margin-top: 20px;">
            <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                Cetak / Print
            </button>
            <a href="{{ route('user.riwayat.index') }}" style="display: inline-block; margin-left: 10px; background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none;">
                Kembali
            </a>
        </div>

        <div class="footer">
            <p>Dokumen ini dicetak dari sistem pembayaran SPP</p>
        </div>
    </div>
</body>
</html>
