@extends('layouts.app')

@section('title', __('essentials::lang.create_group'))

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">@lang('essentials::lang.create_group')</h3>
				</div>
				<div class="box-body">
					{!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'store']), 'method' => 'post', 'id' => 'create_group_form']) !!}
					
					<div class="form-group">
						{!! Form::label('group_name', __('essentials::lang.group_name') . ':*') !!}
						{!! Form::text('group_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('essentials::lang.group_name')]); !!}
					</div>

					<div class="form-group">
						{!! Form::label('group_description', __('essentials::lang.group_description') . ':') !!}
						{!! Form::textarea('group_description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('essentials::lang.group_description')]); !!}
					</div>

					<div class="form-group">
						{!! Form::label('members', __('essentials::lang.group_members') . ':') !!}
						<select name="members[]" id="members" class="form-control select2" multiple="multiple" style="width: 100%;">
							@foreach($users as $user)
								<option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->username }})</option>
							@endforeach
						</select>
						<small class="help-block">@lang('essentials::lang.select_group_members_help')</small>
					</div>

					<div class="form-group text-center">
						<button type="submit" class="btn btn-primary btn-lg">
							<i class="fa fa-save"></i> @lang('essentials::lang.create_group')
						</button>
						<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'index']) }}" class="btn btn-default btn-lg">
							<i class="fa fa-times"></i> @lang('messages.cancel')
						</a>
					</div>

					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready(function(){
		$('#members').select2({
			placeholder: '@lang("essentials::lang.select_group_members")',
			allowClear: true
		});

		$('#create_group_form').submit(function(e) {
			e.preventDefault();
			
			var form_data = $(this).serialize();
			var submit_btn = $(this).find('button[type="submit"]');
			var original_text = submit_btn.html();
			
			submit_btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> @lang("essentials::lang.creating")...');
			
			$.ajax({
				url: $(this).attr('action'),
				data: form_data,
				method: 'POST',
				dataType: 'json',
				success: function(result) {
					if(result.success) {
						toastr.success(result.msg);
						window.location.href = "{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'index']) }}";
					} else {
						toastr.error(result.msg);
					}
				},
				error: function(xhr) {
					toastr.error('@lang("messages.something_went_wrong")');
				},
				complete: function() {
					submit_btn.prop('disabled', false).html(original_text);
				}
			});
		});
	});
</script>
@endsection
