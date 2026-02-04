@extends('layouts.app')

@section('title', $group->group_name)

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">
						<i class="fa fa-users"></i> {{ $group->group_name }}
						@if($group->created_by == auth()->user()->id)
						<div class="box-tools pull-right">
							<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'edit'], [$group->id]) }}" class="btn btn-warning btn-sm">
								<i class="fa fa-edit"></i> @lang('messages.edit')
							</a>
						</div>
						@endif
					</h3>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-6">
							<h4>@lang('essentials::lang.group_details')</h4>
							<table class="table table-bordered">
								<tr>
									<th width="30%">@lang('essentials::lang.group_name')</th>
									<td>{{ $group->group_name }}</td>
								</tr>
								<tr>
									<th>@lang('essentials::lang.group_description')</th>
									<td>{{ $group->group_description ?: '-' }}</td>
								</tr>
								<tr>
									<th>@lang('essentials::lang.created_by')</th>
									<td>{{ $group->creator->user_full_name }}</td>
								</tr>
								<tr>
									<th>@lang('essentials::lang.created_at')</th>
									<td>{{ $group->created_at->format('M d, Y H:i') }}</td>
								</tr>
								<tr>
									<th>@lang('essentials::lang.group_members')</th>
									<td>
										<span class="badge bg-blue">{{ $group->members->count() }}</span>
									</td>
								</tr>
							</table>
						</div>
						<div class="col-md-6">
							<h4>@lang('essentials::lang.group_members')</h4>
							@if($group->members->count() > 0)
								<div class="list-group">
									@foreach($group->members as $member)
									<div class="list-group-item">
										<div class="row">
											<div class="col-md-8">
												<h5 class="list-group-item-heading">{{ $member->user_full_name }}</h5>
												<p class="list-group-item-text text-muted">{{ $member->username }}</p>
											</div>
											<div class="col-md-4 text-right">
												@if($member->id == $group->created_by)
													<span class="label label-success">@lang('essentials::lang.creator')</span>
												@else
													<span class="label label-info">@lang('essentials::lang.member')</span>
												@endif
											</div>
										</div>
									</div>
									@endforeach
								</div>
							@else
								<p class="text-muted">@lang('essentials::lang.no_members_found')</p>
							@endif
						</div>
					</div>

					@if($group->messages->count() > 0)
					<div class="row">
						<div class="col-md-12">
							<h4>@lang('essentials::lang.recent_messages')</h4>
							<div class="box box-primary">
								<div class="box-body" style="height: 300px; overflow-y: scroll;">
									@foreach($group->messages->take(10) as $message)
									<div class="post" style="margin-bottom: 15px;">
										<div class="user-block">
											<span class="username">
												<span class="text-primary">{{ $message->sender->user_full_name }}</span>
											</span>
											<span class="description">
												<small><i class="fas fa-clock"></i> {{ $message->created_at->diffForHumans() }}</small>
											</span>
										</div>
										<p>{!! strip_tags($message->message, '<br>') !!}</p>
									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					@endif

					<div class="row">
						<div class="col-md-12 text-center">
							<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index'], ['type' => 'group']) }}" class="btn btn-primary btn-lg">
								<i class="fa fa-comments"></i> @lang('essentials::lang.chat_in_group')
							</a>
							<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'index']) }}" class="btn btn-default btn-lg">
								<i class="fa fa-arrow-left"></i> @lang('essentials::lang.back_to_groups')
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
