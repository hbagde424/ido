@extends('layouts.app')
@section('title', __('All Tasks'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('All Tasks')
        <small>@lang('List of tasks across projects')</small>
    </h1>
    
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __('Tasks')])
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('Project')</label>
                    <select id="filter_project" class="form-control">
                        <option value="">@lang('All')</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->project_name }}">{{ $proj->project_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>@lang('Status')</label>
                    <select id="filter_status" class="form-control">
                        <option value="">@lang('All')</option>
                        <option value="{{ __('Completed') }}">@lang('Completed')</option>
                        <option value="{{ __('Pending') }}">@lang('Pending')</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="tasks_table">
                <thead>
                    <tr>
                        <th>@lang('Project')</th>
                        <th>@lang('Task')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Remark')</th>
                                                <th>@lang('Created At')</th>
                                                <th>@lang('Actions')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks->get() as $task)
                        <tr>
                            <td>{{ $task->project ? $task->project->project_name : '' }}</td>
                            <td>{{ $task->task_name }}</td>
                            <td>{{ $task->status ? __('Completed') : __('Pending') }}</td>
                            <td>{{ $task->remark }}</td>
                            <td>{{ $task->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                                @if($task->project)
                                                                        <a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$task->project->id]) }}" class="btn btn-xs btn-default" title="@lang('View Project')">
                                                                                <i class="fa fa-eye"></i>
                                                                        </a>
                                                                @endif

                                                                <button type="button" class="btn btn-xs btn-primary edit-task" 
                                                                        data-id="{{ $task->id }}" 
                                                                        data-name="{{ htmlspecialchars($task->task_name, ENT_QUOTES, 'UTF-8') }}" 
                                                                        data-remark="{{ htmlspecialchars($task->remark, ENT_QUOTES, 'UTF-8') }}" 
                                                                        data-status="{{ $task->status ? 1 : 0 }}"
                                                                        data-project_id="{{ $task->project ? $task->project->id : '' }}"
                                                                        title="@lang('Edit Task')">
                                                                        <i class="fa fa-edit"></i>
                                                                </button>

                                                                <button type="button" class="btn btn-xs btn-danger delete-task" data-id="{{ $task->id }}" title="@lang('Delete Task')">
                                                                        <i class="fa fa-trash"></i>
                                                                </button>
                                                        </td>
                                                </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
</section>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-labelledby="editTaskModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="editTaskModalLabel">@lang('Edit Task')</h4>
            </div>
            <form id="edit_task_form">
                <div class="modal-body">
                    <input type="hidden" name="task_id" id="edit_task_id">

                    <div class="form-group">
                        <label for="edit_task_name">@lang('Task')</label>
                        <input type="text" class="form-control" name="task_name" id="edit_task_name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_task_remark">@lang('Remark')</label>
                        <textarea class="form-control" name="remark" id="edit_task_remark"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="status" id="edit_task_status" value="1"> @lang('Completed')
                            </label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            </form>
        </div>
    </div>
</div>

    @push('js')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var tasksTable = $('#tasks_table').DataTable({
            // client-side table as data already rendered
            processing: true
        });

        // Filter by project and status
        $('#filter_project, #filter_status').on('change', function() {
            var projVal = $('#filter_project').val();
            var statusVal = $('#filter_status').val();

            // Project is column 0, Status is column 2
            tasksTable.column(0).search(projVal).column(2).search(statusVal).draw();
        });

        // Open edit modal and populate
        $(document).on('click', '.edit-task', function() {
            var id = $(this).data('id');
            $('#edit_task_id').val(id);
            $('#edit_task_name').val($(this).data('name'));
            $('#edit_task_remark').val($(this).data('remark'));
            $('#edit_task_status').prop('checked', $(this).data('status') == 1);
            $('#editTaskModal').modal('show');
        });

        // Submit edit form via AJAX
        $('#edit_task_form').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_task_id').val();
            var data = {
                task_name: $('#edit_task_name').val(),
                remark: $('#edit_task_remark').val(),
                status: $('#edit_task_status').is(':checked') ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            };

            $.ajax({
                method: 'POST',
                url: '/project-tasks/' + id + '/update',
                data: data,
                success: function(result) {
                    if (result.success) {
                        toastr.success(result.msg);
                        // reload page to refresh table
                        location.reload();
                    } else {
                        toastr.error(result.msg || '@lang("messages.something_went_wrong")');
                    }
                },
                error: function(xhr) {
                    toastr.error('@lang("messages.something_went_wrong")');
                }
            });
        });

        // Delete task
        $(document).on('click', '.delete-task', function() {
            var id = $(this).data('id');
            swal({
                title: LANG.sure,
                text: LANG.confirm_delete,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        method: 'DELETE',
                        url: '/project-tasks/' + id,
                        data: { _token: $('meta[name="csrf-token"]').attr('content') },
                        success: function(result) {
                            if (result.success) {
                                toastr.success(result.msg);
                                location.reload();
                            } else {
                                toastr.error(result.msg || LANG.something_went_wrong);
                            }
                        },
                        error: function() {
                            toastr.error(LANG.something_went_wrong);
                        }
                    });
                }
            });
        });
    });
</script>
@endpush

@endsection
