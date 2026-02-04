<!-- Post -->
<div class="post msg-box" style="margin-left: 15px; margin-right: 15px;" data-delivered-at="{{$message->created_at}}">
  	<div class="user-block">
        <span class="username" style="margin-left: 0;">
          <span class="text-primary">{{$message->sender->user_full_name}}</span>
          @if($message->message_type == 'user' && $message->recipient)
          	<span class="text-muted"> → {{$message->recipient->user_full_name}}</span>
          @elseif($message->message_type == 'group' && $message->group)
          	<span class="text-muted"> → {{$message->group->group_name}}</span>
          @elseif($message->message_type == 'location' && $message->location)
          	<span class="text-muted"> → {{$message->location->name}}</span>
          @endif
          @if($message->user_id == auth()->user()->id)
          	<a href="{{action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'destroy'], [$message->id])}}" class="pull-right btn-box-tool chat-delete" title="@lang('messages.delete')"><i class="fa fa-times text-danger"></i></a>
          @endif
        </span>
    	<span class="description" style="margin-left: 0;">
    		<small>
    			<i class="fas fa-clock"></i> {{$message->created_at->diffForHumans()}}
    			@if($message->message_type == 'user')
    				<i class="fa fa-user text-info" title="@lang('essentials::lang.direct_message')"></i>
    			@elseif($message->message_type == 'group')
    				<i class="fa fa-users text-success" title="@lang('essentials::lang.group_message')"></i>
    			@elseif($message->message_type == 'location')
    				<i class="fa fa-map-marker text-warning" title="@lang('essentials::lang.location_message')"></i>
    			@endif
    		</small>
    	</span>
  	</div>
  	<!-- /.user-block -->
  	<p>
    	{!! strip_tags($message->message, '<br>') !!}
  	</p>
</div>
<!-- /.post -->