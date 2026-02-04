 <button type="button" class="btn btn-primary no-print btn-sm" id="printButton" aria-label="Print"><i
                    class="fas fa-print"></i> @lang( 'messages.print' )
            </button>
            @auth
            <a href="{{action([\App\Http\Controllers\SellController::class, 'index'])}}"
                class="btn btn-success no-print btn-sm"><i class="fas fa-backward"></i>
            </a>
            @endauth

@foreach($receipt_detail as $index => $receipt_details)

 @if($index > 0)
        <div style="page-break-before: always;"></div>
    @endif
    
    
@extends('layouts.guest')
@section('title', $title)
@section('content')
 
<div class="container">
    <div class="spacer"></div>
    <div class="row">
        <div class="col-md-12 text-right mb-12">
            @if(!empty($payment_link))
            <a href="{{$payment_link}}" class="btn btn-info no-print" style="margin-right: 20px;"><i
                    class="fas fa-money-check-alt" title="@lang('lang_v1.pay')"></i> @lang('lang_v1.pay')
            </a>
            @endif
           
        </div>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2 col-sm-12" style="border: 1px solid #ccc;">
            <div class="spacer"></div>
            <div id="invoice_content">
                 @php
                $custom_labels = json_decode(session('business.custom_labels'), true);
                @endphp


    

                <!DOCTYPE html>
                <html>

                <head>
                    <meta charset="utf-8">

                    <meta http-equiv="X-UA-Compatible" content="IE=edge">

                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link rel="stylesheet"
                        href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css"
                        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
                        crossorigin="anonymous">
                    <link rel="stylesheet"
                        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
                    <style type="text/css">
                        @media print {

                            /* Override grayscale */
                            body {
                                -webkit-print-color-adjust: exact;
                                /* For WebKit browsers */
                                color-adjust: exact;
                                /* Standard syntax */
                            }

                            /* Additional styling for printed content */
                            /* Add any specific styles you want for printing */
                        }

                        ul {
                            list-style-type: none !important;
                        }

                        tr {
                            padding-bottom: 20px;
                        }
                    </style>
                </head>

                <body
                    style="background:#fffff:  font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;">
                    <section><div><div style="display: flex;"><img src="https://gaurishankar369.in/shreeshiv/public/uploads/img/invoice3.png" alt="Product image" class="product-thumbnail" style="display: block;
  margin-left: auto;
  margin-right: auto;
  height:20%;
  width:20%;
  "></div></div></section>
                    <div class="container-div" style="display: flex;">
                        <div class="item2" style="border:2px black solid; border-radius: 25px;width:50%;text-align:center;
                        color:black !important;
                        padding: 10px;
                        margin: 5px;">
                            <span style="font-size:15px;"
                                class="text-center"><b>@if(!empty($receipt_details['invoice_layout']['invoice_no_prefix']))
                                    <b style="font-weight:bold;"> {!! $receipt_details['invoice_layout']['invoice_no_prefix'] !!}:</b>
                                    @endif
                                    {{$receipt_details['transaction']['invoice_no']}}</b>
                        </div>
                        <div class="item2" style="border:2px black solid; border-radius: 25px;width:50%;text-align:center;
                        color:black !important;
                        padding: 10px;
                        margin: 5px;">
                            <b style="font-weight:bold;font-size:15px;" class="text-center">Date:

                                <?php  
                                $date = Carbon::parse($receipt_details['transaction']['transaction_date']);

                                // Format the date without time
                                echo $formattedDate = $date->format('Y-m-d');



                                ?>

                            </b></td>
                        </div>
                    </div>


                    <div>
                        <h3>
                            @if(!empty($receipt_details['customer_info']))

                            <p style="font-size:18px;"> {{ $receipt_details['invoice_layout']['customer_label'] }}: {!!
                                $receipt_details['customer_info'] !!}</p>
                            @endif


                        </h3>
                    </div>
                    <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;border-collapse: collapse;">

                        <table
                            style="width: 100% ;font-size:15px;border: 1px solid black;border-collapse: collapse;font-family: &#39;Source Sans Pro&#39;,&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;">
                            <thead style="border: 1px solid black;color:black !important;">
                                <tr style="font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;">
                                    <th
                                        style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1px;border-top:0;width: 10px;">
                                        S.No. </th>
                                    <th
                                        style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 250px;">
                                        {{$receipt_details['invoice_layout']['table_product_label']}} </th>

                                    <th
                                        style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 28px;">
                                        {{$receipt_details['invoice_layout']['table_qty_label']}}</th>
                                    <th
                                        style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 27px;">
                                        {{$receipt_details['invoice_layout']['table_unit_price_label']}}</th>


                                    <th
                                        style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 41px;">
                                        {{$receipt_details['invoice_layout']['table_subtotal_label']}}</th>

                                </tr>
                            </thead>
                            <tbody>
                                
                               
                                 @foreach($receipt_details['lines']['lines'] as $key => $line)
                             
                                 <tr
                                    style="margin-bottom: 20px;border: 1px solid #696969;font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;color:black;">

                                    <td style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;"
                                    valign="top"> {{ $loop->iteration }}</td>
                                    <td
                                        style="border: 1px solid black;text-align: left;padding: 4px 5px 8px 5px;border-bottom:0;border-top:0;font-size: 12px;">
                                        {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}}
                                        @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif
                                        @if(!empty($line['brand'])), {{$line['brand']}} @endif
                                        @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
                                        @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}}
                                        @endif
                                        @if(!empty($line['product_description']))
                                        <small>
                                            {!!$line['product_description']!!}

                                        </small>
                                        @endif
                                        @if(!empty($line['sell_line_note']))
                                        {!!$line['sell_line_note']!!}
                                        @endif
                                    </td>
                               
                                    <td
                                        style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 12px;">
                                        {{$line['quantity']}} {{$line['units']}}</td>
                                    <td
                                        style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 12px;">
                                        {{$line['unit_price_before_discount']}}</td>

                                    

                                    @if(!empty($line['line_discount_percent']))
                                    ({{$line['line_discount_percent']}}%)
                                    @endif</td>
                                    <td
                                        style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 12px;">
                                        {{$line['line_total']}}</td>


                                </tr>

                                @endforeach


                                <tr style="color:black;">
                                    <td style="border: 1px solid black;text-align: center;padding: 5px 5px;"><b
                                            style="font-weight:bold;"></b></td>
                                    <td colspan="" style="border: 1px solid black;padding: 5px 5px;"> <span
                                            id="totalword"></span></td>
                                    <td colspan="2"
                                        style="border: 1px solid black;text-align: center;padding: 5px 5px;font-size: 12px;">
                                        <b>Sub-Total<b />
                                    </td>
                                    <td
                                        style="border: 1px solid black;text-align: center;padding: 5px 5px;font-size: 12px;">
                                        {{number_format($receipt_details['transaction']['total_before_tax'] , 2)}}</td>
                                </tr>
                            <tbody>
                        </table>



                        <br>
                        <div class="row" style="color: #000000 !important;font-size:12px;">

                            <div class="col-xs-6"><b>Maguilaye diadieufeul thie kolouté bi</b><br><b>Merci pour votre
                                    entreprise</b><br><br><br><br><br>
                                <br><b>Term & Condition:</b> {{$receipt_details['invoice_layout']['additional_notes']}}
                            </div>
                            <div class="col-xs-6">
                                <div class="table-responsive">
                                    <table class="table table-slim">
                                        <tbody>
                                            <tr>
                                                <th style="width:50%">
                                                    {!! $receipt_details['invoice_layout']['table_subtotal_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                   {{number_format($receipt_details['transaction']['total_before_tax'], 2)}}
                                                </td>
                                            </tr>
                                            @if(!empty($receipt_details['total_exempt_uf']))
                                            <tr>
                                                <th style="width:50%">
                                                    @lang('lang_v1.exempt')
                                                </th>
                                                <td class="text-right">
                                                    {{$receipt_details['total_exempt']}}
                                                </td>
                                            </tr>
                                            @endif
                                       
                                            @if(!empty($receipt_details['transaction']['shipping_charges']))
                                            <tr>
                                                <th style="width:50%">
                                                    {{trans('sale.shipping_charges')}}
                                                </th>
                                                <td class="text-right">
                                                     
                                                     {{number_format( $receipt_details['transaction']['shipping_charges'], 2)}}
                                                </td>
                                            </tr>
                                            @endif

                                            @if(!empty($receipt_details['packing_charge']))
                                            <tr>
                                                <th style="width:50%">
                                                    {!! $receipt_details['invoice_layout']['packing_charge_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                    {{$receipt_details['packing_charge']}}
                                                </td>
                                            </tr>
                                            @endif

                                          
                                            @if( !empty($receipt_details['discount']) )
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['invoice_layout']['discount_label'] !!}
                                                </th>

                                                <td class="text-right">
                                                    (-) {{number_format($receipt_details['discount'] , 2)}}
                                                </td>
                                            </tr>
                                            @endif

                                            @if( !empty($receipt_details['total_line_discount']) )
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['invoice_layout']['line_discount_label'] !!}
                                                </th>

                                                <td class="text-right">
                                                    (-) {{number_format($receipt_details['total_line_discount'], 2)}}
                                                </td>
                                            </tr>
                                            @endif

                                            @if( !empty($receipt_details['additional_expenses']) )
                                            @foreach($receipt_details['additional_expenses'] as $key => $val)
                                            <tr>
                                                <td>
                                                    {{$key}}:
                                                </td>

                                                <td class="text-right">
                                                    (+) {{$val}}
                                                </td>
                                            </tr>
                                            @endforeach
                                            @endif

                                            @if( !empty($receipt_details['reward_point_label']) )
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['reward_point_label'] !!}
                                                </th>

                                                <td class="text-right">
                                                    (-) {{$receipt_details['reward_point_amount']}}
                                                </td>
                                            </tr>
                                            @endif
 
                                            @if( !empty($receipt_details['tax']) )
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['tax_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                    (+) {{$receipt_details['tax']}}
                                                </td>
                                            </tr>
                                            @endif

                                            @if( $receipt_details['round_off_amount'] > 0)
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['round_off_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                    {{$receipt_details['round_off']}}
                                                </td>
                                            </tr>
                                            @endif

                                       
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['invoice_layout']['total_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                    {{number_format($receipt_details['transaction']['final_total'] , 2)}}
                                                </td>

                                            </tr>

                                            <tr>
                                                <th>
                                                    Total Paid
                                                </th>
                                                <td class="text-right">
                                                    {{$receipt_details['total_paid']}}
                                                </td>

                                            </tr>
                                            <tr>
                                                <th>
                                                    {!! $receipt_details['invoice_layout']['total_due_label'] !!}
                                                </th>
                                                <td class="text-right">
                                                    {{$receipt_details['total_due']}}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <strong style="font-size: 12px;color:black;">Auth. Signatory</strong>

                        </div>


            </div>


        </div>
        </body>

        </html>


    </div>
    <div class="spacer"></div>
</div>
</div>
<div class="spacer"></div>
</div>
@endforeach

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $("#printButton").on("click", function(){
        window.print();
    });
});
</script>
@stop
 

