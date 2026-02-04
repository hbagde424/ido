@php
    $common_settings = session()->get('business.common_settings');
@endphp
<div class="row">
    <div class="col-md-12">

        <h4>{{ $stock_details['variation'] }}</h4>
    </div>

    <div class="col-md-12 col-xs-12">


        <strong>@lang('lang_v1.totals')</strong>
        <table class="table table-condensed">
            <tr>
                <th <?php if($product->alert_quantity >= $stock_details['current_stock'] ){  ?> style="font-size: 30px;color:red;"
                    <?php }else{ ?>style="color:blue;font-size: 30px;" <?php } ?>>@lang('report.current_stock') - <span
                        class="display_currency" data-is_quantity="true"
                        style="margin-left:15px;">{{ $stock_details['current_stock'] }}</span>
                    {{ $stock_details['unit'] }}</th>
                <td style="font-size: 25px;">

                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6 col-xs-6">
        <strong>@lang('lang_v1.quantities_in')</strong>
        <table class="table table-condensed">
            <tr>
                <th>@lang('report.total_purchase')</th>
                <td>
                    <span class="display_currency" data-is_quantity="true">{{ $stock_details['total_purchase'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.opening_stock')</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_opening_stock'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.total_sell_return')</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_sell_return'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.in'))</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_purchase_transfer'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
        </table>
    </div>
    <div class="col-md-6 col-xs-6">
        <strong>@lang('lang_v1.quantities_out')</strong>
        <table class="table table-condensed">
            <tr>
                <th>@lang('lang_v1.total_sold')</th>
                <td>
                    <span class="display_currency" data-is_quantity="true">{{ $stock_details['total_sold'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
            <tr>
                <th>@lang('report.total_stock_adjustment')</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_adjusted'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
            <tr>
                <th>@lang('lang_v1.total_purchase_return')</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_purchase_return'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>

            <tr>
                <th>@lang('lang_v1.stock_transfers') (@lang('lang_v1.out'))</th>
                <td>
                    <span class="display_currency"
                        data-is_quantity="true">{{ $stock_details['total_sell_transfer'] }}</span>
                    {{ $stock_details['unit'] }}
                </td>
            </tr>
        </table>
    </div>

</div>
<div class="row">
    <div class="col-md-12">
        <hr>
        <table class="table table-slim" id="stock_history_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.date')</th>
                    <th>@lang('purchase.ref_no')</th>
                    <th>@lang('lang_v1.customer_supplier_info')</th>
                    <th>@lang('lang_v1.type')</th>
                    <th>@lang('lang_v1.quantity_change')</th>
                    @if (!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.quantity_change') (@lang('lang_v1.secondary_unit'))</th>
                    @endif
                    <th>@lang('lang_v1.new_quantity')</th>
                    @if (!empty($common_settings['enable_secondary_unit']))
                        <th>@lang('lang_v1.new_quantity') (@lang('lang_v1.secondary_unit'))</th>
                    @endif

                </tr>
            </thead>
            <tbody>
                @forelse($stock_history as $history)
                    <tr>
                        <td>
                            @if ($history['type_label'] == 'Opening Stock')
                                <!--User this format( Y-M-d H:m)-->
                                <!--<input type="text" class="from-control" id="tdatetime" value="{{ @format_datetime($history['date']) }}">-->
                                <!--<button type="button" onclick="quintityupdate()">Stock Update</button>-->
                            @endif

                            {{ @format_date($history['date']) }}
                        </td>
                        <td>
                            {{ $history['ref_no'] }}

                            @if (!empty($history['additional_notes']))
                                @if (!empty($history['ref_no']))
                                    <br>
                                @endif
                                {{ $history['additional_notes'] }}
                            @endif
                        </td>

                        <td>
                            {{ $history['contact_name'] ?? '--' }}
                            @if (!empty($history['supplier_business_name']))
                                - {{ $history['supplier_business_name'] }}
                            @endif
                        </td>

                        <td>{{ $history['type_label'] }}</td>


                        @if ($history['quantity_change'] > 0)
                            <td class="text-success">

                                @if ($history['type_label'] == 'Opening Stock')
                                    <input type="text" name="quantity_change" id="quantity_change"
                                        value="{{ $history['quantity_change'] }}">
                                    <input type="hidden" value="{{ $history['purchaseid'] }}" id="purchaseid">
                                    <input type="hidden" value="{{ $history['transaction_id'] }}" id="transaction_id">


                                    <button class="no print" type="button" onclick="quintityupdate()">StockUpdate</button>
                                @else
                                    +<span class="display_currency"
                                        data-is_quantity="true">{{ $history['quantity_change'] }}</span>
                                @endif


                            </td>
                        @else
                            <td class="text-danger">

                                @if ($history['type_label'] == 'Opening Stock')
                                    <input type="text" name="quantity_change" id="quantity_change"
                                        value="{{ $history['quantity_change'] }}">
                                    <input type="hidden" value="{{ $history['purchaseid'] }}" id="purchaseid">
                                    <input type="hidden" value="{{ $history['transaction_id'] }}" id="transaction_id">
                                @else
                                    <span class="display_currency  text-danger"
                                        data-is_quantity="true">{{ $history['quantity_change'] }}</span>
                                @endif
                        @endif

                        @if (!empty($common_settings['enable_secondary_unit']))
                            @if ($history['quantity_change'] > 0)
                                <td class="text-success">
                                    @if (!empty($history['purchase_secondary_unit_quantity']))
                                        +<span class="display_currency"
                                            data-is_quantity="true">{{ $history['purchase_secondary_unit_quantity'] }}</span>
                                        {{ $stock_details['second_unit'] }}
                                    @endif
                                </td>
                            @else
                                <td class="text-danger">
                                    @if (!empty($history['sell_secondary_unit_quantity']))
                                        -<span class="display_currency"
                                            data-is_quantity="true">{{ $history['sell_secondary_unit_quantity'] }}</span>
                                        {{ $stock_details['second_unit'] }}
                                    @endif
                                </td>
                            @endif
                        @endif
                        <td>
                            <span class="display_currency" data-is_quantity="true">{{ $history['stock'] }}</span>
                        </td>
                        @if (!empty($common_settings['enable_secondary_unit']))
                            <td>
                                @if (!empty($stock_details['second_unit']))
                                    <span class="display_currency"
                                        data-is_quantity="true">{{ $history['stock_in_second_unit'] }}</span>
                                    {{ $stock_details['second_unit'] }}
                                @endif
                            </td>
                        @endif



                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">
                            @lang('lang_v1.no_stock_history_found')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
    function quintityupdate() {
        var quantity = $("#quantity_change").val();
        var purchaseId = $("#purchaseid").val();
        var transaction_id = $("#transaction_id").val();
        var tdatetime = $("#tdatetime").val();
        // Confirm update
        if (!confirm("Are you sure you want to update the quantity?")) {
            return; // Do nothing if the user cancels the confirmation
        }
        $.ajax({
            url: '/products/stockupdate',
            method: 'POST',
            data: {
                quantity: quantity,
                id: purchaseId,
                transaction_id: transaction_id,
                tdatetime: tdatetime,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    alert('Quantity updated successfully!');
                    window.location.reload();
                    // You may want to refresh the table or perform any further actions here
                } else {
                    alert('Failed to update quantity.');
                }
            },
            error: function(xhr, status, error) {
                alert('Failed to update quantity. Please try again later.');
            }
        });
    }
</script>
