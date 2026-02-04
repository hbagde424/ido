<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController::class, 'update'], [$leave_type->id]), 'method' => 'put', 'id' => 'edit_leave_type_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_leave_type' )</h4>
    </div>

    <div class="modal-body">
      	<div class="form-group">
        	{!! Form::label('leave_type', __( 'essentials::lang.leave_type' ) . ':*') !!}
          	{!! Form::text('leave_type', $leave_type->leave_type, ['class' => 'form-control', 'required', 'placeholder' => __( 'essentials::lang.leave_type' ) ]); !!}
      	</div>

      	<div class="form-group">
        	{!! Form::label('max_leave_count', __( 'essentials::lang.max_leave_count' ) . ':') !!}
          	{!! Form::number('max_leave_count', $leave_type->max_leave_count, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.max_leave_count' ) ]); !!}
      	</div>

      	<div class="form-group">
        	{!! Form::label('max_leaves_per_month', 'Max Leaves Per Month:') !!}
        	<div class="input-group">
          		{!! Form::number('max_leaves_per_month', $leave_type->max_leaves_per_month ?? '', ['class' => 'form-control', 'placeholder' => 'e.g., 1 for Paid Leave', 'min' => '0']); !!}
          		<span class="input-group-addon">
          			<i class="fa fa-info-circle" data-toggle="tooltip" title="Leave this empty for no monthly limit. For Paid Leave, set to 1 to allow only 1 paid leave per month."></i>
          		</span>
        	</div>
        	<small class="help-block">Optional: Set monthly limit to control leave distribution. Example: 1 paid leave per month even if annual quota is 12.</small>
      	</div>

        <div class="form-group">
            <strong>@lang('essentials::lang.leave_count_interval')</strong><br>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', 'month', $leave_type->leave_count_interval == 'month'); !!} @lang('essentials::lang.current_month')
            </label>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', 'year', $leave_type->leave_count_interval == 'year'); !!} @lang('essentials::lang.current_fy')
            </label>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', null, empty($leave_type->leave_count_interval)); !!} @lang('lang_v1.none')
            </label>
        </div>
    </div>

    <div class="modal-footer">
      	<button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->