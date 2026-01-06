<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard Penjual</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin: 0 0 6px 0; }
        .muted { color: #666; }
        .box { border: 1px solid #ddd; padding: 12px; border-radius: 6px; margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>Laporan Dashboard Penjual</h1>
    <div class="muted">Dicetak: {{ now()->format('d M Y H:i') }}</div>

    <div class="box">
        <strong>Ringkasan Produk</strong>
        <table>
            <tr>
                <th>Total Produk</th>
                <td>{{ $totalProduk }}</td>
            </tr>
            <tr>
                <th>Produk Aktif</th>
                <td>{{ $produkAktif }}</td>
            </tr>
            <tr>
                <th>Produk Nonaktif</th>
                <td>{{ $produkNonaktif }}</td>
            </tr>
        </table>
    </div>

    <div class="box">
        <strong>Ringkasan Pesanan</strong>
        <table>
            <tr>
                <th>Total Pesanan</th>
                <td>{{ $pesananMasuk }}</td>
            </tr>
            <tr>
                <th>Dikemas</th>
                <td>{{ $pesananDikemas }}</td>
            </tr>
            <tr>
                <th>Dikirim</th>
                <td>{{ $pesananDikirim }}</td>
            </tr>
            <tr>
                <th>Selesai</th>
                <td>{{ $pesananSelesai }}</td>
            </tr>
            <tr>
                <th>Ditolak</th>
                <td>{{ $pesananDitolak }}</td>
            </tr>
            <tr>
                <th><strong>Total Terjual</strong></th>
                <td><strong>Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}</strong></td>
            </tr>
        </table>
    </div>

</body>
</html>
