@extends('layouts.app')

@section('title', __('essentials::lang.messages'))

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<!-- Message Type Tabs -->
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
			<li class="{{ $message_type == 'location' ? 'active' : '' }}">
				<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'location']) }}">
					<i class="fa fa-map-marker"></i> @lang('essentials::lang.location_message')
				</a>
			</li>
			<li class="{{ $message_type == 'user' ? 'active' : '' }}">
				<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'user']) }}">
					<i class="fa fa-user"></i> @lang('essentials::lang.direct_message')
				</a>
			</li>
			<li class="{{ $message_type == 'group' ? 'active' : '' }}">
				<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'group']) }}">
					<i class="fa fa-users"></i> @lang('essentials::lang.group_message')
				</a>
			</li>
			@can('essentials.create_message')
			<li class="pull-right">
				<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'index']) }}">
					<i class="fa fa-cog"></i> @lang('essentials::lang.manage_groups')
				</a>
			</li>
			@endcan
		</ul>
	</div>

	<!-- Chat box -->
	<div class="box box-solid">
	<div class="box-header">
	  <i class="fa fa-comments-o"></i>
	  <h3 class="box-title">@lang('essentials::lang.messages') - 
	  	@if($message_type == 'location')
	  		@lang('essentials::lang.location_message')
	  	@elseif($message_type == 'user')
	  		@lang('essentials::lang.direct_message')
	  	@elseif($message_type == 'group')
	  		@lang('essentials::lang.group_message')
	  	@endif
	  </h3>
	</div>
	<div class="box-body" id="chat-box" style="height: 70vh; overflow-y: scroll;">
		@can('essentials.view_message')
		  @foreach($messages as $message)
		  	@include('essentials::messages.message_div')
		  @endforeach
	  	@endcan
	</div>
	<!-- /.chat -->
	@can('essentials.create_message')
	<div class="box-footer">
		{!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'store']), 'method' => 'post', 'id' => 'add_essentials_msg_form']) !!}
			{!! Form::hidden('message_type', $message_type) !!}
			<div class="input-group">
		  		{!! Form::textarea('message', null, ['class' => 'form-control', 'required', 'id' => 'chat-msg', 'placeholder' => __('essentials::lang.type_message'), 'rows' => 1]); !!}
		  		
		  		@if($message_type == 'location')
		  		<div class="input-group-addon" style="width: 137px;padding: 0;border: none;">
		  			{!! Form::select('location_id',$business_locations,  null, ['class' => 'form-control', 'placeholder' => __('lang_v1.select_location'), 'style' => 'width: 100%;' ]); !!}
		  		</div>
		  		@elseif($message_type == 'user')
		  		<div class="input-group-addon" style="width: 137px;padding: 0;border: none;">
		  			{!! Form::select('recipient_user_id',$users->pluck('first_name', 'id'),  null, ['class' => 'form-control', 'placeholder' => __('essentials::lang.select_recipient'), 'style' => 'width: 100%;' ]); !!}
		  		</div>
		  		@elseif($message_type == 'group')
		  		<div class="input-group-addon" style="width: 137px;padding: 0;border: none;">
		  			{!! Form::select('group_id',$groups->pluck('group_name', 'id'),  null, ['class' => 'form-control', 'placeholder' => __('essentials::lang.select_group'), 'style' => 'width: 100%;' ]); !!}
		  		</div>
		  		@endif
		  		
		  		<div class="input-group-btn">
		  			<button type="submit" class="btn btn-success pull-right ladda-button" data-style="expand-right">
		  				<span class="ladda-label"><i class="fa fa-plus"></i></span>
		  			</button>
		  		</div>
			</div>
		  {!! Form::close() !!}
	</div>
	@endcan
	</div>
	<!-- /.box (chat box) -->
</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready(function(){
		scroll_down_chat_div();
		$('#chat-msg').focus();
		$('form#add_essentials_msg_form').submit(function(e) {
			e.preventDefault();
			var msg = $('#chat-msg').val().trim();
			if(msg) {
				var data = $(this).serialize();
				var ladda = Ladda.create(document.querySelector('.ladda-button'));
				ladda.start();
				$.ajax({
					url: "{{action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'store'])}}",
					data: data,
					method: 'post',
					dataType: "json",
					success: function(result){
						ladda.stop();
						if(result.html) {
							$('div#chat-box').append(result.html);
							scroll_down_chat_div();
							$('#chat-msg').val('').focus();
						}
					}
				});
			}
		});

		$(document).on('click', 'a.chat-delete', function(e) {
			e.preventDefault();
			swal({
	          title: LANG.sure,
	          icon: "warning",
	          buttons: true,
	          dangerMode: true,
	        }).then((willDelete) => {
	            if (willDelete) {
	            	var chat_item = $(this).closest('.post');
					$.ajax({
						url: $(this).attr('href'),
						method: 'DELETE',
						dataType: "json",
						success: function(result){
							if(result.success == true){
								toastr.success(result.msg);
								chat_item.remove();
							} else {
								toastr.error(result.msg);
							}
						}
					});
	            }
	        });
		});
		var chat_refresh_interval = "{{config('essentials::config.chat_refresh_interval', 20)}}";
		chat_refresh_interval = parseInt(chat_refresh_interval) * 1000;
		setInterval(function(){ getNewMessages() }, chat_refresh_interval);
	});

	function scroll_down_chat_div() {
		var chat_box    = $('#chat-box');
		var height = chat_box[0].scrollHeight;
		chat_box.scrollTop(height);
	}

	function getNewMessages() {
		var last_chat_time =  $('div.msg-box').length ? $('div.msg-box:last').data('delivered-at') : '';
		var message_type = "{{ $message_type }}";
		$.ajax({
            url: "{{action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'getNewMessages'])}}?last_chat_time=" + last_chat_time + "&type=" + message_type,
            dataType: 'html',
            global: false,
            success: function(result) {
            	if(result.trim() != ''){
            		$('div#chat-box').append(result);
					scroll_down_chat_div();
            	}
            },
        });
	}
</script>
@endsection