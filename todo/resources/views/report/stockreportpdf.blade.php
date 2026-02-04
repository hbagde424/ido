<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock In Hand</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
            text-align: center; /* Center-align header content */
        }
        td img {
            width: 100px; /* Adjust size as needed */
            height: auto;
        }
        .centered {
            text-align: center;
        }
        .low-stock {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Stock In Hand - 
    <?php 
     foreach($business_locations as $key => $loc){
          //print_r($loc);
        if($key == $location_id){
            echo  $loc;
        }
    }
    ?>
    </h1>
    <table>
        <thead>
            <tr>
                <th style="width: 50%;">@lang('business.product')</th>
                <th style="width: 30%;">Image</th>
                <th style="width: 20%;">@lang('report.current_stock')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $each_result)
                @if($each_result->stock > 0)
                    <tr class="{{ $each_result->alert_quantity > $each_result->stock ? 'low-stock' : '' }}">
                        <td style="width: 50%;" class="centered">{{ $each_result->product }}</td>
                        <td style="width: 30%;" class="centered"><img src="https://gaurishankar369.in/shreeshiv/public/uploads/img/{{ $each_result->image }}"style="width:100px"></td>
                        <td style="width: 20%;" class="centered">
                            {{ number_format($each_result->stock, 2) }}
                            @if($each_result->remaing_qty > 0)
                                <br>({{ $each_result->remaing_qty }}-{{ $each_result->postatus }})
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
 