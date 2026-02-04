@extends('layouts.app')
@section('title', __('report.stock_report'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('report.stock_report')}}</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
             <form id="myForm" method="post"  action="{{route('stockproductreport')}}" >
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, $location_id, ['class' => 'form-control select2', 'style' => 'width:100%']); !!}
                    </div>
                </div> <br>
               <div class="col-md-2">
                    <div class="form-group">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <button type="submit" class="btn btn-success">Submit</button>
                 </div>
                  </div>
              </form> 
                 <form id="myFormnew" method="post"  action="{{route('getStockReportproductpdf')}}" >
                
                <input type="hidden" name="location_id" value="<?php echo $location_id; ?>">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                 <button type="submit" class="btn btn-success" name="submit" >Download pdf</button>
              </form> 
            
            @endcomponent
            
          
          
            
            
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
        
<table class="table table-bordered table-striped" id="stock_report_table" style="font-size:18px;">
    <thead>
        <tr>
            
            <th style="width:50% !important;">@lang('business.product')</th>
            <th>Image</th>
            <th style="width:20% !important;">@lang('report.current_stock')</th>
            <th style="width:30% !important;">@lang('messages.action')</th>
        </tr>
    </thead>
 <tbody>
       @foreach($results as $each_result)
       @if($each_result->stock >0 )
           <tr <?php if($each_result->alert_quantity > $each_result->stock ){ ?> style="color:red" <?php } ?> >
            <td  style="width:50% !important;" ><?php echo $each_result->product ?></td>
            <td><img src="/shreeshiv/public/uploads/img/<?php echo $each_result->image ?>" style="width:100px !important;"></td>
            <td style="width:20% !important;"><?php echo number_format($each_result->stock,2);
            if($each_result->remaing_qty>0){?>
            <br>(<?php echo $each_result->remaing_qty ?>-<?php echo $each_result->postatus ?>)
            
            <?php } ?> </td>
            <td>
                <a class="btn btn-info btn-xs" href="{{url('products/stock-history/'.$each_result->id.'?location_id='.$each_result->location_id.'&variation_id='.$each_result->variation_id.'')}}"><i class="fas fa-history"></i> {{__('lang_v1.product_stock_history')}}</a> 
                    
                    </td>
        </tr>
        @endif
        @endforeach
        
 </tbody>
</table>
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
    <script src="{{ asset('js/reportnew.js?v=' . $asset_v) }}"></script>
    <script>
$(document).ready(function(){
    $('#selectOption').on('change', function(){
        $('#myForm').submit(); // Submit the form when select value changes
    });
});
</script>

@endsection
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--  <meta charset="UTF-8">-->
<!--  <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--  <title>Stock In Hand</title>-->
<!--  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">-->
<!--  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">-->
<!--</head>-->
<!--<body>-->

<!--<table id="example" class="display" style="width:100%">-->
<!--  <thead>-->
<!--    <tr>-->
<!--      <th>Name</th>-->
<!--      <th>Position</th>-->
<!--      <th>Office</th>-->
<!--      <th>Age</th>-->
<!--      <th>Start date</th>-->
<!--      <th>Image</th>-->
<!--      <th>Salary</th>-->
<!--    </tr>-->
<!--  </thead>-->
<!--  <tbody>-->
<!--    <tr>-->
<!--      <td>Tiger Nixon</td>-->
<!--      <td>System Architect</td>-->
<!--      <td>Edinburgh</td>-->
<!--      <td>61</td>-->
<!--      <td>2011/04/25</td>-->
<!--      <td><img src="https://via.placeholder.com/150" alt="Sample Image" width="50"></td>-->
<!--      <td>$320,800</td>-->
<!--    </tr>-->
    <!-- Add more rows as needed -->
<!--  </tbody>-->
<!--</table>-->

<!--<button id="export-pdf">Export to PDF</button>-->

<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>-->
<!--<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>-->
<!--<script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>-->
 

<!--</body>-->
<!--</html>-->
