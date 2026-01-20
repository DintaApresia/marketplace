<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Tailwind (ikut Vite / CDN sesuai setupmu) --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-gray-50 text-gray-900">

<div class="max-w-6xl mx-auto p-4 space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Checkout</h1>
        <a href="{{ route('pembeli.keranjang') }}"
           class="text-sm text-gray-600 hover:underline">
            ← Kembali ke Keranjang
        </a>
    </div>

    {{-- Alert --}}
    @if (session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 p-3 text-red-700 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pembeli.orders.simpan') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid md:grid-cols-2 gap-6">

            {{-- DATA PENERIMA --}}
            <div class="rounded-xl border bg-white p-4 space-y-3">
                <h2 class="text-lg font-semibold">Data Penerima</h2>

                <div>
                    <label class="text-sm font-medium">Nama Penerima</label>
                    <input type="text"
                           class="mt-1 w-full rounded-lg border p-2 bg-gray-100"
                           value="{{ $pembeli->nama_pembeli ?? '-' }}"
                           readonly>
                </div>

                <div>
                    <label class="text-sm font-medium">No. HP</label>
                    <input type="text"
                           class="mt-1 w-full rounded-lg border p-2 bg-gray-100"
                           value="{{ $pembeli->no_telp ?? '-' }}"
                           readonly>
                </div>

                <div>
                    <label class="text-sm font-medium">Alamat Pengiriman</label>
                    <textarea rows="4"
                              class="mt-1 w-full rounded-lg border p-2 bg-gray-100"
                              readonly>{{ $pembeli->alamat ?? '-' }}</textarea>
                </div>

                <div>
                    <label class="text-sm font-medium">Catatan (opsional)</label>
                    <input type="text"
                           name="catatan"
                           class="mt-1 w-full rounded-lg border p-2"
                           placeholder="Misal: taruh di pos satpam"
                           value="{{ old('catatan') }}">
                </div>
            </div>

            {{-- RINGKASAN PESANAN --}}
            <div class="rounded-xl border bg-white p-4 space-y-3">
                <h2 class="text-lg font-semibold">Ringkasan Pesanan</h2>

                <div class="space-y-2">
                    @foreach ($cart as $item)
                        <div class="flex justify-between text-sm border-b pb-2">
                            <div>
                                <div class="text-xs text-gray-500 mb-0.5">
                                🏪 {{ $item['nama_penjual'] }}
                                </div>

                                <div class="font-medium">
                                    {{ $item['nama_barang'] ?? $item['nama'] ?? 'Produk' }}
                                </div>
                                <div class="text-gray-500">
                                    {{ $item['qty'] }} × Rp{{ number_format($item['harga'], 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="font-semibold">
                                Rp{{ number_format($item['qty'] * $item['harga'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- METODE PEMBAYARAN --}}
                <div class="pt-2">
                    <label class="text-sm font-medium">Metode Pembayaran</label>
                    <select name="metode_pembayaran"
                            id="metode_pembayaran"
                            class="mt-1 w-full rounded-lg border p-2"
                            required>
                        <option value="" disabled {{ old('metode_pembayaran') ? '' : 'selected' }}>-- Pilih --</option>
                        <option value="cod" {{ old('metode_pembayaran')=='cod' ? 'selected' : '' }}>COD</option>
                        <option value="transfer" {{ old('metode_pembayaran')=='transfer' ? 'selected' : '' }}>Transfer</option>
                    </select>
                    @error('metode_pembayaran')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- INFO TRANSFER --}}
                <div id="box-transfer" class="mt-3 p-3 border rounded-lg bg-gray-50 hidden">
                    <p class="text-sm font-semibold mb-1">Transfer ke Rekening:</p>
                    <p class="text-sm">
                        <span class="font-medium">{{ $penjual->nama_bank }}</span><br>
                        No. Rekening: <span class="font-semibold">{{ $penjual->rekening }}</span><br>
                        A/N: {{ $penjual->nama_rekening }}
                    </p>

                    <div class="mt-3">
                        <label class="text-sm font-medium">Upload Bukti Pembayaran</label>
                        <input type="file"
                               name="bukti_pembayaran"
                               accept="image/*"
                               class="mt-1 w-full rounded-lg border p-2">
                        @error('bukti_pembayaran')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TOTAL --}}
                <div class="border-t pt-3 space-y-1 text-sm">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-semibold">Rp{{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkir</span>
                        <span class="font-semibold">Rp{{ number_format($ongkir, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t pt-3 flex justify-between text-base font-semibold">
                    <span>Total Bayar</span>
                    <span>Rp{{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <button type="submit"
                        class="w-full mt-4 rounded-lg bg-indigo-600 py-2 text-white font-semibold hover:bg-indigo-700">
                    Buat Pesanan
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    const metodeSelect = document.getElementById('metode_pembayaran');
    const boxTransfer  = document.getElementById('box-transfer');

    function toggleTransfer() {
        if (metodeSelect.value === 'transfer') {
            boxTransfer.classList.remove('hidden');
        } else {
            boxTransfer.classList.add('hidden');
        }
    }

    metodeSelect.addEventListener('change', toggleTransfer);
    toggleTransfer();
</script>

</body>
</html>
