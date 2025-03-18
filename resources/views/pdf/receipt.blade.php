<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table td {
            padding: 8px;
            vertical-align: top;
        }

        .reg-table td:first-child {
            width: 50%;
        }

        .reg-table td:last-child {
            width: 50%;
        }

        .pro-table th, .pro-table td {
            border: 1px solid black;
            padding: 8px;
            text-align: center; /* Mengatur teks agar berada di tengah */
        }

        .pro-table th {
            background-color: #f2f2f2; /* Menambahkan warna latar belakang untuk header */
        }

        label {
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
        }

        .section-title {
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .double-column {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .column {
            width: 48%;
        }

        .column label {
            display: block;
            margin-bottom: 5px;
        }

        .column p {
            margin: 5px 0;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo-container img {
            width: 40%;
        }
        .header{
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo-container">
                @if(isset($setting->logo))
                    <img src="{{ public_path('storage/' . $setting->logo) }}" alt="Logo">
                @endif
            </div>
    
            <div>
                <h4 style="margin-bottom: 0%;margin-top: 5px">{{$setting->alamat ?? ''}}</h4>
                <h4 style="margin-top: 10px">{{$setting->no_telp ?? ''}}</h4>
                <h4 style="margin-top: 10px">{{ $data->created_at ? $data->created_at->format('d M Y H:i') : '' }}</h4>
            </div>
        </div>

        <table class="reg-table">
            <tr>
                <td><label>Nama Pelanggan:</label></td>
                <td>{{ $data->nama ?? '' }}</td>
            </tr>
            <tr>
                <td><label>No Telepon:</label></td>
                <td>{{ $data->no_telp ?? '' }}</td>
            </tr>
            <tr>
                <td><label>Alamat:</label></td>
                <td>{{ $data->alamat ?? '' }}</td>
            </tr>
            <tr>
                <td><label>Merk Frame:</label></td>
                <td>{{ $data->merk_frame ?? '' }}</td>
            </tr>
            <tr>
                <td><label>Jenis Lensa:</label></td>
                <td>{{ $data->jenis_lensa ?? '' }}</td>
            </tr>
            <tr>
                <td><label>Harga Frame:</label></td>
                <td>Rp. {{ number_format($data->harga_frame, 2, ',', '.') ?? '0' }}</td>
            </tr>
            <tr>
                <td><label>Harga Lensa:</label></td>
                <td>Rp. {{ number_format($data->harga_lensa, 2, ',', '.') ?? '0' }}</td>
            </tr>
        </table>
        <table class="pro-table">
            <tr>
                <td></td>
                <td>SPH</td>
                <td>CYL</td>
                <td>AXIS</td>
                <td>ADD</td>
                <td>PD</td>
            </tr>
            <tr>
                <td>R</td>
                <td>{{ $data->r_sph ?? '0' }}</td>
                <td>{{ $data->r_cyl ?? '0' }}</td>
                <td>{{ $data->r_axis ?? '0' }}</td>
                <td>{{ $data->r_add ?? '0' }}</td>
                <td>{{ $data->r_pd ?? '0' }}</td>
            </tr>
            <tr>
                <td>L</td>
                <td>{{ $data->l_sph ?? '0' }}</td>
                <td>{{ $data->l_cyl ?? '0' }}</td>
                <td>{{ $data->l_axis ?? '0' }}</td>
                <td>{{ $data->l_add ?? '0' }}</td>
                <td>{{ $data->l_pd ?? '0' }}</td>
            </tr>
        </table>

        <table class="reg-table">
            <tr>
                <td><label>Total Harga:</label></td>
                <td>Rp. {{ number_format($data->total, 2, ',', '.') ?? '0' }}</td>
            </tr>
            <tr>
                <td><label>Jumlah Bayar/DP:</label></td>
                <td>Rp. {{ number_format($data->bayar, 2, ',', '.') ?? '0' }}</td>
            </tr>
            <tr>
                <td><label>Jumlah Kekurangan Pembayaran:</label></td>
                <td>Rp. {{ number_format($data->kurang, 2, ',', '.') ?? '0' }}</td>
            </tr>
            <tr>
                <td><label>Status:</label></td>
                <td>{{ $data->status ?? '' }}</td>
            </tr>
            <tr>
                <td><label>Note:</label></td>
                <td>{{ $data->note ?? '' }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for your purchase!</p>
        </div>

    </div>
</body>
</html>
