<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran SPP - Tahun {{ $tahun }} - {{ $siswa->nama }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family: Arial, sans-serif; padding: 20px; background: white; }
        .nota-container { max-width: 900px; margin: 0 auto; background: white; border: 1px solid #ddd; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color:#2563eb; font-size: 24px; margin-bottom: 5px; }
        .header p { color:#666; font-size: 14px; }
        .info-section { display:grid; grid-template-columns:1fr 1fr; gap:30px; margin-bottom: 20px; }
        .info-box { background:#f9fafb; padding:15px; border-radius:8px; }
        .info-box h3 { color:#2563eb; font-size:14px; margin-bottom:10px; text-transform:uppercase; }
        .info-box p { color:#333; font-size:14px; margin:5px 0; }
        .month-card { border:1px solid #e5e7eb; border-radius:8px; margin:12px 0; }
        .month-header { display:flex; justify-content:space-between; align-items:center; padding:10px 15px; background:#f3f4f6; }
        .month-title { font-weight:600; color:#374151; }
        .status-badge { display:inline-block; padding:5px 12px; border-radius:16px; font-size:12px; font-weight:bold; }
        .status-lunas { background:#dbeafe; color:#1e40af; }
        .status-belum { background:#fee2e2; color:#991b1b; }
        .month-body { padding:12px 15px; }
        .row { display:flex; justify-content:space-between; margin:6px 0; color:#666; }
        .total-section { background:#f9fafb; padding:16px; border-radius:8px; margin-top:20px; }
        .total-row { display:flex; justify-content:space-between; margin:8px 0; font-size:16px; }
        .grand { font-size:18px; font-weight:bold; color:#2563eb; border-top:2px solid #2563eb; padding-top:10px; margin-top:10px; }
        .footer { text-align:center; margin-top: 20px; padding-top: 10px; border-top:1px solid #e5e7eb; color:#666; font-size:12px; }
        @media print { body { padding:0; } .nota-container { border:none; padding:20px; } .no-print { display:none; } }
    </style>
    </head>
<body>
    <div class="nota-container">
        <div class="header">
            <h1>NOTA PEMBAYARAN SPP TAHUN {{ $tahun }}</h1>
            <p>Sistem Pembayaran SPP</p>
        </div>
        <div class="info-section">
            <div class="info-box">
                <h3>Data Siswa</h3>
                <p><strong>Nama:</strong> {{ $siswa->nama }}</p>
                <p><strong>NISN:</strong> {{ $siswa->nisn }}</p>
                <p><strong>Kelas:</strong> {{ $siswa->kelas->nama_kelas ?? 'N/A' }}</p>
            </div>
            <div class="info-box">
                <h3>SPP Tahun</h3>
                <p><strong>Tahun SPP:</strong> {{ $spp->tahun ?? $tahun }}</p>
                <p><strong>Nominal SPP:</strong> Rp {{ number_format($spp->nominal ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
        @php
            $totalNominal = 0;
            $totalBayar = 0;
            $totalSisa = 0;
        @endphp
        @foreach($data as $d)
            @php
                $totalNominal += $d['nominal'];
                $totalBayar += $d['jumlah_bayar'];
                $totalSisa += $d['sisa'];
            @endphp
            <div class="month-card">
                <div class="month-header">
                    <div class="month-title">{{ $d['bulan'] }} / {{ $d['tahun'] }}</div>
                    <span class="status-badge {{ $d['status'] === 'Lunas' ? 'status-lunas' : 'status-belum' }}">
                        {{ strtoupper($d['status']) }}
                    </span>
                </div>
                <div class="month-body">
                    <div class="row"><span>Nominal SPP</span><span>Rp {{ number_format($d['nominal'], 0, ',', '.') }}</span></div>
                    <div class="row"><span>Jumlah Bayar</span><span>Rp {{ number_format($d['jumlah_bayar'], 0, ',', '.') }}</span></div>
                    <div class="row"><span>Sisa Tagihan</span><span>Rp {{ number_format($d['sisa'], 0, ',', '.') }}</span></div>
                    @if($d['pembayaran'])
                        <div class="row"><span>No. Transaksi</span><span>#{{ str_pad($d['pembayaran']->id, 6, '0', STR_PAD_LEFT) }}</span></div>
                        <div class="row"><span>Tanggal</span><span>{{ optional($d['pembayaran']->tgl_bayar)->format('d/m/Y') }}</span></div>
                        <div class="row"><span>Petugas</span><span>{{ $d['pembayaran']->petugas->username ?? 'N/A' }}</span></div>
                    @endif
                </div>
            </div>
        @endforeach
        <div class="total-section">
            <div class="total-row"><span>Total Nominal Tahun</span><span>Rp {{ number_format($totalNominal, 0, ',', '.') }}</span></div>
            <div class="total-row"><span>Total Dibayar</span><span>Rp {{ number_format($totalBayar, 0, ',', '.') }}</span></div>
            <div class="total-row grand"><span>Total Sisa</span><span>Rp {{ number_format($totalSisa, 0, ',', '.') }}</span></div>
        </div>
        <div class="footer">
            <p>Terima kasih atas pembayaran Anda</p>
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <div style="display:inline-flex; gap:12px;">
            <button onclick="window.print()" style="background:#2563eb; color:#fff; padding:10px 20px; border:none; border-radius:5px; cursor:pointer; font-size:16px;">
                Cetak / Print
            </button>
            <a href="{{ route('admin.pembayaran.create', ['id_siswa' => $siswa->id, 'tahun' => $tahun]) }}"
               style="background:#111827; color:#fff; padding:10px 20px; border-radius:5px; font-size:16px; text-decoration:none;">
               Kembali
            </a>
        </div>
    </div>
</body>
</html>
