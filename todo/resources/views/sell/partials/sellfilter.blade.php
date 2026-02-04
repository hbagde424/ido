<form method="get" action={{route('viewshow_invoice',1)}} target="_blank"> 
@if(empty($only) || in_array('sell_list_filter_location_id', $only))
<div class="col-md-3">
    <div class="form-group">
        {!! Form::label('sell_list_filter_location_idnew',  __('purchase.business_location') . ':') !!}

        {!! Form::select('sell_list_filter_location_idnew', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
    </div>
</div>
@endif
 
<div class="col-md-3">
    <div class="form-group">
        <label>From Date</label>
    <input type="date" name="from_date" class="form-control">
    </div>
</div>
<div class="col-md-3">
    <div class="form-group">
          <label>To Date</label>
    <input type="date" name="to_date" class="form-control">
    </div>
</div>
@if(empty($only) || in_array('only_subscriptions', $only))
<div class="col-md-3">
    <div class="form-group">
        <div class="checkbox">
            <label>
                <br>
             <input type="submit"  class="btn btn-success">
            </label>
        </div>
    </div>
</div>
@endif
</form>