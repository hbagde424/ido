<!-- business information here -->
    @php
    $custom_labels = json_decode(session('business.custom_labels'), true);
    @endphp
       

<!DOCTYPE html>

<html>

<head>

  <meta charset="utf-8">

  <meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
<style type="text/css">
@media print {
    /* Override grayscale */
    body {
        -webkit-print-color-adjust: exact; /* For WebKit browsers */
        color-adjust: exact; /* Standard syntax */
    }

    /* Additional styling for printed content */
    /* Add any specific styles you want for printing */
}
 ul {
  list-style-type: none !important;
 }
 tr
 {
     padding-bottom: 20px;
 }
</style>
  </head>
   <body style="background:#fffff:  font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;">
        <div class="container-div1" style="display: flex;
    background-color:orange !important;">
  <div class="item" style=" flex: 1;
    background-color:orange !important;
    color:black !important;
    padding: 10px;
    margin: 5px;">@php
							$sub_headings = implode('<br/>', array_filter([$receipt_details->sub_heading_line1, $receipt_details->sub_heading_line2, $receipt_details->sub_heading_line3, $receipt_details->sub_heading_line4, $receipt_details->sub_heading_line5]));
						@endphp

						@if(!empty($sub_headings))
							<h3 class="text-center"><b><span>{!! $sub_headings !!}</span></b></h3>
						@endif</div>
  <div class="item" style=" flex: 1;
    background-color:orange !important;
    color:black !important;
    padding: 10px;
    margin: 5px;"> <h3>
       	@if(!empty($receipt_details->invoice_heading))
				<h3 class="text-center">
				<b>	{!! $receipt_details->invoice_heading !!}</b>
				</h3>
			@endif</h3></div>
</div>

<div class="container-div" style="display: flex;">
  <div class="item2" style="background-color:orange !important; flex: 1;
    color:black !important;
    padding: 10px;
    margin: 5px;" ><span style="font-size:18px;"class="text-center"><b>@if(!empty($receipt_details->invoice_no_prefix))
				<b style="font-weight:bold;">	{!! $receipt_details->invoice_no_prefix !!}:</b>
				@endif
				{{$receipt_details->invoice_no}}</b></div>
  <div class="item2" style="background-color:orange !important; flex: 1;
    color:black !important;
    padding: 10px;
    margin: 5px;"> <b style="font-weight:bold;font-size:18px;" class="text-center">Date: {{$receipt_details->invoice_date}} </b></td></div>
</div>
  
  
    <div><h2>
         @if(!empty($receipt_details->customer_info))
				
				<p style="font-size:22px;">	{{ $receipt_details->customer_label }}: {!! $receipt_details->customer_info !!}</p>
				@endif
			
			
    </h2></div>
  <table cellpadding="0" cellspacing="0" border="0" style="width: 100%;border-collapse: collapse;">

       <table style="width: 100% ;border: 1px solid black;border-collapse: collapse;font-family: &#39;Source Sans Pro&#39;,&#39;Helvetica Neue&#39;,Helvetica,Arial,sans-serif;"> 
         <thead style="border: 1px solid black;color:black !important;">
            <tr style="font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;">
               <th style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1px;border-top:0;width: 10px;">S.No. </th>
               <th style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 300px;">{{$receipt_details->table_product_label}}   </th>
               <!--<th style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:1;border-top:0;width: 65px;">HSN Code</th>-->
              
               <th style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 8px;">{{$receipt_details->table_qty_label}}</th>
               <th style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 27px;">{{$receipt_details->table_unit_price_label}}</th>
              
             
               <th style="text-align: center;border: 1px solid black;padding: 4px 5px;border-bottom:1;border-top:0;width: 41px;">{{$receipt_details->table_subtotal_label}}</th>
               
            </tr>
        </thead>
        <tbody>
            	 
		@foreach($receipt_details->lines as $key => $line)		 

<tr style="margin-bottom: 20px;border: 1px solid #696969;font-family: 'Source Sans Pro','Helvetica Neue',Helvetica,Arial,sans-serif;color:black;">

<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;" valign="top"> {{ $loop->iteration }}</td>
<td  style="border: 1px solid black;text-align: left;padding: 4px 5px 8px 5px;border-bottom:0;border-top:0;font-size: 15px;">
  {{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
                            @if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif @if(!empty($line['cat_code'])), {{$line['cat_code']}}@endif
                            @if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
                             @if(!empty($line['product_description']))
                            	<small>
                            		{!!$line['product_description']!!}
                            		
                            	</small>
                            @endif
                             @if(!empty($line['sell_line_note']))
                <br>	{!!$line['sell_line_note']!!}
                @endif
                </td>
<!--<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">-->
<!--    84249000-->
<!--    </td>-->


<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">
    {{$line['quantity']}} {{$line['units']}}</td>
<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">
    {{$line['unit_price_before_discount']}}</td>

<!--<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">-->
<!--    {{$line['tax']}}  -->
<!--                </td>-->
<!--<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">-->
<!--    {{$line['total_line_discount'] ?? '0.00'}}-->

								@if(!empty($line['line_discount_percent']))
								 	({{$line['line_discount_percent']}}%)
								@endif</td>
<td  style="border: 1px solid black;text-align: center;padding: 4px 5px;border-bottom:0;border-top:0;font-size: 15px;">
    {{$line['line_total']}}</td>

            
                </tr>
   
@endforeach
                                  
                        
            <tr style="color:black;" >
               <td style="border: 1px solid black;text-align: center;padding: 5px 5px;"><b style="font-weight:bold;"></b></td>
               <td colspan="2" style="border: 1px solid black;padding: 5px 5px;"> <span id="totalword"></span></td>
               <td colspan="" style="border: 1px solid black;text-align: center;padding: 5px 5px;font-size: 15px;"><b>Sub Total<b/></td>
               <td style="border: 1px solid black;text-align: center;padding: 5px 5px;font-size:15px;">	{{$receipt_details->subtotal}}</td>
            </tr>
  <tbody>          
</table>



<br> 
<div class="row" style="color: #000000 !important;">
 
<div class="col-xs-7"><b>Maguilaye diadieufeul thie kolouté bi</b><br><b>Merci pour votre entreprise</b><br><br><br><br><br>
<br><b>Term & Condition:</b> {{$receipt_details->additional_notes}}
</div>
<div class="col-xs-5">
        <div class="table-responsive">
          	<table class="table table-slim">
				<tbody> 
					<tr>
						<th style="width:50%">
							{!! $receipt_details->subtotal_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->subtotal}}
						</td>
					</tr>
					@if(!empty($receipt_details->total_exempt_uf))
					<tr>
						<th style="width:70%">
							@lang('lang_v1.exempt')
						</th>
						<td class="text-right">
							{{$receipt_details->total_exempt}}
						</td>
					</tr>
					@endif
					<!-- Shipping Charges -->
					@if(!empty($receipt_details->shipping_charges))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->shipping_charges_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->shipping_charges}}
							</td>
						</tr>
					@endif

					@if(!empty($receipt_details->packing_charge))
						<tr>
							<th style="width:70%">
								{!! $receipt_details->packing_charge_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->packing_charge}}
							</td>
						</tr>
					@endif

					<!-- Discount -->
					@if( !empty($receipt_details->discount) )
						<tr>
							<th>
								{!! $receipt_details->discount_label !!}
							</th>

							<td class="text-right">
								(-) {{$receipt_details->discount}}
							</td>
						</tr>
					@endif

					@if( !empty($receipt_details->total_line_discount) )
						<tr>
							<th>
								{!! $receipt_details->line_discount_label !!}
							</th>

							<td class="text-right">
								(-) {{$receipt_details->total_line_discount}}
							</td>
						</tr>
					@endif

					@if( !empty($receipt_details->additional_expenses) )
						@foreach($receipt_details->additional_expenses as $key => $val)
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

					@if( !empty($receipt_details->reward_point_label) )
						<tr>
							<th>
								{!! $receipt_details->reward_point_label !!}
							</th>

							<td class="text-right">
								(-) {{$receipt_details->reward_point_amount}}
							</td>
						</tr>
					@endif

					<!-- Tax -->
					@if( !empty($receipt_details->tax) )
						<tr>
							<th>
								{!! $receipt_details->tax_label !!}
							</th>
							<td class="text-right">
								(+) {{$receipt_details->tax}}
							</td>
						</tr>
					@endif

					@if( $receipt_details->round_off_amount > 0)
						<tr>
							<th>
								{!! $receipt_details->round_off_label !!}
							</th>
							<td class="text-right">
								{{$receipt_details->round_off}}
							</td>
						</tr>
					@endif

					<!-- Total -->
					<tr>
						<th>
							{!! $receipt_details->total_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->total}}
						</td>
					
					</tr>
					
			<tr>
			    	<th>
							{!! $receipt_details->total_paid_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->total_paid}}
						</td>
						
			</tr>
			<tr>
			    <th>
							{!! $receipt_details->total_due_label !!}
						</th>
						<td class="text-right">
							{{$receipt_details->total_due}}
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
            		<strong style="font-size:12px;color:black;">Auth. Signatory</strong>
            	
            </div>
     
 
      </div>
      
        
   </div>
   </body>
</html>
