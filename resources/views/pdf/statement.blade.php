<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>განაცხადი</title>
    <style>
        * {
            font-family: DejaVu sans;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <p><strong>ბენეფიციარის სახელი, გვარი:</strong> {{ $data->beneficiary_name }}</p>
    <p><strong>ზედნადების ნომერი:</strong> {{ $data->overhead_number }}</p>
    <p><strong>ზედნადების თარიღი:</strong> {{ $data->overhead_date }}</p>
    <p><strong>მაღაზიის მისამართი:</strong> {{ $data->store_address }}</p>
    <p><strong>ბარათის ბოლო 4 ციფრი:</strong> {{ $data->card_number }}</p>
    <p><strong>ჯამური თანხა:</strong> {{ $data->full_amount }}</p>

    <hr>

    <table border="1" width="100%">
        <thead>
            <tr>
                <th>პროდუქტი</th>
                <th>ფასი</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data["statement_products"] as $item)
                @foreach ($item["products"] as $products)
                    <tr>
                        <td>{{ $products->name }}</td>
                        <td>{{ $item->price }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>