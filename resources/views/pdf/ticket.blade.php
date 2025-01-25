<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bilet Detayı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }
        .ticket-header {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        .ticket-details {
            margin-top: 20px;
        }
        .ticket-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .ticket-details table th, .ticket-details table td {
            padding: 8px;
            border: 1px solid #ccc;
        }
        .ticket-footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>
<body>

<div class="ticket-header">
    <h1>Bilet Detayi</h1>
</div>

<div class="ticket-details">
    <h2>Etkinlik Bilgileri</h2>
    <table>
        <tr>
            <th>Etkinlik Adı</th>
            <td>{{ $event_details['name'] }}</td>
        </tr>
        <tr>
            <th>Etkinlik Açiklamasi</th>
            <td>{{ $event_details['description'] }}</td>
        </tr>
        <tr>
            <th>Başlangiç Tarihi</th>
            <td>{{ $event_details['start_date'] }}</td>
        </tr>
        <tr>
            <th>Bitiş Tarihi</th>
            <td>{{ $event_details['end_date'] }}</td>
        </tr>
    </table>

    <h2>Koltuk Bilgileri</h2>
    <table>
        <tr>
            <th>Bölüm</th>
            <td>{{ $seat_details['section'] }}</td>
        </tr>
        <tr>
            <th>Sira</th>
            <td>{{ $seat_details['row'] }}</td>
        </tr>
        <tr>
            <th>Koltuk Numarası</th>
            <td>{{ $seat_details['number'] }}</td>
        </tr>
        <tr>
            <th>Fiyat</th>
            <td>{{ $price }} TL</td>
        </tr>
    </table>

    <h2>Salon Bilgileri</h2>
    <table>
        <tr>
            <th>Salon Adı</th>
            <td>{{ $venue_details['name'] }}</td>
        </tr>
        <tr>
            <th>Adres</th>
            <td>{{ $venue_details['address'] }}</td>
        </tr>
    </table>
    <h2>Kisi Bilgileri</h2>
    <table>
        <tr>
            <th>Kisi Adi</th>
            <td>{{ $user_details['name'] }}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{ $user_details['email'] }}</td>
        </tr>
    </table>
</div>

<div class="ticket-footer">
    <p>Bilet Kodu: {{ $ticket_code }}</p>
    <p>Durum: {{ $status }}</p>
</div>

</body>
</html>
