@extends('layouts.app')
@section('title', __( 'report.profit_loss' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'report.profit_loss' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="print_section"><h2>{{session()->get('business.name')}} - @lang( 'report.profit_loss' )</h2></div>
    
    <div class="row no-print">
        <div class="col-md-3 col-md-offset-7 col-xs-6">
            <div class="input-group">
                <span class="input-group-addon bg-light-blue"><i class="fa fa-map-marker"></i></span>
                 <select class="form-control select2" id="profit_loss_location_filter">
                    @foreach($business_locations as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-xs-6">
            <div class="form-group pull-right">
                <div class="input-group">
                  <button type="button" class="btn btn-primary" id="profit_loss_date_filter">
                    <span>
                      <i class="fa fa-calendar"></i> {{ __('messages.filter_by_date') }}
                    </span>
                    <i class="fa fa-caret-down"></i>
                  </button>
                </div>
            </div>
        </div>
    </div>
    <div class="row" class="col-xs-12" >
        <div id="pl_data_div">
            
        </div>
    </div>
    

    <div class="row no-print">
        <div class="col-sm-12">
            <button type="button" class="btn btn-primary pull-right" 
            aria-label="Print" onclick="window.print();"
            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button>
        </div>
    </div>
    <div class="row no-print">
        <div class="col-md-12">
           <!-- Custom Tabs -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                   
                    <li  > 
                        <a href="#profit_by_products" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i> @lang('lang_v1.profit_by_products')</a>
                    </li>
 <li class="active">
                        <a href="#profit_by_ts" data-toggle="tab" aria-expanded="true"><i class="fa fa-cubes" aria-hidden="true"></i>Profit By TS</a>
                    </li>
                    <li style="display:none;">
                        <a href="#profit_by_categories" data-toggle="tab" aria-expanded="true"><i class="fa fa-tags" aria-hidden="true"></i> @lang('lang_v1.profit_by_categories')</a>
                    </li>

                    <li style="display:none;">
                        <a href="#profit_by_brands" data-toggle="tab" aria-expanded="true"><i class="fa fa-diamond" aria-hidden="true"></i> @lang('lang_v1.profit_by_brands')</a>
                    </li>

                    <li style="display:none;">
                        <a href="#profit_by_locations" data-toggle="tab" aria-expanded="true"><i class="fa fa-map-marker" aria-hidden="true"></i> @lang('lang_v1.profit_by_locations')</a>
                    </li>

                    <li style="display:none;">
                        <a href="#profit_by_invoice" data-toggle="tab" aria-expanded="true"><i class="fa fa-file-alt" aria-hidden="true"></i> @lang('lang_v1.profit_by_invoice')</a>
                    </li>

                    <li style="display:none;"> 
                        <a href="#profit_by_date" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('lang_v1.profit_by_date')</a>
                    </li>
                    <li style="display:none;">
                        <a href="#profit_by_customer" data-toggle="tab" aria-expanded="true"><i class="fa fa-user" aria-hidden="true"></i> @lang('lang_v1.profit_by_customer')</a>
                    </li>
                    <li style="display:none;">
                        <a href="#profit_by_day" data-toggle="tab" aria-expanded="true"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('lang_v1.profit_by_day')</a>
                    </li>
                </ul>

                <div class="tab-content">
                 
                    <div class="tab-pane " id="profit_by_products"> 
                        @include('report.partials.profit_by_products')
                    </div>
                         <div class="tab-pane active" id="profit_by_ts">
                        @include('report.partials.profit_by_ts')
                    </div>
                    <div class="tab-pane" id="profit_by_categories">
                        @include('report.partials.profit_by_categories')
                    </div>

                    <div class="tab-pane" id="profit_by_brands">
                        @include('report.partials.profit_by_brands')
                    </div>

                    <div class="tab-pane" id="profit_by_locations">
                        @include('report.partials.profit_by_locations')
                    </div>

                    <div class="tab-pane" id="profit_by_invoice">
                        @include('report.partials.profit_by_invoice')
                    </div>

                    <div class="tab-pane" id="profit_by_date">
                        @include('report.partials.profit_by_date')
                    </div>

                    <div class="tab-pane" id="profit_by_customer">
                        @include('report.partials.profit_by_customer')
                    </div>

                    <div class="tab-pane" id="profit_by_day">
                        
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    

</section>
<!-- /.content -->
@stop
@section('javascript')
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>

<script type="text/javascript">
    $(document).ready( function() {
     
        var newTitle = "Profit/Loss Report"; // Set your new title here

            $('title').text(newTitle);
            
            
  profit_by_ts_datatable = $('#profit_by_ts_table').DataTable({
    processing: true,
    serverSide: true,
    "ajax": {
        "url": "/reports/get-profit/ts",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [
        { data: 'transaction_date', name: 'transaction_date' },
        {
            data: 'customer',
            name: 'customer',
            render: function(data, type, row) {
                return data ? data : row.supplier_business_name;
            }
        },
        { data: 'product', name: 'product' },
        { data: 'quantity', name: 'quantity' },
        {
            data: 'dpp_inc_tax',
            name: 'dpp_inc_tax',
            render: function(data, type, row) {
                // Set to 0 if null
                return data !== null ? parseFloat(data).toFixed(2) : '0.00';
            }
        },
        {
            data: null,
            name: 'calculated_profit',
            render: function (data, type, row) {
                return (parseFloat(row.quantity) * parseFloat(row.dpp_inc_tax)).toFixed(2);
            },
            title: 'Total P'
        },
        {
            data: 'unit_price_inc_tax',
            name: 'unit_price_inc_tax',
            render: function(data, type, row) {
                return parseFloat(data).toFixed(2);
            }
        },
        {
            data: null,
            name: 'calculated_sale_profit',
            render: function (data, type, row) {
                return (parseFloat(row.quantity) * parseFloat(row.unit_price_inc_tax)).toFixed(2);
            },
            title: 'Total S'
        },
        {
            data: null,
            name: 'profit_difference',
            render: function (data, type, row) {
                var calculatedProfit = parseFloat(row.quantity) * parseFloat(row.dpp_inc_tax || 0); // Ensure dpp_inc_tax is not null
                var calculatedSaleProfit = parseFloat(row.quantity) * parseFloat(row.unit_price_inc_tax);
                return (calculatedSaleProfit - calculatedProfit).toFixed(2);
            },
            title: 'Profit'
        }
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit_difference = 0;
        var total_commi_product = 0;
        for (var i = 0; i < data.length; i++) {
            var calculatedProfit = parseFloat(data[i].quantity) * parseFloat(data[i].dpp_inc_tax || 0); // Ensure dpp_inc_tax is not null
            var calculatedSaleProfit = parseFloat(data[i].quantity) * parseFloat(data[i].unit_price_inc_tax);
            total_profit_difference += calculatedSaleProfit - calculatedProfit;
            
                        total_commi_product += data[i].commission_subtotal ;

            
        }
       var  dics =0;
        if($("#total_sell_discount_val").val() != undefined ){
            dics = parseFloat($("#total_sell_discount_val").val()).toFixed(2);
    $('.footer_total_discout').html('Disc ' + parseFloat($("#total_sell_discount_val").val()).toFixed(2));
    }else{
        dics = parseFloat(0);
            $('.footer_total_discout').html('Disc ' + parseFloat(0));

    }
    var expess = 0;
    if($("#aaaaaaa").val() != undefined)
    {
        expess = parseFloat($("#aaaaaaa").val()).toFixed(2);
    }else{
        expess = parseFloat(0);
    }
        $('#profit_by_ts_table .footer_total').html(__currency_trans_from_en(total_profit_difference.toFixed(2)));
        $('#profit_by_ts_table .footer_total_exp').html(expess);
        $('#profit_by_ts_table .footer_total_comm').html(total_commi_product);
        $('#profit_by_ts_table .footer_total_pro').html( (parseFloat(total_profit_difference) - expess -dics -parseFloat(total_commi_product)).toFixed(2) );
        $('.finalprofit').html(  parseFloat(total_profit_difference) - expess -dics  -parseFloat(total_commi_product).toFixed(2) );
    
    
        console.log('Profit ',total_profit_difference);
        console.log('Expense ',$("#aaaaaaa").val());
        console.log('Discount ',$("#total_sell_discount_val").val());
        console.log('Shipping chnarge ',$("#total_sell_shipping_charge_val").val());
        console.log('Commision ',total_commi_product);
        
        
        
    }
});

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr('href');
            if ( target == '#profit_by_categories') {
                if(typeof profit_by_categories_datatable == 'undefined') {
                    profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/category",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'category', name: 'C.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_categories_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_categories_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_brands') {
                if(typeof profit_by_brands_datatable == 'undefined') {
                    profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/brand",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'brand', name: 'B.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_brands_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_brands_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_locations') {
                if(typeof profit_by_locations_datatable == 'undefined') {
                    profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/location",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'location', name: 'L.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_locations_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_locations_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_invoice') {
                if(typeof profit_by_invoice_datatable == 'undefined') {
                    profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/invoice",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'invoice_no', name: 'sale.invoice_no'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_invoice_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_invoice_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_date') {
                if(typeof profit_by_date_datatable == 'undefined') {
                    profit_by_date_datatable = $('#profit_by_date_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/date",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'transaction_date', name: 'sale.transaction_date'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_date_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_date_datatable.ajax.reload();
                }
            } else if (target == '#profit_by_customer') {
                
                if(typeof profit_by_customers_table == 'undefined') {
                    profit_by_customers_table = $('#profit_by_customer_table').DataTable({
                        processing: true,
                        serverSide: true,
                        "ajax": {
                            "url": "/reports/get-profit/customer",
                            "data": function ( d ) {
                                d.start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');
                                d.end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                                d.location_id = $('#profit_loss_location_filter').val();
                            }
                        },
                        columns: [
                            { data: 'customer', name: 'CU.name'  },
                            { data: 'gross_profit', "searchable": false},
                        ],
                        footerCallback: function ( row, data, start, end, display ) {
                            var total_profit = 0;
                            for (var r in data){
                                total_profit += $(data[r].gross_profit).data('orig-value') ? 
                                parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                            }

                            $('#profit_by_customer_table .footer_total').html(__currency_trans_from_en(total_profit));
                        },
                    });
                } else {
                    profit_by_customers_table.ajax.reload();
                }
                
            } else if (target == '#profit_by_ts') {
               if (typeof profit_by_ts_datatable == 'undefined') {
                  
                   
  profit_by_ts_datatable = $('#profit_by_ts_table').DataTable({
    processing: true,
    serverSide: true,
    "ajax": {
        "url": "/reports/get-profit/ts",
        "data": function (d) {
            d.start_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            d.end_date = $('#profit_loss_date_filter')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            d.location_id = $('#profit_loss_location_filter').val();
        }
    },
    columns: [
        { data: 'transaction_date', name: 'transaction_date' },
        {
            data: 'customer',
            name: 'customer',
            render: function(data, type, row) {
                return data ? data : row.supplier_business_name;
            }
        },
        { data: 'product', name: 'product' },
        { data: 'quantity', name: 'quantity' },
        {
            data: 'dpp_inc_tax',
            name: 'dpp_inc_tax',
            render: function(data, type, row) {
                // Set to 0 if null
                return data !== null ? parseFloat(data).toFixed(2) : '0.00';
            }
        },
        {
            data: null,
            name: 'calculated_profit',
            render: function (data, type, row) {
                return (parseFloat(row.quantity) * parseFloat(row.dpp_inc_tax)).toFixed(2);
            },
            title: 'Total P'
        },
        {
            data: 'unit_price_inc_tax',
            name: 'unit_price_inc_tax',
            render: function(data, type, row) {
                return parseFloat(data).toFixed(2);
            }
        },
        {
            data: null,
            name: 'calculated_sale_profit',
            render: function (data, type, row) {
                return (parseFloat(row.quantity) * parseFloat(row.unit_price_inc_tax)).toFixed(2);
            },
            title: 'Total S'
        },
        {
            data: null,
            name: 'profit_difference',
            render: function (data, type, row) {
                var calculatedProfit = parseFloat(row.quantity) * parseFloat(row.dpp_inc_tax || 0); // Ensure dpp_inc_tax is not null
                var calculatedSaleProfit = parseFloat(row.quantity) * parseFloat(row.unit_price_inc_tax);
                return (calculatedSaleProfit - calculatedProfit).toFixed(2);
            },
            title: 'Profit'
        }
    ],
    footerCallback: function (row, data, start, end, display) {
        var total_profit_difference = 0;
        var total_commi_product = 0;
        for (var i = 0; i < data.length; i++) {
            var calculatedProfit = parseFloat(data[i].quantity) * parseFloat(data[i].dpp_inc_tax || 0); // Ensure dpp_inc_tax is not null
            var calculatedSaleProfit = parseFloat(data[i].quantity) * parseFloat(data[i].unit_price_inc_tax);
            total_profit_difference += calculatedSaleProfit - calculatedProfit;
            
                        total_commi_product += data[i].commission_subtotal ;

            
        }
        $('#profit_by_ts_table .footer_total').html(__currency_trans_from_en(total_profit_difference.toFixed(2)));
        $('#profit_by_ts_table .footer_total_exp').html(($("#aaaaaaa").val()));
        $('#profit_by_ts_table .footer_total_comm').html(total_commi_product);
        $('#profit_by_ts_table .footer_total_pro').html((parseFloat(total_profit_difference) - parseFloat($("#aaaaaaa").val())-parseFloat($("#total_sell_discount_val").val()) -parseFloat(total_commi_product)).toFixed(2) );
        $('.finalprofit').html((parseFloat(total_profit_difference) - parseFloat($("#aaaaaaa").val())-parseFloat($("#total_sell_discount_val").val()) -parseFloat(total_commi_product)).toFixed(2) );
    
    
    
        console.log('Profit ',total_profit_difference);
        console.log('Expense ',$("#aaaaaaa").val());
        console.log('Discount ',$("#total_sell_discount_val").val());
        console.log('Shipping chnarge ',$("#total_sell_shipping_charge_val").val());
        console.log('Commision ',total_commi_product);
        
        
        
    }
});

} else {
    profit_by_ts_datatable.ajax.reload();
}

                
            } else if (target == '#profit_by_day') {
                var start_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .startDate.format('YYYY-MM-DD');

                var end_date = $('#profit_loss_date_filter')
                                    .data('daterangepicker')
                                    .endDate.format('YYYY-MM-DD');
                var location_id = $('#profit_loss_location_filter').val();

                var url = '/reports/get-profit/day?start_date=' + start_date + '&end_date=' + end_date + '&location_id=' + location_id;
                $.ajax({
                        url: url,
                        dataType: 'html',
                        success: function(result) {
                           $('#profit_by_day').html(result); 
                            profit_by_days_table = $('#profit_by_day_table').DataTable({
                                    "searching": false,
                                    'paging': false,
                                    'ordering': false,
                            });
                            var total_profit = sum_table_col($('#profit_by_day_table'), 'gross-profit');
                           $('#profit_by_day_table .footer_total').text(total_profit);
                            __currency_convert_recursively($('#profit_by_day_table'));
                        },
                    });
            } else if (target == '#profit_by_products') {
                 profit_by_products_table = $('#profit_by_products_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "/reports/get-profit/product",
                    "data": function ( d ) {
                        d.start_date = $('#profit_loss_date_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#profit_loss_date_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.location_id = $('#profit_loss_location_filter').val();
                    }
                },
                columns: [
                    { data: 'product', name: 'product'  },
                    { data: 'gross_profit', "searchable": false},
                ],
                footerCallback: function ( row, data, start, end, display ) {
                    var total_profit = 0;
                    for (var r in data){
                        total_profit += $(data[r].gross_profit).data('orig-value') ? 
                        parseFloat($(data[r].gross_profit).data('orig-value')) : 0;
                    }

                    $('#profit_by_products_table .footer_total').html(__currency_trans_from_en(total_profit));
                }
            });

 
            }
        });
    });
</script>

@endsection
