@extends('layouts.app')
@section('title', __( 'lang_v1.Product-casting'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>Product-Costing
    </h1>
</section>
    <style>
        /* Custom styles for print */
        @media print {
            table {
                border-collapse: collapse !important;
            }
            table, th, td {
                border: 1px solid black !important;
            }
        }
    </style>
<!-- Main content -->
<section class="content no-print">

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Product Costing'])
        @can('direct_sell.access')
            @slot('tool')
                <div class="box-tools">
                    <a class="btn btn-block btn-primary" href="{{ route('product-costings.create') }}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
        @if(auth()->user()->can('direct_sell.view') ||  auth()->user()->can('view_own_sell_only') ||  auth()->user()->can('view_commission_agent_sell'))
        @php
            $custom_labels = json_decode(session('business.custom_labels'), true);
         @endphp
            <table class="table table-bordered table-striped" id="product_casting_table">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Sno.</th>
                        <th>Date</th>
                        <th>location</th>
                        <th>Container Number</th>
                        <th>BN Number</th>
                    </tr>
                </thead>
                <tbody>
                    @if($productCostings)
                    @foreach($productCostings as $costing)
                    <tr>
                        <td>
                            <div class="btn-group ashokraj">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false" fdprocessedid="z8rz2g">Actions<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">
                                    <li><a href="{{ route('product-costings.show', $costing->id) }}" ><i class="fas fa-eye" aria-hidden="true"></i> View</a></li>
                                    <li><a href="{{ route('product-costings.edit', $costing->id) }}"><i class="fas fa-edit"></i> Edit</a></li>
                                   <li>

                                        <form id="delete-form-{{ $costing->id }}" action="{{ route('product-costings.delete', $costing->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                        </form>

                                        <a href="#" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $costing->id }}').submit();">
                                        <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </li> 
                                    
                                </ul>
                            </div>
                        </td>
                        <td>{{$loop->iteration}}</td>
                        <td>{{ $costing->date }}</td>
                        <td>{{ $costing->location }}</td>
                        <td>{{ $costing->container_number }}</td>
                        <td>{{ $costing->bn_number }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td class="text-center">No data found</td>
                    </tr>
                    @endif

                </tbody>
                <!-- <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td ><strong>@lang('sale.total'):</strong></td>
                        <td class="footer_payment_status_count" colspan="2"></td>
                        <td class="payment_method_count" colspan="3"></td>
                        <td class="footer_sale_total"></td>
                        <td ></td>
                        <td class="footer_total_paid"></td>
                        <td class="footer_total_remaining"></td>
                        <td class="footer_total_sell_return_due"></td>
                        
                        <td ></td>
                        <td class="service_type_count"></td>
                        <td ></td>
                    </tr>
                </tfoot> -->
            </table>
        @endif
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->

@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    
    $('#sell_list_filter_date_range_new').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range_new').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    
    
    $('#sell_list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#sell_list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            sell_table.ajax.reload();
        }
    );
    $('#sell_list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#sell_list_filter_date_range').val('');
        sell_table.ajax.reload();
    });

    $('#product_casting_table').DataTable();

    $(document).on('change', '#sell_list_filter_location_id, #sell_list_filter_customer_id, #sell_list_filter_payment_status, #created_by, #sales_cmsn_agnt, #service_staffs, #shipping_status, #sell_list_filter_source',  function() {
        sell_table.ajax.reload();
    });

    $('#only_subscriptions').on('ifChanged', function(event){
        sell_table.ajax.reload();
    });
});
</script>
<script>
        // jQuery code to update HTML title onload
        $(document).ready(function() {
            var newTitle = "Product-Costing"; // Set your new title here
            $('title').text(newTitle);
        });
    </script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection