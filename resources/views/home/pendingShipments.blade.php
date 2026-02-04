@extends('layouts.app')
@section('title', __('Pending Shipments'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header content-header-custom"></section>
<!-- Main content -->
<section class="content content-custom no-print">
    <br>
    @if(auth()->user()->can('dashboard.data'))

  
    
 
        @if(auth()->user()->can('access_pending_shipments_only') || auth()->user()->can('access_shipping') || auth()->user()->can('access_own_shipping') )
            @component('components.widget', ['class' => 'box-warning'])
              @slot('icon')
                  <i class="fas fa-list-alt text-yellow fa-lg" aria-hidden="true"></i>
              @endslot
              @slot('title')
                  @lang('lang_v1.pending_shipments')
              @endslot
                <div class="row " >
                    @if(count($all_locations) > 1)
                        <div class="col-md-4 col-sm-6 col-md-offset-8 mb-10">
                            {!! Form::select('pending_shipments_location', $all_locations, null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.select_location'), 'id' => 'pending_shipments_location']); !!}
                        </div>
                    @endif
                    <div class="col-md-12">  
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped ajax_view" id="shipments_table">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.action')</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('sale.invoice_no')</th>
                                        <th>@lang('sale.customer_name')</th>
                                        <th>@lang('lang_v1.contact_no')</th>
                                        <th>@lang('sale.location')</th>
                                        <th>@lang('lang_v1.shipping_status')</th>
                                        @if(!empty($custom_labels['shipping']['custom_field_1']))
                                            <th>
                                                {{$custom_labels['shipping']['custom_field_1']}}
                                            </th>
                                        @endif
                                        @if(!empty($custom_labels['shipping']['custom_field_2']))
                                            <th>
                                                {{$custom_labels['shipping']['custom_field_2']}}
                                            </th>
                                        @endif
                                        @if(!empty($custom_labels['shipping']['custom_field_3']))
                                            <th>
                                                {{$custom_labels['shipping']['custom_field_3']}}
                                            </th>
                                        @endif
                                        @if(!empty($custom_labels['shipping']['custom_field_4']))
                                            <th>
                                                {{$custom_labels['shipping']['custom_field_4']}}
                                            </th>
                                        @endif
                                        @if(!empty($custom_labels['shipping']['custom_field_5']))
                                            <th>
                                                {{$custom_labels['shipping']['custom_field_5']}}
                                            </th>
                                        @endif
                                        <th>@lang('sale.payment_status')</th>
                                        <th>@lang('restaurant.service_staff')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div> 
                </div>
            @endcomponent
        @endif
         
 
        @if(!empty($widgets['after_dashboard_reports']))
          @foreach($widgets['after_dashboard_reports'] as $widget)
            {!! $widget !!}
          @endforeach
        @endif

    @endif
   <!-- can('dashboard.data') end -->
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
    <script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
    @includeIf('sales_order.common_js')
    @includeIf('purchase_order.common_js')
    @if(!empty($all_locations))
        {!! $sells_chart_1->script() !!}
        {!! $sells_chart_2->script() !!}
    @endif
    <script type="text/javascript">
        $(document).ready( function(){
        sales_order_table = $('#sales_order_table').DataTable({
          processing: true,
          serverSide: true,
          scrollY: "75vh",
          scrollX:        true,
          scrollCollapse: true,
          aaSorting: [[1, 'desc']],
          "ajax": {
              "url": '{{action([\App\Http\Controllers\SellController::class, 'index'])}}?sale_type=sales_order',
              "data": function ( d ) {
                    d.for_dashboard_sales_order = true;

                    if ($('#so_location').length > 0) {
                        d.location_id = $('#so_location').val();
                    }
                }
          },
          columnDefs: [ {
              "targets": 7,
              "orderable": false,
              "searchable": false
          } ],
          columns: [
              { data: 'action', name: 'action'},
              { data: 'transaction_date', name: 'transaction_date'  },
              { data: 'invoice_no', name: 'invoice_no'},
              { data: 'conatct_name', name: 'conatct_name'},
              { data: 'mobile', name: 'contacts.mobile'},
              { data: 'business_location', name: 'bl.name'},
              { data: 'status', name: 'status'},
              { data: 'shipping_status', name: 'shipping_status'},
              { data: 'so_qty_remaining', name: 'so_qty_remaining', "searchable": false},
              { data: 'added_by', name: 'u.first_name'},
          ]
        });

        @if(auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)

            // Cash Flow Table
            cash_flow_table = $('#cash_flow_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                        "url": "{{action([\App\Http\Controllers\AccountController::class, 'cashFlow'])}}",
                        "data": function ( d ) {
                            d.type = 'credit';
                            d.only_payment_recovered = true;
                        }
                    },
                "ordering": false,
                "searching": false,
                columns: [
                    {data: 'operation_date', name: 'operation_date'},
                    {data: 'account_name', name: 'account_name'},
                    {data: 'sub_type', name: 'sub_type'},
                    {data: 'method', name: 'TP.method'},
                    {data: 'payment_details', name: 'payment_details', searchable: false},
                    {data: 'credit', name: 'amount'},
                    {data: 'balance', name: 'balance'},
                    {data: 'total_balance', name: 'total_balance'},
                ],
                "fnDrawCallback": function (oSettings) {
                    __currency_convert_recursively($('#cash_flow_table'));
                },
                "footerCallback": function ( row, data, start, end, display ) {
                    var footer_total_credit = 0;

                    for (var r in data){
                        footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                    }
                    $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                }
            });
        @endif

        $('#so_location').change( function(){
            sales_order_table.ajax.reload();
        });
        @if(!empty($common_settings['enable_purchase_order']))
          //Purchase table
          purchase_order_table = $('#purchase_order_table').DataTable({
              processing: true,
              serverSide: true,
              aaSorting: [[1, 'desc']],
              scrollY: "75vh",
              scrollX:        true,
              scrollCollapse: true,
              ajax: {
                  url: '{{action([\App\Http\Controllers\PurchaseOrderController::class, 'index'])}}',
                  data: function(d) {
                      d.from_dashboard = true;

                        if ($('#po_location').length > 0) {
                            d.location_id = $('#po_location').val();
                        }
                  },
              },
              columns: [
                  { data: 'action', name: 'action', orderable: false, searchable: false },
                  { data: 'transaction_date', name: 'transaction_date' },
                  { data: 'ref_no', name: 'ref_no' },
                  { data: 'location_name', name: 'BS.name' },
                  { data: 'name', name: 'contacts.name' },
                  { data: 'status', name: 'transactions.status' },
                  { data: 'po_qty_remaining', name: 'po_qty_remaining', "searchable": false},
                  { data: 'added_by', name: 'u.first_name' }
              ]
            })

            $('#po_location').change( function(){
                purchase_order_table.ajax.reload();
            });
        @endif

        @if(!empty($common_settings['enable_purchase_requisition']))
          //Purchase table
          purchase_requisition_table = $('#purchase_requisition_table').DataTable({
              processing: true,
              serverSide: true,
              aaSorting: [[1, 'desc']],
              scrollY: "75vh",
              scrollX:        true,
              scrollCollapse: true,
              ajax: {
                  url: '{{action([\App\Http\Controllers\PurchaseRequisitionController::class, 'index'])}}',
                  data: function(d) {
                      d.from_dashboard = true;

                        if ($('#pr_location').length > 0) {
                            d.location_id = $('#pr_location').val();
                        }
                  },
              },
              columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'location_name', name: 'BS.name' },
                    { data: 'status', name: 'status' },
                    { data: 'delivery_date', name: 'delivery_date' },
                    { data: 'added_by', name: 'u.first_name' },
              ]
            })

            $('#pr_location').change( function(){
                purchase_requisition_table.ajax.reload();
            });

            $(document).on('click', 'a.delete-purchase-requisition', function(e) {
                e.preventDefault();
                swal({
                    title: LANG.sure,
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                }).then(willDelete => {
                    if (willDelete) {
                        var href = $(this).attr('href');
                        $.ajax({
                            method: 'DELETE',
                            url: href,
                            dataType: 'json',
                            success: function(result) {
                                if (result.success == true) {
                                    toastr.success(result.msg);
                                    purchase_requisition_table.ajax.reload();
                                } else {
                                    toastr.error(result.msg);
                                }
                            },
                        });
                    }
                });
            });
        @endif

        sell_table = $('#shipments_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            scrollY:        "75vh",
            scrollX:        true,
            scrollCollapse: true,
            "ajax": {
                "url": '{{action([\App\Http\Controllers\SellController::class, 'index'])}}',
                "data": function ( d ) {
                    d.only_pending_shipments = true;
                    if ($('#pending_shipments_location').length > 0) {
                        d.location_id = $('#pending_shipments_location').val();
                    }
                }
            },
            columns: [
                { data: 'action', name: 'action', searchable: false, orderable: false},
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'conatct_name', name: 'conatct_name'},
                { data: 'mobile', name: 'contacts.mobile'},
                { data: 'business_location', name: 'bl.name'},
                { data: 'shipping_status', name: 'shipping_status'},
                @if(!empty($custom_labels['shipping']['custom_field_1']))
                    { data: 'shipping_custom_field_1', name: 'shipping_custom_field_1'},
                @endif
                @if(!empty($custom_labels['shipping']['custom_field_2']))
                    { data: 'shipping_custom_field_2', name: 'shipping_custom_field_2'},
                @endif
                @if(!empty($custom_labels['shipping']['custom_field_3']))
                    { data: 'shipping_custom_field_3', name: 'shipping_custom_field_3'},
                @endif
                @if(!empty($custom_labels['shipping']['custom_field_4']))
                    { data: 'shipping_custom_field_4', name: 'shipping_custom_field_4'},
                @endif
                @if(!empty($custom_labels['shipping']['custom_field_5']))
                    { data: 'shipping_custom_field_5', name: 'shipping_custom_field_5'},
                @endif
                { data: 'payment_status', name: 'payment_status'},
                { data: 'waiter', name: 'ss.first_name', @if(empty($is_service_staff_enabled)) visible: false @endif }
            ],
            "fnDrawCallback": function (oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },
            createdRow: function( row, data, dataIndex ) {
                $( row ).find('td:eq(4)').attr('class', 'clickable_td');
            }
        });

        $('#pending_shipments_location').change( function(){
            sell_table.ajax.reload();
        });
    });
    </script>
@endsection

