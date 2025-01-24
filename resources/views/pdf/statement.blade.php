<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>განაცხადი</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>განაცხადი</h1>
        <p><strong>კომპანიის სახელი:</strong> {{ $data->company_name }}</p>
        <p><strong>მაღაზიის მისამართი:</strong> {{ $data->store_address }}</p>
        <p><strong>ზედნადების ნომერი:</strong> {{ $data->overhead_number }}</p>
        <p><strong>ზედნადების თარიღი:</strong> {{ $data->overhead_date }}</p>
        <p><strong>ბენეფიციარის სახელი, გვარი:</strong> {{ $data->beneficiary_name }}</p>
        <p><strong>ბარათის ბოლო 4 ციფრი:</strong> {{ $data->card_number }}</p>
        <p><strong>ჯამური თანხა:</strong> {{ $data->full_amount }}</p>

        <hr>

        <table border="1">
            <thead>
                <tr>
                    <th>პროდუქტი</th>
                    <th>ფასი</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data["statement_products"] as $item)
                        <tr>
                            <td>{{ $item["products"]->name }}</td>
                            <td>{{ $item->price }}</td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
