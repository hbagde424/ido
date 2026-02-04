@extends('layouts.app')

@section('title', __('account.account_book'))



@section('content')



<!-- Content Header (Page header) -->

<section class="content-header">

    <h1>@lang('account.account_book')

    </h1>

</section>



<!-- Main content -->

<section class="content">

    <div class="row" >

        

        <div class="col-sm-6 col-xs-12 " >

            <div class="box box-solid">

                <div class="box-header no-print">

                    <h3 class="box-title"> <i class="fa fa-filter" aria-hidden="true"></i> @lang('report.filters'):</h3>

                </div>

                <div class="box-body">

                    <div class="col-sm-6">

                        <div class="form-group">

                            {!! Form::label('transaction_date_range', __('report.date_range') . ':') !!}

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                                {!! Form::text('transaction_date_range', null, ['class' => 'form-control', 'readonly', 'placeholder' => __('report.date_range')]) !!}

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                            {!! Form::label('transaction_type', __('account.transaction_type') . ':') !!}

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>

                                {!! Form::select('transaction_type', ['' => __('messages.all'),'credit' => __('account.debit'), 'debit' => __('account.credit')], '', ['class' => 'form-control']) !!}

                            </div>

                        </div>

                    </div>

                    <div class="col-sm-6">

                        <div class="form-group">

                           Payment Method

                            <div class="input-group">

                                <span class="input-group-addon"><i class="fas fa-exchange-alt"></i></span>



<select class="form-control" id="payment_method" name="payment_method">

    <option value="">All</option>

            <?php  foreach($payment_types as $key=>$each_payment){ ?>

             

            <option value="<?php echo $key   ?>"><?php echo $each_payment;  ?></option>

            

            <?php  } ?> 

</select>                     

</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

       

<!--<input type='button' id='btn' value='Print' onclick='printDiv();'>-->



        <div class="col-sm-6 col-xs-12" id='DivIdToPrint'>

            <div class="row no-print">

        <div class="col-sm-12">

            <button type="button" class="btn btn-primary pull-right" 

            aria-label="Print" onclick="window.print();"

            ><i class="fa fa-print"></i> @lang( 'messages.print' )</button>

        </div>

    </div>

                <div class="print_section">   </div>

            <div class="box box-solid">

                <div class="box-body">

                    <table class="table" style="border-collapse: collapse;border: 2px solid;">

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th>@lang('account.account_name'): </th>

                            <td>{{$account->name}}</td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;display:none;">

                            <th>@lang('lang_v1.account_type'):</th>

                            <td>@if(!empty($account->account_type->parent_account)) {{$account->account_type->parent_account->name}} - @endif {{$account->account_type->name ?? ''}}</td>

                        </tr>

                        <!--<tr style="border-collapse: collapse;border: 2px solid;">-->

                        <!--    <th>@lang('account.account_number'):</th>-->

                        <!--    <td>{{$account->account_number}}</td>-->

                        <!--</tr>-->

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th>Total Account Balance:</th>

                            <td><span id="account_balance"></span></td>

                        </tr>

                        

                           

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

         Bank Transfer 

          </td>

          <td>

            <span  >

                <?php foreach($paymtnetotal as $key=>$each_payment){

                   

                if( $each_payment['method']=='bank_transfer')

                {

                echo $each_payment['net_amount'];

                } 

                

                }

                ?> 

            </span>

          </td>

          

        </tr>

           <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

        Cash

          </td>

          <td>

            <span  >

                <?php foreach($paymtnetotal as $key=>$each_payment){

                   

                if( $each_payment['method']=='cash' )

                {

                echo $each_payment['net_amount'];

                } 

                

                

                }

                ?> 

            </span>

          </td>

          

        </tr>

      

      

      

                        

                          @if(array_key_exists('custom_pay_1', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_1']}}:

          </td>

          <td>

            <span  >

                <?php foreach($paymtnetotal as $key=>$each_payment){

                   

                if( $each_payment['method']=='custom_pay_1')

                {

                echo $each_payment['net_amount'];

                } 

                

                }

                ?> 

              

             

             

             

            </span>

          </td>

          

        </tr>

      @endif

      @if(array_key_exists('custom_pay_2', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_2']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as $key => $each_payment){ 

                if( $each_payment['method']=='custom_pay_2')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

           

        </tr>

      @endif

      @if(array_key_exists('custom_pay_3', $payment_types))

        <tr style="display:none">

          <td>

            {{$payment_types['custom_pay_3']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as  $key =>$each_payment){ 

               if( $each_payment['method']=='custom_pay_3')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

          

        </tr>

      @endif

      @if(array_key_exists('custom_pay_4', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_4']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as $key=> $each_payment){ 

                if( $each_payment['method']=='custom_pay_4')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

           

        </tr>

      @endif

      @if(array_key_exists('custom_pay_5', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_5']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as $key=>$each_payment){ 

                 if( $each_payment['method']=='custom_pay_5')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

           

        </tr>

      @endif

      @if(array_key_exists('custom_pay_6', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_6']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as $key=>$each_payment){ 

 if( $each_payment['method']=='custom_pay_6')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

          

        </tr>

      @endif

      @if(array_key_exists('custom_pay_7', $payment_types))

        <tr style="border-collapse: collapse;border: 2px solid;display:none">

          <td>

            {{$payment_types['custom_pay_7']}}:

          </td>

          <td>

            <span class="display_currency" data-currency_symbol="true">

                <?php foreach($paymtnetotal as $key=> $each_payment){ 

                if( $each_payment['method']=='custom_pay_7')

                {

                echo $each_payment['net_amount'];

                } 

                }

                ?> 

                

            </span>

          </td>

          

        </tr>

      @endif

      

      

      

                           <tr style="border-collapse: collapse;border: 2px solid;">

                            <th>Select Date Opening Balance:</th>

                            <td><span id="account_balance_opening"></span></td>

                        </tr>

                        

                           <tr style="color:blue !important;">

                            <th><b>Select Date Closing Balance:</b></th>

                            <td><b><span id="account_balance_closing"></span></b></td>

                        </tr>

                        

                       <tr style="border-collapse: collapse;border: 2px solid;">

                            <th>Select Date Total:</th>

                            <td><span id="grand_total"></span></td>

                        </tr>

                        

                          

                          

                          <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Cash Receive  :</th>

                            <td><span class="Cash_total"></span></td>

                        </tr>  

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Orange Total :</th>

                            <td><span class="orange_total"></span></td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Wave Total  :</th>

                            <td><span class="web_total"></span></td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Karan Wave Total :</th>

                            <td><span class="karanweb_total"></span></td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th > Select Date Karan Orange Total :</th>

                            <td><span class="karanorange_total"></span></td>

                        </tr>

                             

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Karan Cash Total :</th>

                            <td><span class="karancash_total"></span></td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Moussa Cash Total :</th>

                            <td><span class="moussacash_total"></span></td>

                        </tr>

                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date Ravi Cash Total :</th>

                            <td><span class="ravicash_total"></span></td>

                        </tr>
                        
                        <tr style="border-collapse: collapse;border: 2px solid;">

                            <th> Select Date SumarrowCredit Total :</th>

                            <td><span class="sumarrowcredit_total"></span></td>

                        </tr>

                             

                             

                    </table>

                </div>

            </div>

     

        </div>

    </div>

    <div class="row no-print" >

        <div class="col-sm-12">

        	<div class="box">

                <div class="box-body">

                    @can('account.access')

                        <div class="table-responsive">

                    	<table class="table table-bordered table-striped" id="account_book">

                    	  

                    		<thead>

                    		   

                    		   

                    			<tr>

                                    <th>@lang( 'messages.date' )</th>

                                    <th style="width:200px">@lang( 'lang_v1.description' )</th>

                                    <th>@lang( 'lang_v1.payment_method' )</th>

                                    <!--<th>@lang( 'lang_v1.payment_details' )</th>-->

                                    <th>@lang( 'brand.note' )</th>

                                    <th style="width:200px">@lang( 'lang_v1.added_by' )</th>

                                    <th >@lang('account.debit')</th>

                                    <th>@lang('account.credit')</th>

                    				<!--<th>@lang( 'lang_v1.balance' )</th>-->

                                    <th>@lang( 'messages.action' )</th>

                    			</tr> 

                    			

                    		</thead>

                            <tfoot>

                                <tr class="bg-gray font-17 footer-total text-center">

                                   

                                       <td>Opening Balance   <span class="footer_total_debit"></span></td>

                                    <td>Closing Balance  <span class="footer_total_credit"></span></td>

                                    <!--<td></td>-->

                                    

                                    

                                    <!--<td></td>-->

                                     <!--<td>  </td>-->

                                    <td></td>

                                     <td></td> 

                                    <td> </td>

                                     <td></td>

                                    <td></td>
                                    <td></td>

                                    

                                    <!--Cash - <span class="Cash_total"></span>Wave <span class="web_total"></span>Orange <span class="orange_total"></span>-->

                                    

                                </tr>

                                

                            </tfoot>

                            

                    	</table>

                    	

                        </div>

                    @endcan

                </div>

            </div>

        </div>

    </div>

    



    <div class="modal fade account_model" tabindex="-1" role="dialog" 

    	aria-labelledby="gridSystemModalLabel">

    </div>

    <div class="modal fade account_model" tabindex="-1" role="dialog" 

        aria-labelledby="gridSystemModalLabel" id="edit_account_transaction">

    </div>



</section>

<!-- /.content -->



@endsection



@section('javascript')



 <script>



function printDiv() 

{



  var divToPrint=document.getElementById('DivIdToPrint');



  var newWin=window.open('','Print-Window');



  newWin.document.open();



  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');



  newWin.document.close();



  setTimeout(function(){newWin.close();},10);



}

// jQuery code to update HTML title onload

        $(document).ready(function() {

            var newTitle = "Daily Payment Report"; // Set your new title here

            $('title').text(newTitle);

        });

    </script>

    

    

<script>



 



    $(document).ready(function(){

        update_account_balance();

  

        dateRangeSettings.startDate = moment().subtract(6, 'days');

        dateRangeSettings.endDate = moment();

        $('#transaction_date_range').daterangepicker(

            dateRangeSettings,

            function (start, end) {

                $('#transaction_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));

                

                account_book.ajax.reload();

            }

        );

        

        // Account Book

        account_book = $('#account_book').DataTable({

                            processing: true,

                            serverSide: true,

                             "pageLength": -1,

                            ajax: {

                                url: '{{action([\App\Http\Controllers\AccountController::class, 'show'],[$account->id])}}',

                                data: function(d) {

                                    var start = '';

                                    var end = '';

                                    if($('#transaction_date_range').val()){

                                        start = $('input#transaction_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');

                                        end = $('input#transaction_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');

                                    }

                                    var transaction_type = $('select#transaction_type').val();

                                    var payment_method = $('select#payment_method').val();

                                    d.start_date = start;

                                    d.end_date = end;

                                    d.type = transaction_type;

                                    d.pmethod = payment_method;

                                }

                            },

                            "ordering": false,

                            columns: [

                                {data: 'operation_date', name: 'operation_date'},

                                {data: 'sub_type', name: 'sub_type'},

                                {data: 'method', name: 'tp.method'},

                                // {data: 'payment_details', name: 'tp.payment_ref_no'},

                                {data: 'note', name: 'note'},

                                {data: 'added_by', name: 'added_by'},

                                {data: 'credit', name: 'amount', searchable: false},

                                {data: 'debit', name: 'amount', searchable: false},

                                // {data: 'balance', name: 'balance', searchable: false},

                                {data: 'action', name: 'action', searchable: false}

                            ],
                            "fnDrawCallback": function (oSettings) {

                                __currency_convert_recursively($('#account_book'));

                            },

                            "footerCallback": function ( row, data, start, end, display ) {

                                var footer_total_debit = 0;

                                var footer_total_credit = 0;



                                var Wave_total_debit = 0;

                                var Wave_total_credit = 0;

                                

                                var KaranWave_total_debit = 0;

                                var KaranWave_total_credit = 0;

                                

                                var KaranOrange_total_debit = 0;

                                var KaranOrange_total_credit = 0;

                                

                                var KaranCash_total_debit = 0;

                                var KaranCash_total_credit = 0;

                                

                                var MoussaCash_total_debit = 0;

                                var MoussaCash_total_credit = 0;

                                

                                var RaviCash_total_debit = 0;

                                var RaviCash_total_credit = 0;
                                
                                
                                var SumarrowCredit_total_debit = 0;

                                var SumarrowCredit_total_credit = 0;
                                



                                var Cash_total_credit = 0;

                                var Cash_total_debit = 0; 



                                var Orange_total_credit = 0;

                                var Orange_total_debit = 0; 





                                for (var r in data){

                                    

                                    if(data[r].method == 'Wave') 

                                    {

                                    Wave_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    Wave_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                    if(data[r].method == 'KaranWave') 

                                    {

                                    KaranWave_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    KaranWave_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                     if(data[r].method == 'KaranCash') 

                                    {

                                    KaranCash_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    KaranCash_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                     if(data[r].method == 'KaranOrange') 

                                    {

                                    KaranOrange_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    KaranOrange_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                     if(data[r].method == 'MoussaCash') 

                                    {

                                    MoussaCash_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    MoussaCash_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                     if(data[r].method == 'RaviCash') 

                                    {

                                    RaviCash_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    RaviCash_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }
                                    
                                    
                                     if(data[r].method == 'SumarrowCredit') 

                                    {

                                    SumarrowCredit_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    SumarrowCredit_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    }

                                    

                                     if(data[r].method == 'Cash' || data[r].method == 'cash') 

                                    {

                                    Cash_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    Cash_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                  

                                    } 

                                    

                                    

                                    if(data[r].method == 'Orange') 

                                    {

                                   Orange_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    Orange_total_debit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;



                                    } 

                                    

                                    

                                    

                                    footer_total_debit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;

                                    footer_total_credit += $(data[r].debit).data('orig-value') ? parseFloat($(data[r].debit).data('orig-value')) : 0;

                                    footer_total_credit1 = $(data[r].balance).data('orig-value') ? parseFloat($(data[r].balance).data('orig-value')) : 0;

                                 }

                                 

                             $('.web_total').html(__currency_trans_from_en(Wave_total_credit-Wave_total_debit));

                             

                             $('.karanweb_total').html(__currency_trans_from_en(KaranWave_total_credit-KaranWave_total_debit));

                             

                             $('.karanorange_total').html(__currency_trans_from_en(KaranOrange_total_credit-KaranOrange_total_debit));

                             $('.karancash_total').html(__currency_trans_from_en(KaranCash_total_credit-KaranCash_total_debit));

                             

                             $('.moussacash_total').html(__currency_trans_from_en(MoussaCash_total_credit-MoussaCash_total_debit));

                             

                             $('.ravicash_total').html(__currency_trans_from_en(RaviCash_total_credit-RaviCash_total_debit));

                             $('.sumarrowcredit_total').html(__currency_trans_from_en(SumarrowCredit_total_credit-SumarrowCredit_total_debit));
                              

                                 $('.Cash_total').html(__currency_trans_from_en(Cash_total_credit-Cash_total_debit));

                                

                             $('.orange_total').html(__currency_trans_from_en(Orange_total_credit-Orange_total_debit));

                             

                             



                            if(data[0]){

                                if(data[0].method == ""){

                           

                            footer_total_debitqq  = $(data[1].credit).data('orig-value') ? parseFloat($(data[1].credit).data('orig-value')) : 0;

                           

                           

                           

                            footer_total_creditqq = $(data[1].debit).data('orig-value') ? parseFloat($(data[1].debit).data('orig-value')) : 0;

                            footer_blance_opening_blance = $(data[1].balance).data('orig-value') ? parseFloat($(data[1].balance).data('orig-value')) : 0;

                           

                           

                            //  $('.footer_total_debit').html(__currency_trans_from_en(footer_total_debit));

                            // $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));

                            $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit1));

                            $('.footer_total_debit').html(__currency_trans_from_en(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq));

                            $('#account_balance_closing').html(__currency_trans_from_en(footer_total_credit1));

                            $('#account_balance_opening').html(__currency_trans_from_en(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq));

                            $('#grand_total').html(__currency_trans_from_en(footer_total_credit1-(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq)));

                                }else{

                            footer_total_debitqq  = $(data[0].credit).data('orig-value') ? parseFloat($(data[0].credit).data('orig-value')) : 0;

                            footer_total_creditqq = $(data[0].debit).data('orig-value') ? parseFloat($(data[0].debit).data('orig-value')) : 0;

                            footer_blance_opening_blance = $(data[0].balance).data('orig-value') ? parseFloat($(data[0].balance).data('orig-value')) : 0;

                            //  $('.footer_total_debit').html(__currency_trans_from_en(footer_total_debit));

                            // $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));

                            $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit1));

                            $('.footer_total_debit').html(__currency_trans_from_en(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq));

                            $('#account_balance_closing').html(__currency_trans_from_en(footer_total_credit1));

                          

                  if(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq == 0 )

                  {    $('#account_balance_opening').html(__currency_trans_from_en(footer_blance_opening_blance+footer_total_creditqq));



                  }else{

                    $('#account_balance_opening').html(__currency_trans_from_en(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq));



                  }

                    $('#grand_total').html(__currency_trans_from_en(footer_total_credit1-(footer_blance_opening_blance+footer_total_creditqq-footer_total_debitqq)));

                                 

                                

                                }

                                

                            }

                        

                        

                            }

                        });







        $('#transaction_type').change( function(){

            account_book.ajax.reload();

        });

        

        

        $('#payment_method').change( function(){

            account_book.ajax.reload();

        });

        

        $('#transaction_date_range').on('cancel.daterangepicker', function(ev, picker) {

            $('#transaction_date_range').val('');

            account_book.ajax.reload();

        });



        $('#edit_account_transaction').on('shown.bs.modal', function(e) {

            $('#edit_account_transaction_form').validate({

                submitHandler: function(form) {

                    e.preventDefault();

                    var data = $(form).serialize();

                    $.ajax({

                        method: 'POST',

                        url: $(form).attr('action'),

                        dataType: 'json',

                        data: data,

                        beforeSend: function(xhr) {

                            __disable_submit_button($(form).find('button[type="submit"]'));

                        },

                        success: function(result) {

                            if (result.success == true) {

                                $('#edit_account_transaction').modal('hide');

                                toastr.success(result.msg);



                                if (typeof(account_book) != 'undefined') {

                                    account_book.ajax.reload();

                                }

                                

                            } else {

                                toastr.error(result.msg);

                            }

                        },

                    });

                },

            });

        })



    });



    $(document).on('click', '.delete_account_transaction', function(e){

        e.preventDefault();

        swal({

          title: LANG.sure,

          icon: "warning",

          buttons: true,

          dangerMode: true,

        }).then((willDelete) => {

            if (willDelete) {

                var href = $(this).data('href');

                $.ajax({

                    url: href,

                    dataType: "json",

                    success: function(result){

                        if(result.success === true){

                            toastr.success(result.msg);

                            account_book.ajax.reload();

                            update_account_balance();

                        } else {

                            toastr.error(result.msg);

                        }

                    }

                });

            }

        });

    });



    function update_account_balance(argument) {

        $('span#account_balance').html('<i class="fas fa-sync fa-spin"></i>');

        $.ajax({

            url: '{{action([\App\Http\Controllers\AccountController::class, 'getAccountBalance'], [$account->id])}}',

            dataType: "json",

            success: function(data){

                $('span#account_balance').text(__currency_trans_from_en(data.balance, true));

            }

        });

    }

    

    

    

</script>

@endsection