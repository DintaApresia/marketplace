<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Dashboard Penjual</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px 0;
        }

        .muted {
            color: #666;
            font-size: 11px;
            margin-bottom: 14px;
        }

        .section {
            border: 1px solid #ddd;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 14px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
            width: 55%;
        }

        .highlight {
            font-weight: bold;
        }

        .total {
            font-weight: bold;
            background-color: #f9fafb;
        }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #777;
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- HEADER --}}
    <h1>Laporan Dashboard Penjual</h1>

    <div class="muted">
        Periode:
        <strong>
            {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
            –
            {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
        </strong>
        <br>
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

    {{-- RINGKASAN PRODUK --}}
    <div class="section">
        <div class="section-title">Ringkasan Produk</div>

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

    {{-- RINGKASAN PESANAN --}}
    <div class="section">
        <div class="section-title">Ringkasan Pesanan</div>

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
            <tr class="total">
                <th>Total Terjual</th>
                <td>
                    Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem.
    </div>

</body>
</html>
