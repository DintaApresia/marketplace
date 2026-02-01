<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penjualan Penjual</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }

        .container {
            padding: 24px;
        }

        h1 {
            font-size: 16px;
            font-weight: bold;
            color: #15803d;
            margin-bottom: 4px;
            text-align: center;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
            text-align: center;
        }

        .divider {
            border-top: 1px solid #9ca3af;
            margin: 14px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background-color: #f3f4f6;
            text-align: left;
        }

        .section {
            margin-bottom: 18px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 6px;
            padding-left: 6px;
            border-left: 4px solid;
        }

        .green { border-color: #16a34a; color: #15803d; }
        .blue  { border-color: #2563eb; color: #1d4ed8; }
        .gray  { border-color: #6b7280; color: #374151; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        .thead-dark th {
            background-color: #334155; /* slate-700 */
            color: #ffffff;
            font-weight: bold;
            text-align: center;
        }
        tbody td {
            text-align: left;
        }

        tbody td.text-center {
            text-align: center;
        }

        tbody td.text-right {
            text-align: right;
        }

    </style>
</head>

<body>
<div class="container">

    {{-- ================= HEADER ================= --}}
    <h1>Laporan Penjualan Penjual</h1>
    <div class="subtitle">SecondLife Marketplace</div>

    <div class="divider"></div>

    {{-- ================= IDENTITAS ================= --}}
    <table>
        <tr>
            <th style="width:35%">Nama Penjual</th>
            <td>{{ auth()->user()->penjual?->nama_penjual }}</td>
        </tr>
        <tr>
            <th>Periode Laporan</th>
            <td>
                {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}
                s.d.
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </td>
        </tr>
        <tr>
            <th>Tanggal Cetak</th>
            <td>{{ now()->timezone('Asia/Jakarta')->format('d M Y H:i') }}</td>
        </tr>
    </table>

    {{-- ================= I. RINGKASAN PENJUALAN ================= --}}
    <div class="section">
        <div class="section-title green">I. Ringkasan Penjualan</div>
        <table>
            <tr>
                <th>Total Pesanan</th>
                <td>{{ $pesananMasuk }}</td>
            </tr>
            <tr>
                <th>Total Terjual</th>
                <td>
                    Rp {{ number_format($totalTerjual ?? 0, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

    {{-- ================= RINCIAN PRODUK TERJUAL ================= --}}
    <div class="section">
        <div class="section-title green">Rincian Produk Terjual</div>

        @if(count($produkTerjual))
            <table>
                <thead class="thead-dark">
                    <tr>
                        <th style="width:5%">No</th>
                        <th style="width:15%">Tgl Pembelian</th>
                        <th style="width:20%">Nama Pembeli</th>
                        <th>Nama Produk</th>
                        <th style="width:12%">Status</th>
                        <th style="width:10%">Jumlah</th>
                        <th style="width:18%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($produkTerjual as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($item->tanggal_pembelian)->format('d M Y') }}
                            </td>
                            <td>{{ $item->nama_pembeli }}</td>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="text-center" style="font-weight:600;">
                                {{ ucfirst($item->status_pesanan) }}
                            </td>
                            <td class="text-center">{{ $item->jumlah }}</td>
                            <td class="text-right">
                                Rp {{ number_format($item->subtotal_item, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                    <tr style="font-weight:bold;background:#f3f4f6;">
                        <td colspan="6" style="text-align:right;">
                            Total Subtotal
                        </td>
                        <td style="text-align:right;">
                            Rp {{ number_format($totalSubtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="font-size:11px;color:#6b7280;">
                Tidak ada produk terjual pada periode ini.
            </p>
        @endif
    </div>

    {{-- ================= II. RINGKASAN PRODUK ================= --}}
    <div class="section">
        <div class="section-title blue">II. Ringkasan Produk</div>
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

    {{-- ================= III. STATUS PESANAN ================= --}}
    <div class="section">
        <div class="section-title gray">III. Ringkasan Status Pesanan</div>
        <table>
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
        </table>
    </div>

    {{-- ================= FOOTER ================= --}}
    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh sistem SecondLife Marketplace.
    </div>

</div>
</body>
</html>