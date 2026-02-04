@extends('layouts.app')

@section('title', __('essentials::lang.todo'))

@section('content')
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-primary">
				<div class="box-header">
					<h4 class="box-title">
						<i class="ion ion-clipboard"></i>
						<small><code>({{$todo->task_id}})</code></small> {{$todo->task}}
					</h4>
					<div class="box-tools pull-right">
						@can('essentials.edit_todos')
						<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#update_task_status_modal" onclick="setTaskIdForStatusUpdate({{$todo->id}}, '{{$todo->status}}')">
							<i class="fa fa-edit"></i> @lang('essentials::lang.change_status')
						</button>
						@endcan
					</div>
				</div>
				<div class="box-body">
					<div class="row">
						<div class="col-md-4">
							<strong>{{__('business.start_date')}}: </strong> {{@format_date($todo->date)}}<br>
							<strong>{{__('essentials::lang.end_date')}}: </strong> @if(!empty($todo->end_date)){{@format_date($todo->end_date)}}@endif<br>
							<strong>{{__('essentials::lang.estimated_hours')}}: </strong> {{$todo->estimated_hours}}
						</div>
						<div class="col-md-4">
							<strong>{{__('essentials::lang.assigned_by')}}: </strong> {{$todo->assigned_by?->user_full_name}}<br>
							<strong>{{__('essentials::lang.assigned_to')}}: </strong> {{implode(', ', $users)}}
						</div>
						<div class="col-md-4">
						<strong>{{__('essentials::lang.priority')}}: </strong> {{$priorities[$todo->priority] ?? ''}}<br>
						<strong>{{__('sale.status')}}: </strong>
						@php
							$status_text = $task_statuses[$todo->status] ?? '';
							$label_class = 'default';
							if (preg_match('/^completed$/i', $status_text)) {
								$label_class = 'success';
							} elseif (preg_match('/^in\s*progress$/i', $status_text)) {
								$label_class = 'warning';
							} elseif (preg_match('/^incomplete$/i', $status_text)) {
								$label_class = 'danger';
							}
						@endphp
						<span class="label label-{{$label_class}}">{{$status_text}}</span>
						</div>
						<div class="clearfix"></div>
						<div class="col-md-12">
							<br/>
							<strong>{{__('lang_v1.description')}}: </strong> {!! $todo->description !!}
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="nav-tabs-custom">
			    <ul class="nav nav-tabs">
			        <li class="active">
			            <a href="#comments_tab" data-toggle="tab" aria-expanded="true">
			                <i class="fa fa-comment"></i>
							@lang('essentials::lang.comments') </a>
			        </li>
			        <li>
			            <a href="#documents_tab" data-toggle="tab">
			                <i class="fa fa-file"></i>
						@lang('lang_v1.documents') </a>
			        </li>
			        <li>
			            <a href="#activities_tab" data-toggle="tab">
			                <i class="fa fa-pen-square"></i>
						@lang('lang_v1.activities') </a>
			        </li>
			    </ul>
			    <div class="tab-content">
			    	<div class="tab-pane active" id="comments_tab">
			    		<div class="row">
							{!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'addComment']), 'id' => 'task_comment_form', 'method' => 'post']) !!}
							<div class="col-md-6">
								<div class="form-group">
									{!! Form::label('comment', __('essentials::lang.add_comment') . ':') !!}
									{!! Form::textarea('comment', null, ['rows' => 3, 'class' => 'form-control', 'required']); !!}
									{!! Form::hidden('task_id', $todo->id); !!}
								</div>
							</div>
							<div class="col-md-12">
								<button type="submit" class="btn btn-primary pull-right ladda-button add-comment-btn" data-style="expand-right">
									<span class="ladda-label">
										@lang('messages.add')
									</span>
								</button>
							</div>
							{!! Form::close() !!}
							<div class="col-md-12">
								<hr>
								<div class="direct-chat-messages">
									@foreach($todo->comments as $comment)
										@include('essentials::todo.comment', 
										['comment' => $comment])
									@endforeach
								</div>
							</div>
						</div>
			    	</div>

			    	<div class="tab-pane" id="documents_tab">
			    		<div class="row">
							{!! Form::open(['url' => action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'uploadDocument']), 'id' => 'task_upload_doc_form', 'method' => 'post', 'files' => true]) !!}
							<div class="col-md-12">
								<div class="form-group">
									{!! Form::label('documents', __('lang_v1.upload_documents') . ':') !!}
									{!! Form::file('documents[]', ['id' => 'documents', 'multiple', 'required']); !!}
									{!! Form::hidden('task_id', $todo->id); !!}
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									{!! Form::label('description', __('lang_v1.description') . ':') !!}
									{!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3]); !!}
								</div>
							</div>
							<div class="col-md-12">
								<button type="submit" class="btn btn-primary pull-right">@lang('essentials::lang.upload')</button>
							</div>
							{!! Form::close() !!}
							<div class="col-md-12">
								<hr>
								<table class="table">
									<thead>
										<tr>
											<th>@lang('lang_v1.documents')</th>
											<th>@lang('lang_v1.description')</th>
											<th>@lang('lang_v1.uploaded_by')</th>
											<th>@lang('lang_v1.download')</th>
										</tr>
									</thead>
									<tbody>
										@foreach($todo->media as $media)
											<tr>
											    
												<td>{{$media->display_name}}</td>
												<td>{{$media->description}}</td>
												<td>{{$media->uploaded_by_user->user_full_name ?? ''}}</td>
												<td><a href="{{ asset( 'public/uploads/media/' . $media->file_name ) }} " download class="btn btn-success btn-xs">@lang('lang_v1.download')</a>

												@if(in_array(auth()->user()->id, [$media->uploaded_by, $todo->created_by]))
													<a href="{{action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'deleteDocument'], $media->id)}}" class="btn btn-danger btn-xs delete-document" data-media_id="{{$media->id}}"><i class="fa fa-trash"></i> @lang('messages.delete')</a>
												@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
			    	</div>
			    	<div class="tab-pane" id="activities_tab">
			    		<div class="row">
			    			<div class="col-md-12">
			    				@include('activity_log.activities', ['activity_type' => 'sell', 'statuses' => $task_statuses])
			    			</div>
			    		</div>
			    	</div>
			    </div>
			</div>
		</div>
	</div>
</section>
<div class="modal fade" id="task_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
</div>

@include('essentials::todo.update_task_status_modal')
@endsection

@section('javascript')
<script type="text/javascript">
// Function to set task ID and current status for status update modal
function setTaskIdForStatusUpdate(taskId, currentStatus) {
	$('#update_task_status_modal').find('#task_id').val(taskId);
	$('#update_task_status_modal').find('#updated_status').val(currentStatus).trigger('change');
}

// Handle status change in modal
$('#updated_status').on('change', function() {
	var status = $(this).val();
	if (status === 'Completed') {
		$('#completion_notice').show();
	} else {
		$('#completion_notice').hide();
	}
});

// Update status button click handler
$(document).on('click', '#update_status_btn', function(){
	var task_id = $('#update_task_status_modal').find('#task_id').val();
	var status = $('#update_task_status_modal').find('#updated_status').val();

	if (!status) {
		toastr.error('Please select a status');
		return;
	}

	// Show confirmation for completion with auto end date
	if (status === 'Completed') {
		if (!confirm('Are you sure you want to mark this task as completed? The end date will be automatically set to the current date and time.')) {
			return;
		}
	}

	var url = "/essentials/todo/" + task_id;
	$.ajax({
		method: "PUT",
		url: url,
		data: {status: status, only_status: true},
		dataType: "json",
		success: function(result){
			if(result.success == true){
				if (status === 'Completed') {
					toastr.success(result.msg + ' End date has been automatically set.');
				} else {
					toastr.success(result.msg);
				}
				$('#update_task_status_modal').modal('hide');
				// Reload the page to show updated status
				location.reload();
			} else {
				toastr.error(result.msg);
			}
		},
		error: function(xhr, status, error) {
			toastr.error('Failed to update status. Please try again.');
		}
	});
});

//form submit
$(document).on('submit', 'form#task_comment_form', function(e){
	e.preventDefault();
	var url = $(this).attr("action");
	var method = $(this).attr("method");
	var data = $("form#task_comment_form").serialize();
	var ladda = Ladda.create(document.querySelector('.add-comment-btn'));
	ladda.start();
	$.ajax({
		method: method,
		url: url,
		data: data,
		dataType: "json",
		success: function(result){
			ladda.stop();
			if(result.success == true){
				toastr.success(result.msg);
				$('.direct-chat-messages').prepend(result.comment_html);
				$("form#task_comment_form").find('#comment').val('');
			} else {
				toastr.error(result.msg);
			}
		}
	});
});
$(document).on('click', '.delete-comment', function(e){
	var element = $(this);
	$.ajax({
		url: '/essentials/todo/delete-comment/' + element.data('comment_id'),
		dataType: "json",
		success: function(result){
			if(result.success == true){
				toastr.success(result.msg);
				element.closest('.direct-chat-msg').remove();
			} else {
				toastr.error(result.msg);
			}
		}
	});
});

$(document).on('click', '.delete-document', function(e){
	e.preventDefault();
	var element = $(this);
	var url = $(this).attr('href');
	$.ajax({
		url: url,
		dataType: "json",
		success: function(result){
			if(result.success == true){
				toastr.success(result.msg);
				element.closest('tr').remove();
			} else {
				toastr.error(result.msg);
			}
		}
	});
});
</script>
@endsection