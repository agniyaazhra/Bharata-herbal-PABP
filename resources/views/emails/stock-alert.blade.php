<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peringatan Stok Produk</title>
    <style>
        body { font-family: Arial, sans-serif; background:
        .container { max-width: 600px; margin: 0 auto; background:
        .header { background:
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 24px; }
        .alert-box { padding: 16px; border-radius: 6px; margin: 16px 0; }
        .alert-danger  { background:
        .alert-warning { background:
        .product-info { background:
        .product-info table { width: 100%; border-collapse: collapse; }
        .product-info td { padding: 6px 4px; font-size: 14px; }
        .product-info td:first-child { color:
        .footer { background:
        .btn { display: inline-block; background:
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚠️ Peringatan Stok — Bharata Herbal</h1>
    </div>
    <div class="body">
        <p>Halo, <strong>{{ $admin->name }}</strong></p>

        <div class="alert-box {{ $product->status === 'inactive' ? 'alert-danger' : 'alert-warning' }}">
            {{ $message }}
        </div>

        <div class="product-info">
            <table>
                <tr>
                    <td>Nama Produk</td>
                    <td><strong>{{ $product->name }}</strong></td>
                </tr>
                <tr>
                    <td>Stok Saat Ini</td>
                    <td><strong>{{ $product->stock }} unit</strong></td>
                </tr>
                <tr>
                    <td>Status</td>
                    <td>
                        @if($product->status === 'inactive')
                            <span style="color:
                        @elseif($product->status === 'warning')
                            <span style="color:
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>{{ now()->format('d M Y, H:i') }} WIB</td>
                </tr>
            </table>
        </div>

        <p>Segera tambahkan stok produk untuk menghindari kehilangan penjualan.</p>

        <a href="{{ url('/admin/products') }}" class="btn">Kelola Produk →</a>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} Bharata Herbal. Email ini dikirim otomatis, jangan dibalas.
    </div>
</div>
</body>
</html>
