@extends('layouts.app')

@section('title', __('essentials::lang.message_groups'))

@section('content')
@include('essentials::layouts.nav_essentials')
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header">
					<h3 class="box-title">@lang('essentials::lang.message_groups')</h3>
					@can('essentials.create_message')
					<div class="box-tools pull-right">
						<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'create']) }}" class="btn btn-primary btn-sm">
							<i class="fa fa-plus"></i> @lang('essentials::lang.create_group')
						</a>
					</div>
					@endcan
				</div>
				<div class="box-body">
					@if(count($groups) > 0)
						<div class="table-responsive">
							<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>@lang('essentials::lang.group_name')</th>
										<th>@lang('essentials::lang.group_description')</th>
										<th>@lang('essentials::lang.group_members')</th>
										<th>@lang('essentials::lang.created_by')</th>
										<th>@lang('essentials::lang.created_at')</th>
										<th>@lang('essentials::lang.action')</th>
									</tr>
								</thead>
								<tbody>
									@foreach($groups as $group)
									<tr>
										<td>
											<strong>{{ $group->group_name }}</strong>
										</td>
										<td>{{ $group->group_description ?: '-' }}</td>
										<td>
											<span class="badge bg-blue">{{ $group->members->count() }}</span>
											@if($group->members->count() > 0)
												<small class="text-muted">
													{{ $group->members->pluck('first_name')->implode(', ') }}
												</small>
											@endif
										</td>
										<td>{{ $group->creator->user_full_name }}</td>
										<td>{{ $group->created_at->format('M d, Y H:i') }}</td>
										<td>
											<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'show'], [$group->id]) }}" 
											   class="btn btn-info btn-xs" title="@lang('essentials::lang.view')">
												<i class="fa fa-eye"></i>
											</a>
											@if($group->created_by == auth()->user()->id)
											<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'edit'], [$group->id]) }}" 
											   class="btn btn-warning btn-xs" title="@lang('messages.edit')">
												<i class="fa fa-edit"></i>
											</a>
											<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'destroy'], [$group->id]) }}" 
											   class="btn btn-danger btn-xs delete-group" title="@lang('messages.delete')">
												<i class="fa fa-trash"></i>
											</a>
											@endif
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center">
							<h4>@lang('essentials::lang.no_groups_found')</h4>
							<p>@lang('essentials::lang.create_your_first_group')</p>
							@can('essentials.create_message')
							<a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageGroupController::class, 'create']) }}" class="btn btn-primary">
								<i class="fa fa-plus"></i> @lang('essentials::lang.create_group')
							</a>
							@endcan
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click', '.delete-group', function(e) {
			e.preventDefault();
			swal({
	          title: LANG.sure,
	          icon: "warning",
	          buttons: true,
	          dangerMode: true,
	        }).then((willDelete) => {
	            if (willDelete) {
	            	var delete_url = $(this).attr('href');
					$.ajax({
						url: delete_url,
						method: 'DELETE',
						dataType: "json",
						success: function(result){
							if(result.success == true){
								toastr.success(result.msg);
								location.reload();
							} else {
								toastr.error(result.msg);
							}
						}
					});
	            }
	        });
		});
	});
</script>
@endsection
