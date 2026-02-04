<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran SPP - {{ $pembayaran->siswa->nama ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: white;
        }
        .nota-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 14px;
        }
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
        }
        .info-box h3 {
            color: #2563eb;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .info-box p {
            color: #333;
            font-size: 14px;
            margin: 5px 0;
        }
        .detail-section {
            margin-bottom: 30px;
        }
        .detail-section h2 {
            color: #2563eb;
            font-size: 18px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }
        .detail-table th,
        .detail-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }
        .detail-table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: 600;
        }
        .detail-table td {
            color: #666;
        }
        .total-section {
            background: #f9fafb;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            font-size: 16px;
        }
        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #2563eb;
            padding-top: 10px;
            margin-top: 15px;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #666;
            font-size: 12px;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-lunas {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-belum {
            background: #fee2e2;
            color: #991b1b;
        }
        @media print {
            body {
                padding: 0;
            }
            .nota-container {
                border: none;
                padding: 20px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="nota-container">
        <div class="header">
            <h1>NOTA PEMBAYARAN SPP</h1>
            <p>Sistem Pembayaran SPP</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>Data Siswa</h3>
                <p><strong>Nama:</strong> {{ $pembayaran->siswa->nama ?? 'N/A' }}</p>
                <p><strong>NISN:</strong> {{ $pembayaran->siswa->nisn ?? 'N/A' }}</p>
                <p><strong>Kelas:</strong> {{ $pembayaran->siswa->kelas->nama_kelas ?? 'N/A' }}</p>
            </div>
            <div class="info-box">
                <h3>Data Pembayaran</h3>
                <p><strong>No. Transaksi:</strong> #{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</p>
                <p><strong>Tanggal:</strong> {{ $pembayaran->tgl_bayar->format('d/m/Y') }}</p>
                <p><strong>Petugas:</strong> {{ $pembayaran->petugas->username ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="detail-section">
            <h2>Detail Pembayaran</h2>
            <table class="detail-table">
                <thead>
                    <tr>
                        <th>Bulan/Tahun</th>
                        <th>Nominal SPP</th>
                        <th>Jumlah Bayar</th>
                        <th>Sisa Tagihan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pembayaran->bulan_dibayar }} / {{ $pembayaran->tahun_dibayar }}</td>
                        <td>Rp {{ number_format($pembayaran->spp->nominal ?? 0, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format(max(0, ($pembayaran->spp->nominal ?? 0) - $pembayaran->jumlah_bayar), 0, ',', '.') }}</td>
                        <td>
                            <span class="status-badge {{ $pembayaran->status === 'Lunas' ? 'status-lunas' : 'status-belum' }}">
                                {{ $pembayaran->status }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-row">
                <span>Nominal SPP:</span>
                <span>Rp {{ number_format($pembayaran->spp->nominal ?? 0, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Jumlah Dibayar:</span>
                <span>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="total-row">
                <span>Sisa Tagihan:</span>
                <span>Rp {{ number_format(max(0, ($pembayaran->spp->nominal ?? 0) - $pembayaran->jumlah_bayar), 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih atas pembayaran Anda</p>
            <p>Nota ini adalah bukti pembayaran yang sah</p>
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <div style="display:inline-flex; gap:12px;">
            <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
                Cetak / Print
            </button>
            <a href="{{ route('admin.pembayaran.create', ['id_siswa' => $pembayaran->siswa->id, 'tahun' => $pembayaran->tahun_dibayar]) }}"
               style="background: #111827; color: white; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; text-decoration: none;">
                Kembali
            </a>
        </div>
    </div>
</body>
</html>
