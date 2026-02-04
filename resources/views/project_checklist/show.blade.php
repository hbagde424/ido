@extends('layouts.app')
@section('title', __('Project Details'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('Project Details')
        <small>{{ $project->project_name }}</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'index']) }}"><i class="fa fa-dashboard"></i> @lang('Project Checklists')</a></li>
        <li class="active">{{ $project->project_name }}</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    @if(session('status'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-check"></i> Success!</h4>
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-ban"></i> Error!</h4>
            {{ session('error') }}
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __('Project Information')])
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('Project Name'):</strong> {{ $project->project_name }}
                    </div>
                    <div class="col-md-6">
                        <strong>@lang('Created By'):</strong> {{ $project->createdBy ? $project->createdBy->user_full_name : '' }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>@lang('Created At'):</strong> {{ $project->created_at->format('d/m/Y H:i') }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>@lang('Assigned Users'):</strong> 
                        <div id="assigned_users_display" style="display: inline-block; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; border-radius: 4px; min-width: 200px; background-color: #f9f9f9;">
                            @if($project->users && $project->users->count() > 0)
                                {{ $project->users->pluck('user_full_name')->join(', ') }}
                            @else
                                <span class="text-muted">@lang('Click to assign users')</span>
                            @endif
                        </div>
                        <div id="assigned_users_edit" style="display: none; margin-top: 10px;">
                            {!! Form::select('assigned_users[]', $all_business_users, $project->users->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple', 'id' => 'assigned_users_select', 'style' => 'width: 100%;']) !!}
                            <div style="margin-top: 10px;">
                                <button type="button" class="btn btn-sm btn-success" id="save_assigned_users">
                                    <i class="fa fa-save"></i> @lang('Save')
                                </button>
                                <button type="button" class="btn btn-sm btn-default" id="cancel_assigned_users">
                                    @lang('Cancel')
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <!-- Task Statistics Cards and Pie Chart in Single Row -->
    <div class="row">
        <div class="col-md-3">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('Task Status')</h3>
                </div>
                <div class="box-body" style="padding: 10px;">
                    <canvas id="taskStatusChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $total_tasks }}</h3>
                    <p>@lang('Total Tasks')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-tasks"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $complete_tasks }}</h3>
                    <p>@lang('Complete Tasks')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $incomplete_tasks }}</h3>
                    <p>@lang('Incomplete Tasks')</p>
                </div>
                <div class="icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-success', 'title' => __('Tasks')])
                @slot('tool')
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTaskModal">
                        <i class="fa fa-plus"></i> @lang('Add Task')
                    </button>
                @endslot

                <!-- Range Controls -->
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="entries_per_page">@lang('Show entries per page'):</label>
                            <select id="entries_per_page" class="form-control" style="width: 100px; display: inline-block;">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="250">250</option>
                                <option value="500">500</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>@lang('Filter by Status'):</label>
                            <select id="task_status_filter" class="form-control" style="width: 150px; display: inline-block;">
                                <option value="">@lang('All Tasks')</option>
                                <option value="complete">@lang('Complete')</option>
                                <option value="incomplete">@lang('Incomplete')</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4 text-right">
                        <div class="form-group">
                            <label>@lang('Search'):</label>
                            <input type="text" id="task_search" class="form-control" placeholder="@lang('Search tasks...')" style="width: 200px; display: inline-block;">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="project_tasks_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('Task Name')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Start Date')</th>
                                <th>@lang('End Date')</th>
                                <th>@lang('User Name')</th>
                                <th>@lang('Task-Update')</th>
                                <th>@lang('Remark')</th>
                                <th>@lang('Timeline')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action([\App\Http\Controllers\ProjectChecklistController::class, 'addTask'], $project->id), 'method' => 'post', 'id' => 'add_task_form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTaskModalLabel">@lang('Add Task')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('task_name', __('Task Name') . ':*') !!}
                        {!! Form::text('task_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter task name')]) !!}
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('start_date', __('Start Date')) !!}
                                {!! Form::text('start_date', null, ['class' => 'form-control date-picker', 'placeholder' => __('Select start date'), 'readonly']) !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('end_date', __('End Date')) !!}
                                {!! Form::text('end_date', null, ['class' => 'form-control date-picker', 'placeholder' => __('Select end date'), 'readonly']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('user_id', __('Assigned To')) !!}
                        {!! Form::select('user_id', $users, auth()->user()->id, ['class' => 'form-control select2', 'placeholder' => __('Select user')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('status', __('Status')) !!}
                        {!! Form::checkbox('status', 1, false, ['class' => 'input-icheck']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('remark', __('Remark')) !!}
                        {!! Form::textarea('remark', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter remark')]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Add Comment Modal -->
<div class="modal fade" id="addCommentModal" tabindex="-1" role="dialog" aria-labelledby="addCommentModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="addCommentModalLabel">@lang('Add Task Comment')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="comments-container" style="max-height: 300px; overflow-y: auto; margin-bottom: 20px; border: 1px solid #ddd; padding: 15px; border-radius: 5px;">
                            <p class="text-center text-muted">@lang('Loading comments...')</p>
                        </div>
                    </div>
                </div>
                {!! Form::open(['url' => '', 'method' => 'post', 'id' => 'add_comment_form', 'enctype' => 'multipart/form-data']) !!}
                    <input type="hidden" name="task_id" id="comment_task_id">
                    <div class="form-group">
                        {!! Form::label('comment', __('Day by Day Progress / Comment') . ':*') !!}
                        {!! Form::textarea('comment', null, ['class' => 'form-control', 'required', 'rows' => 5, 'placeholder' => __('Enter your progress update or comment...')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('document', __('Attach Document') . ' (Optional)') !!}
                        {!! Form::file('document', ['class' => 'form-control', 'accept' => '.pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png']) !!}
                        <small class="help-block">@lang('Maximum file size: 10MB. Allowed formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG')</small>
                    </div>
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
                <button type="button" class="btn btn-primary" id="submit_comment_btn">@lang('messages.save')</button>
            </div>
        </div>
    </div>
</div>

<!-- Timeline View Modal -->
<div class="modal fade" id="timelineModal" tabindex="-1" role="dialog" aria-labelledby="timelineModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="timelineModalLabel">@lang('Task Timeline')</h4>
            </div>
            <div class="modal-body">
                <div id="timeline-container">
                    <p class="text-center text-muted">@lang('Loading timeline...')</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // Assigned Users Edit Functionality
        $('#assigned_users_display').on('click', function() {
            $(this).hide();
            $('#assigned_users_edit').show();
            
            // Destroy existing select2 if any
            if ($('#assigned_users_select').hasClass('select2-hidden-accessible')) {
                $('#assigned_users_select').select2('destroy');
            }
            
            // Initialize select2 with proper configuration
            $('#assigned_users_select').select2({
                dropdownParent: $('#assigned_users_edit'),
                width: '100%',
                closeOnSelect: true
            });
            
            // Force close dropdown on select
            $('#assigned_users_select').on('select2:select', function (e) {
                var $select = $(this);
                // Small delay to allow selection to complete
                setTimeout(function() {
                    $select.select2('close');
                }, 10);
            });
        });
        
        $('#cancel_assigned_users').on('click', function() {
            $('#assigned_users_edit').hide();
            $('#assigned_users_display').show();
        });
        
        $('#save_assigned_users').on('click', function() {
            var selectedUsers = $('#assigned_users_select').val();
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> @lang("Saving...")');
            
            $.ajax({
                url: '{{ action([\App\Http\Controllers\ProjectChecklistController::class, "updateAssignedUsers"], [$project->id]) }}',
                method: 'POST',
                data: {
                    assigned_users: selectedUsers || [],
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        // Update display
                        if (response.user_names && response.user_names.length > 0) {
                            $('#assigned_users_display').html(response.user_names.join(', '));
                        } else {
                            $('#assigned_users_display').html('<span class="text-muted">@lang("Click to assign users")</span>');
                        }
                        $('#assigned_users_edit').hide();
                        $('#assigned_users_display').show();
                    } else {
                        toastr.error(response.msg);
                    }
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> @lang("Save")');
                },
                error: function(xhr) {
                    var errorMsg = '@lang("Something went wrong. Please try again.")';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }
                    toastr.error(errorMsg);
                    btn.prop('disabled', false).html('<i class="fa fa-save"></i> @lang("Save")');
                }
            });
        });
        var projectName = {!! json_encode($project->project_name) !!};
        var projectFileName = {!! json_encode(\Illuminate\Support\Str::slug($project->project_name)) !!};
        
        // Initialize Task Status Pie Chart
        var taskStatusChart = null;
        
        function initTaskStatusChart(completeCount, incompleteCount, totalCount) {
            var taskCtx = document.getElementById('taskStatusChart').getContext('2d');
            
            // Destroy existing chart if it exists
            if (taskStatusChart) {
                taskStatusChart.destroy();
            }
            
            taskStatusChart = new Chart(taskCtx, {
                type: 'pie',
                data: {
                    labels: ['@lang("Complete")', '@lang("Incomplete")'],
                    datasets: [{
                        data: [completeCount, incompleteCount],
                        backgroundColor: [
                            '#28a745', // Green for Complete
                            '#dc3545'  // Red for Incomplete
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var label = context.label || '';
                                    var value = context.parsed || 0;
                                    var percentage = totalCount > 0 ? ((value / totalCount) * 100).toFixed(1) : 0;
                                    return label + ': ' + value + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Initialize chart with initial data
        initTaskStatusChart({{ $complete_tasks }}, {{ $incomplete_tasks }}, {{ $total_tasks }});
        
        // Function to update task statistics
        function updateTaskStatistics() {
            // Make AJAX call to get updated statistics
            $.ajax({
                url: '{{ action([\App\Http\Controllers\ProjectChecklistController::class, "show"], [$project->id]) }}',
                method: 'GET',
                data: { get_stats_only: true },
                dataType: 'json',
                success: function(response) {
                    var total = response.total_tasks || 0;
                    var complete = response.complete_tasks || 0;
                    var incomplete = response.incomplete_tasks || 0;
                    
                    // Update cards
                    $('.bg-aqua .inner h3').text(total);
                    $('.bg-green .inner h3').text(complete);
                    $('.bg-red .inner h3').text(incomplete);
                    
                    // Update pie chart
                    initTaskStatusChart(complete, incomplete, total);
                },
                error: function() {
                    // Fallback: reload table and count
                    project_tasks_table.ajax.reload(null, false);
                }
            });
        }
        
        // Initialize DataTable for project tasks with server-side processing
        var project_tasks_table = $('#project_tasks_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'show'], [$project->id]) }}",
                type: 'GET',
                data: function(d) {
                    d.search_value = $('#task_search').val();
                }
            },
            columns: [
                { data: 'sr_no', name: 'sr_no', orderable: false, searchable: false },
                { data: 'task_name', name: 'task_name' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'start_date', name: 'start_date', orderable: false, searchable: false },
                { data: 'end_date', name: 'end_date', orderable: false, searchable: false },
                { data: 'user_name', name: 'user_name', orderable: false, searchable: false },
                { data: 'task_update', name: 'task_update', orderable: false, searchable: false },
                { data: 'remark', name: 'remark', orderable: false, searchable: false },
                { data: 'timeline', name: 'timeline', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100, 250, 500],
            order: [[1, 'asc']], // Order by Task Name
            dom: 'Blfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: projectName,
                    filename: projectFileName,
                    exportOptions: {
                        // Export Sr No, Task Name, Status, Start Date, End Date, User Name, Task-Update, Remark and Timeline (exclude Action column)
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        format: {
                            body: function (data, row, column, node) {
                                // Convert the Status checkbox HTML into human-readable text for export
                                if (column === 2) {
                                    var wrapper = $('<div>').html(data);
                                    var cb = wrapper.find('input[type="checkbox"]');
                                    if (cb.length) {
                                        // Use checkbox symbols for export: checked -> ☑, unchecked -> ☐
                                        return cb.is(':checked') ? '☑' : '☐';
                                    }
                                }
                                // For other columns, strip HTML and return text
                                var txt = $('<div>').html(data).text();
                                return txt;
                            }
                        }
                    }
                },
                {
                    extend: 'csvHtml5',
                    title: projectName,
                    filename: projectFileName,
                    exportOptions: {
                        // Export Sr No, Task Name, Status, Start Date, End Date, User Name, Task-Update, Remark and Timeline (exclude Action column)
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        format: {
                            body: function (data, row, column, node) {
                                if (column === 2) {
                                    var wrapper = $('<div>').html(data);
                                    var cb = wrapper.find('input[type="checkbox"]');
                                    if (cb.length) {
                                        // Use checkbox symbols for export: checked -> ☑, unchecked -> ☐
                                        return cb.is(':checked') ? '☑' : '☐';
                                    }
                                }
                                var txt = $('<div>').html(data).text();
                                return txt;
                            }
                        }
                    }
                }
            ],
            language: {
                processing: 'Loading tasks...',
                lengthMenu: 'Show _MENU_ tasks per page',
                info: 'Showing _START_ to _END_ of _TOTAL_ tasks',
                infoEmpty: 'No tasks available',
                infoFiltered: '(filtered from _MAX_ total tasks)',
                search: 'Search tasks:',
                paginate: {
                    first: 'First',
                    last: 'Last',
                    next: 'Next',
                    previous: 'Previous'
                }
            }
        });

        // Custom entries per page control
        $('#entries_per_page').on('change', function() {
            var pageLength = parseInt($(this).val());
            project_tasks_table.page.len(pageLength).draw();
        });

        // Custom search control
        $('#task_search').on('keyup', function() {
            project_tasks_table.search($(this).val()).draw();
        });

        // Task status filter
        var taskStatusFilter = '';
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                if (settings.nTable.id !== 'project_tasks_table') {
                    return true;
                }
                if (taskStatusFilter === '') {
                    return true;
                }
                var row = project_tasks_table.row(dataIndex).node();
                var statusCell = $(row).find('td:eq(2)'); // Status is 3rd column (index 2)
                var checkbox = statusCell.find('input[type="checkbox"]');
                var isChecked = checkbox.length > 0 && checkbox.is(':checked');
                
                if (taskStatusFilter === 'complete') {
                    return isChecked;
                } else if (taskStatusFilter === 'incomplete') {
                    return !isChecked;
                }
                return true;
            }
        );

        $('#task_status_filter').on('change', function() {
            taskStatusFilter = $(this).val();
            project_tasks_table.draw();
        });

        // Sync the custom dropdown with DataTable's length menu
        project_tasks_table.on('length.dt', function(e, settings, len) {
            $('#entries_per_page').val(len);
        });
        $('#add_task_form').validate();

        // Initialize date pickers for start_date and end_date
        function initDatePickers() {
            if (typeof datepicker_date_format !== 'undefined') {
                $('.date-picker').datepicker({
                    format: datepicker_date_format,
                    autoclose: true,
                    todayHighlight: true
                });
            } else {
                $('.date-picker').datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true,
                    todayHighlight: true
                });
            }
        }
        initDatePickers();

        // Re-initialize date pickers and select2 when modal opens
        $('#addTaskModal').on('shown.bs.modal', function() {
            initDatePickers();
            // Initialize select2 for user dropdown if not already initialized
            if ($('#addTaskModal #user_id').length && !$('#addTaskModal #user_id').hasClass('select2-hidden-accessible')) {
                $('#addTaskModal #user_id').select2({
                    dropdownParent: $('#addTaskModal')
                });
            }
        });

        // Handle add task form submission
        $('#add_task_form').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            
            // Convert date format from dd/mm/yyyy to yyyy-mm-dd for backend
            var startDate = $('#start_date').val();
            var endDate = $('#end_date').val();
            
            if (startDate) {
                var parts = startDate.split('/');
                if (parts.length === 3) {
                    startDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }
            
            if (endDate) {
                var parts = endDate.split('/');
                if (parts.length === 3) {
                    endDate = parts[2] + '-' + parts[1] + '-' + parts[0];
                }
            }
            
            var data = form.serialize();
            // Replace date values with converted format
            if (startDate) {
                data = data.replace(/start_date=[^&]*/, 'start_date=' + startDate);
            }
            if (endDate) {
                data = data.replace(/end_date=[^&]*/, 'end_date=' + endDate);
            }

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        $('#addTaskModal').modal('hide');
                        form[0].reset();
                        // Reload task table and update statistics
                        project_tasks_table.ajax.reload(null, false);
                        // Update statistics after a short delay to ensure table is reloaded
                        setTimeout(function() {
                            updateTaskStatistics();
                        }, 500);
                        // Redirect to tasks list page if server suggests
                        if (response.redirect) {
                            // Don't redirect, just reload stats
                        }
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function() {
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        });

        $(document).on('change', '.task-status', function() {
            var taskId = $(this).data('task-id');
            var checkbox = $(this);
            
            // Prevent unchecking if checkbox is disabled (non-creator trying to uncheck)
            if (checkbox.is(':disabled') && !checkbox.is(':checked')) {
                checkbox.prop('checked', true);
                toastr.error('@lang("Only project creator can uncheck completed tasks.")');
                return;
            }
            
            var status = checkbox.is(':checked') ? 1 : 0;
            var remark = $('.task-remark[data-task-id="' + taskId + '"]').val();

            $.ajax({
                url: '{{ url("project-tasks") }}/' + taskId + '/update',
                method: 'POST',
                data: {
                    status: status,
                    remark: remark,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        // Update the Task-Update column in the same row
                        var row = checkbox.closest('tr');
                        var taskUpdateCell = row.find('td').eq(6); // Task-Update is 7th column (index 6)
                        var bgColor = status == 1 ? '#28a745' : '#dc3545';
                        var text = status == 1 ? 'Complete' : 'Incomplete';
                        taskUpdateCell.html('<div class="task-status-bar" style="background-color: ' + bgColor + '; color: white; padding: 8px 12px; text-align: center; border-radius: 4px; font-weight: bold; min-width: 120px;">' + text + '</div>');
                        
                        // If task is now checked and user is not project creator, disable the checkbox
                        if (status == 1) {
                            var isProjectCreator = {{ $project->created_by == auth()->user()->id ? 'true' : 'false' }};
                            if (!isProjectCreator) {
                                checkbox.prop('disabled', true);
                                checkbox.attr('title', '@lang("Only project creator can uncheck this task")');
                            }
                        } else {
                            // If unchecked, enable checkbox
                            checkbox.prop('disabled', false);
                            checkbox.removeAttr('title');
                        }
                        
                        // Reload task statistics and pie chart
                        updateTaskStatistics();
                        
                        // if server sent redirect, follow it to stay on project page
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        toastr.error(response.msg);
                        // Revert checkbox if update failed
                        checkbox.prop('checked', !checkbox.is(':checked'));
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Error updating task status';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }
                    toastr.error(errorMsg);
                    // Revert checkbox on error
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            });
        });

        $(document).on('blur', '.task-remark', function() {
            var taskId = $(this).data('task-id');
            var status = $('.task-status[data-task-id="' + taskId + '"]').is(':checked') ? 1 : 0;
            var remark = $(this).val();

            $.ajax({
                url: '{{ url("project-tasks") }}/' + taskId + '/update',
                method: 'POST',
                data: {
                    task_name: '', // Not updating name here
                    status: status,
                    remark: remark,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                    } else {
                        toastr.error(response.msg);
                    }
                }
            });
        });

        $(document).on('click', '.update-task', function() {
            var taskId = $(this).data('task-id');
            var status = $('.task-status[data-task-id="' + taskId + '"]').is(':checked') ? 1 : 0;
            var remark = $('.task-remark[data-task-id="' + taskId + '"]').val();

            $.ajax({
                url: '{{ url("project-tasks") }}/' + taskId + '/update',
                method: 'POST',
                data: {
                    status: status,
                    remark: remark,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        // follow server redirect (should point to current project show)
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    } else {
                        toastr.error(response.msg);
                    }
                }
            });
        });

        $(document).on('click', '.delete-task', function() {
            var taskId = $(this).data('task-id');
            if (confirm('Are you sure you want to delete this task?')) {
                $.ajax({
                    url: '{{ url("project-tasks") }}/' + taskId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            // follow server redirect (stay on project show page)
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                location.reload();
                            }
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            }
        });

        // Handle comment button click
        $(document).on('click', '.add-comment-btn', function() {
            var taskId = $(this).data('task-id');
            var taskName = $(this).data('task-name');
            
            $('#comment_task_id').val(taskId);
            $('#addCommentModalLabel').text('@lang("Add Task Comment") - ' + taskName);
            $('#add_comment_form').attr('action', '{{ url("project-tasks") }}/' + taskId + '/comments');
            $('#add_comment_form')[0].reset();
            
            // Load existing comments
            loadComments(taskId);
            
            $('#addCommentModal').modal('show');
        });

        // Load comments for a task
        function loadComments(taskId) {
            $('#comments-container').html('<p class="text-center text-muted">@lang("Loading comments...")</p>');
            
            $.ajax({
                url: '{{ url("project-tasks") }}/' + taskId + '/comments',
                method: 'GET',
                success: function(response) {
                    $('#comments-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading comments:', xhr.responseText);
                    var errorMsg = '@lang("Error loading comments")';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    } else if (xhr.status === 404) {
                        errorMsg = '@lang("Comments route not found")';
                    } else if (xhr.status === 403) {
                        errorMsg = '@lang("You do not have permission to view comments")';
                    } else if (xhr.status === 500) {
                        errorMsg = '@lang("Server error. Please check the logs.")';
                    }
                    $('#comments-container').html('<p class="text-danger text-center">' + errorMsg + '</p>');
                }
            });
        }

        // Handle timeline view button click
        $(document).on('click', '.view-timeline-btn', function() {
            var taskId = $(this).data('task-id');
            var taskName = $(this).data('task-name');
            
            $('#timelineModalLabel').text('@lang("Task Timeline") - ' + taskName);
            
            // Load timeline
            loadTimeline(taskId);
            
            $('#timelineModal').modal('show');
        });

        // Load timeline for a task
        function loadTimeline(taskId) {
            $('#timeline-container').html('<p class="text-center text-muted">@lang("Loading timeline...")</p>');
            
            $.ajax({
                url: '{{ url("project-tasks") }}/' + taskId + '/timeline',
                method: 'GET',
                success: function(response) {
                    $('#timeline-container').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error loading timeline:', xhr.responseText);
                    var errorMsg = '@lang("Error loading timeline")';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    } else if (xhr.status === 404) {
                        errorMsg = '@lang("Timeline route not found")';
                    } else if (xhr.status === 403) {
                        errorMsg = '@lang("You do not have permission to view timeline")';
                    } else if (xhr.status === 500) {
                        errorMsg = '@lang("Server error. Please check the logs.")';
                    }
                    $('#timeline-container').html('<p class="text-danger text-center">' + errorMsg + '</p>');
                }
            });
        }

        // Handle comment form submission
        $('#submit_comment_btn').on('click', function() {
            var form = $('#add_comment_form');
            var formData = new FormData(form[0]);
            formData.append('_token', '{{ csrf_token() }}');
            
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.msg);
                        form[0].reset();
                        // Reload comments
                        var taskId = $('#comment_task_id').val();
                        loadComments(taskId);
                        // Reload task table to show updated comment count in timeline button
                        project_tasks_table.ajax.reload(null, false);
                    } else {
                        toastr.error(response.msg);
                    }
                },
                error: function(xhr) {
                    var errorMsg = '@lang("Something went wrong. Please try again.")';
                    if (xhr.responseJSON && xhr.responseJSON.msg) {
                        errorMsg = xhr.responseJSON.msg;
                    }
                    toastr.error(errorMsg);
                }
            });
        });
    });
</script>
@endpush