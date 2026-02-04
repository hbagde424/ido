@extends('layouts.app')

<?php 
use App\Product; // Make sure you have this model
?>

@section('content')

<style type="text/css">
    .costing_tbl {
        margin-top: 40px;
    }
    #costing_view_table {
        margin-top: -40px !important;
    }
    .new-d-flex {
        gap: 20px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }
    .new_table_a table th, .new_table_a table td {
        min-width: 200px;
    }
    .new_table_a {
        overflow-x: scroll;
    }
    .d-flex-btn {
        justify-content: space-between;
        display: flex;
        align-items: center;
        margin-top: 30px;
    }
    /* Custom styling for table */
    .table-custom {
        border-collapse: collapse;
        width: 100%;
    }
    /* Style for table header */
    .table-custom th {
        background-color: #f0f0f0; /* Light gray background */
        color: #333; /* Dark text color */
        border: 1px solid #ddd; /* Light gray border */
        padding: 8px;
    }
    /* Style for table body */
    .table-custom td {
        border: 1px solid #ddd; /* Light gray border */
        padding: 8px;
    }
    /* Style for alternating rows */
    .table-custom tbody tr:nth-child(even) {
        background-color: #f9f9f9; /* Light gray background for even rows */
    }
</style>

 
<section class="content no-print">
    <div class="box box-solid">
        <div class="box-body">
        <h1>Product Costing Details</h1>
        <div class="costing_tbl new_table_a box box-primary">
            <table class="table table-bordered table-custom" id="costing_view_table">

                <div class="new-d-flex" style="font-size: 20px; margin-bottom: 50px;">
                    <span class="mb-0">Date: {{ $productCosting->date }}</span>
                    <span class="mb-0">Container Number: {{ $productCosting->container_number }}</span>
                    <span class="mb-0">BN Number: {{ $productCosting->bn_number }}</span>
                    <span class="mb-0">Location: {{ $productCosting->location }}</span>
                </div>

                <thead>
                    <tr>
                        <th>Sno.</th>
                        <th>Product</th>
                        <th>Product Image</th>
                        <th>Final Costing Price</th>
                        <th>Box in Container</th>
                        <th>SQM in Container</th>
                        <th>SQM in Box</th>
                        <th>Price per SQM</th>
                        <th>Expenses 1</th>
                        <th>Expenses 2</th>
                        <th>Expenses 3</th>
                        <th>Expenses 4</th>
                        <th>Expenses 5</th>
                        <th>Expenses 6</th>
                        <th>Expenses 7</th>
                        <th>Expenses 8</th>
                        <th>Expenses 9</th>
                        <th>Total Expenses</th>
                        <th>Final Price</th>
                        <th>Total Final Expense</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(json_decode($productCosting->product_id) as $key => $product_id)
                    <tr>
                        <?php 
                            $productData = Product::findOrFail($product_id);
                        ?>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $productData->name }}</td>
                        <td>
                            <img src="{{ url('uploads/img/' . $productData->image) }}" class="product_img" width="50">
                        </td>
                        <td>{{ json_decode($productCosting->final_costing_price)[$key] }}</td>
                        <td>{{ json_decode($productCosting->box_in_container)[$key] }}</td>
                        <td>{{ json_decode($productCosting->sqm_in_container)[$key] }}</td>
                        <td>{{ json_decode($productCosting->sqm_in_box)[$key] }}</td>
                        <td>{{ json_decode($productCosting->price_per_sqm)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp1)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp2)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp3)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp4)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp5)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp6)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp7)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp8)[$key] }}</td>
                        <td>{{ json_decode($productCosting->exp9)[$key] }}</td>
                        <td>{{ json_decode($productCosting->total_exp)[$key] }}</td>
                        <td>{{ json_decode($productCosting->final_price)[$key] }}</td>
                        <td>{{ json_decode($productCosting->total_final_exp)[$key] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <a href="{{ route('product-costings.index') }}" class="btn btn-primary">Back</a>
        </div>
    </div>
</section>

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
    // Date range as a button
    $('#sell_list_filter_date_range_new').daterangepicker(
        dateRangeSettings,
        function(start, end) {
            $('#sell_list_filter_date_range_new').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );

    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function(start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );

    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
    });

    $('#costing_view_table').DataTable(
    {

        buttons: [
            {
                extend: 'csv',
                text: '<div style="text-align:center;"><i class="fa fa-file-csv" aria-hidden="true"></i> Csv</div>',
                className: 'btn-sm',
                exportOptions: {
                    columns: ':visible',
                },
            },
            {
                extend: 'excelHtml5',
                className: 'btn-sm',
                text: '<div style="text-align:center;"><i class="fa fa-file-excel" aria-hidden="true"></i> ' + LANG.export_to_excel + '</div>',
                customize: function (xlsx) {
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    $('row c', sheet).attr('s', '25');
                    // Add borders to each cell
                    var border = '<border><left style="thin"/><right style="thin"/><top style="thin"/><bottom style="thin"/></border>';
                    var borderStyle = '<style type="border"><style name="thin" type="line" style="thin"/></style>';
                    var borderXml = $.parseXML(border);
                    var borderStyleXml = $.parseXML(borderStyle);
                    $('borders', sheet).append(borderXml.documentElement);
                    $('styles', sheet).append(borderStyleXml.documentElement);
                },
                exportOptions: {
                    columns: ':visible',
                },
            },
            {
                extend: 'pdfHtml5',
                text: '<div style="text-align:center;"><i class="fa fa-file-pdf" aria-hidden="true"></i> ' + LANG.export_to_pdf + '</div>',
                className: 'btn-sm',
                exportOptions: {
                    columns: ':visible',
                },
                customize: function (doc) {
                    // Remove any existing title
                    doc.content.shift();

                    // Title
                    var title = {
                        text: 'Product Costing Details',
                        fontSize: 16,
                        bold: true,
                        alignment: 'center',
                        margin: [0, 0, 0, 20] // Adjust the margin as needed
                    };

                    // Additional details
                    var additionalDetails = [
                        [
                            { text: 'Date: ' + '{{ $productCosting->date }}', fontSize: 10 },
                            { text: 'Container Number: ' + '{{ $productCosting->container_number }}', fontSize: 10 },
                            { text: 'BN Number: ' + '{{ $productCosting->bn_number }}', fontSize: 10 },
                            { text: 'Location: ' + '{{ $productCosting->location }}', fontSize: 10 }
                        ]
                    ];

                    var detailsTable = {
                        table: {
                            widths: ['25%', '25%', '25%', '25%'],
                            body: additionalDetails
                        },
                        margin: [0, 0, 0, 10] // Adjust the margin as needed
                    };

                    // Insert title and additional details before the table
                    doc.content.unshift(detailsTable);
                    doc.content.unshift(title);

                    // Table layout customization
                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return 0.5; };
                    objLayout['vLineWidth'] = function (i) { return 0.5; };
                    objLayout['hLineColor'] = function (i) { return 'black'; };
                    objLayout['vLineColor'] = function (i) { return 'black'; };
                    objLayout['paddingLeft'] = function (i) { return 2; };
                    objLayout['paddingRight'] = function (i) { return 2; };
                    objLayout['paddingTop'] = function (i) { return 2; };
                    objLayout['paddingBottom'] = function (i) { return 2; };
                    doc.content[2].layout = objLayout;

                    // Style adjustments
                    doc.styles.tableHeader.fontSize = 5.7;
                    doc.styles.tableBodyEven.fontSize = 5.7;
                    doc.styles.tableBodyOdd.fontSize = 5.7;

                    // Update the HTML title (if needed)
                    var newTitle = "Product-Costing";
                    $('title').text(newTitle);
                }
            },
            {
                text: 'print',
                className: 'btn-sm',
                text: '<div style="text-align:center;"><i class="fa fa-print" aria-hidden="true"></i> print</div>',
                exportOptions: {
                    columns: ':visible',
                },
            },
            {
                extend: 'colvis',
                text: '<div style="text-align:center;"><i class="fa fa-columns" aria-hidden="true"></i> ' + LANG.col_vis + '</div>',
                className: 'btn-sm',
                exportOptions: {
                    columns: ':visible',
                },
            },
        ]
    }
    );

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source', function() {
        sell_table.ajax.reload();
    });

    $('#only_subscriptions').on('ifChanged', function(event) {
        sell_table.ajax.reload();
    });
});

$(document).ready(function() {
    var newTitle = "Product-Costing"; // Set your new title here
    $('title').text(newTitle);
});
</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection
