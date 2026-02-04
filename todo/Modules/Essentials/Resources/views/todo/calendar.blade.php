@extends('layouts.app')

@section('title', __('essentials::lang.todo') . ' - ' . __('lang_v1.calendar_view'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('essentials::lang.todo') - @lang('lang_v1.calendar_view')
        <small>@lang('essentials::lang.manage_your_todo')</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fas fa-calendar-alt"></i> @lang('lang_v1.calendar_view')
                    </h3>
                    <div class="box-tools pull-right">
                        <a href="{{ action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'index']) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-list"></i> @lang('lang_v1.list_view')
                        </a>
                        @can('essentials.add_todos')
                        <button type="button" class="btn btn-success btn-sm btn-modal" data-href="{{ action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'create']) }}" data-container="#task_modal">
                            <i class="fa fa-plus"></i> @lang('messages.add')
                        </button>
                        @endcan
                    </div>
                </div>

                <div class="box-body">
                    <!-- Calendar Filters -->
                    <div class="row" style="margin-bottom: 20px;">
                        @if(auth()->user()->can('essentials.assign_todos') && count($users) > 1)
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="calendar_user_filter">@lang('essentials::lang.employee'):</label>
                                <select id="calendar_user_filter" class="form-control select2" style="width: 100%;">
                                    <option value="">@lang('lang_v1.all')</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="calendar_status_filter">@lang('sale.status'):</label>
                                <select id="calendar_status_filter" class="form-control select2" style="width: 100%;">
                                    <option value="">@lang('lang_v1.all')</option>
                                    @foreach($task_statuses as $key => $status)
                                        <option value="{{ $key }}">{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="calendar_priority_filter">@lang('essentials::lang.priority'):</label>
                                <select id="calendar_priority_filter" class="form-control select2" style="width: 100%;">
                                    <option value="">@lang('lang_v1.all')</option>
                                    @foreach($priorities as $key => $priority)
                                        <option value="{{ $key }}">{{ $priority }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" id="refresh_calendar" class="btn btn-info">
                                    <i class="fas fa-sync-alt"></i> @lang('lang_v1.refresh')
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Legend -->
                    <div class="row" style="margin-bottom: 15px;">
                        <div class="col-md-12">
                            <div class="legend-container" style="text-align: center;">
                                <div class="legend-item" style="display: inline-block; margin: 0 15px;">
                                    <div class="legend-color" style="width: 20px; height: 20px; background: #28a745; display: inline-block; margin-right: 5px; border-radius: 3px;"></div>
                                    <span>@lang('essentials::lang.completed')</span>
                                </div>
                                <div class="legend-item" style="display: inline-block; margin: 0 15px;">
                                    <div class="legend-color" style="width: 20px; height: 20px; background: #ffc107; display: inline-block; margin-right: 5px; border-radius: 3px;"></div>
                                    <span>@lang('essentials::lang.in_progress')</span>
                                </div>
                                <div class="legend-item" style="display: inline-block; margin: 0 15px;">
                                    <div class="legend-color" style="width: 20px; height: 20px; background: #dc3545; display: inline-block; margin-right: 5px; border-radius: 3px;"></div>
                                    <span>@lang('essentials::lang.incomplete')</span>
                                </div>
                                <div class="legend-item" style="display: inline-block; margin: 0 15px;">
                                    <div class="legend-color" style="width: 20px; height: 20px; background: #6c757d; display: inline-block; margin-right: 5px; border-radius: 3px;"></div>
                                    <span>@lang('essentials::lang.on_hold')</span>
                                </div>
                                <div class="legend-item" style="display: inline-block; margin: 0 15px;">
                                    <div class="legend-color" style="width: 20px; height: 20px; background: #17a2b8; display: inline-block; margin-right: 5px; border-radius: 3px;"></div>
                                    <span>@lang('essentials::lang.new')</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Loading -->
                    <div id="calendar_loading" style="text-align: center; display: none;">
                        <i class="fas fa-spinner fa-spin fa-2x"></i> 
                        <p>@lang('lang_v1.loading')...</p>
                    </div>

                    <!-- Calendar Container -->
                    <div id="todo_calendar" style="opacity: 1;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Task Modal -->
<div class="modal fade" id="task_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>

<!-- Task Detail Modal -->
<div class="modal fade" id="task_detail_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="task_detail_title">@lang('essentials::lang.task_details')</h4>
            </div>
            <div class="modal-body" id="task_detail_content">
                <!-- Task details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
                <a href="#" id="view_task_link" class="btn btn-primary" target="_blank">@lang('messages.view')</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script src="{{ asset('plugins/fullcalendar/moment.min.js') }}"></script>
<script src="{{ asset('plugins/fullcalendar/fullcalendar.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {
    
    // Initialize Calendar
    initializeTodoCalendar();
    
    // Filter change events
    $('#calendar_user_filter, #calendar_status_filter, #calendar_priority_filter').on('change', function() {
        if ($('#todo_calendar').hasClass('fc')) {
            $('#todo_calendar').fullCalendar('refetchEvents');
        }
    });
    
    // Refresh button
    $('#refresh_calendar').on('click', function() {
        if ($('#todo_calendar').hasClass('fc')) {
            var btn = $(this);
            btn.prop('disabled', true);
            btn.html('<i class="fas fa-spinner fa-spin"></i> @lang("lang_v1.refreshing")...');
            
            $('#todo_calendar').fullCalendar('refetchEvents');
            
            setTimeout(function() {
                btn.prop('disabled', false);
                btn.html('<i class="fas fa-sync-alt"></i> @lang("lang_v1.refresh")');
                toastr.success('@lang("lang_v1.refreshed_successfully")');
            }, 1000);
        }
    });

    function initializeTodoCalendar() {
        console.log('Initializing Todo Calendar...');
        
        if ($('#todo_calendar').length && !$('#todo_calendar').hasClass('fc')) {
            console.log('Calendar container found, initializing FullCalendar...');
            
            $('#todo_calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'month',
                editable: false,
                eventLimit: true,
                eventLimitText: 'more',
                height: 'auto',
                allDayDefault: true, // Default all events to all-day for proper date alignment
                loading: function(bool) {
                    if (bool) {
                        $('#calendar_loading').show();
                        $('#todo_calendar').css('opacity', '0.5');
                    } else {
                        $('#calendar_loading').hide();
                        $('#todo_calendar').css('opacity', '1');
                    }
                },
                eventSources: [{
                    url: '{{ url("essentials/todo-calendar-data") }}',
                    data: function() {
                        return {
                            user_id: $('#calendar_user_filter').val(),
                            status: $('#calendar_status_filter').val(),
                            priority: $('#calendar_priority_filter').val(),
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    success: function(data) {
                        console.log('Calendar data received:', data);
                        if (data.length === 0) {
                            toastr.info('@lang("lang_v1.no_data_found")');
                        } else {
                            console.log('Total events loaded:', data.length);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Calendar data error:', error);
                        toastr.error('@lang("messages.something_went_wrong")');
                    }
                }],
                eventRender: function(event, element) {
                    // Add tooltip with task details
                    var tooltipContent = '<strong>' + event.title + '</strong><br>';
                    if (event.extendedProps.description) {
                        tooltipContent += '<strong>@lang("essentials::lang.description"):</strong> ' + event.extendedProps.description + '<br>';
                    }
                    if (event.extendedProps.assigned_to) {
                        tooltipContent += '<strong>@lang("essentials::lang.assigned_to"):</strong> ' + event.extendedProps.assigned_to + '<br>';
                    }
                    if (event.extendedProps.priority) {
                        tooltipContent += '<strong>@lang("essentials::lang.priority"):</strong> ' + event.extendedProps.priority + '<br>';
                    }
                    if (event.extendedProps.status) {
                        tooltipContent += '<strong>@lang("sale.status"):</strong> ' + event.extendedProps.status + '<br>';
                    }
                    // Use extendedProps dates if available (original dates), otherwise use event dates
                    if (event.extendedProps.start_date) {
                        tooltipContent += '<strong>@lang("lang_v1.start_date"):</strong> ' + moment(event.extendedProps.start_date).format('DD-MM-YYYY') + '<br>';
                    } else if (event.start) {
                        tooltipContent += '<strong>@lang("lang_v1.start_date"):</strong> ' + moment(event.start).format('DD-MM-YYYY') + '<br>';
                    }
                    if (event.extendedProps.end_date) {
                        tooltipContent += '<strong>@lang("lang_v1.end_date"):</strong> ' + moment(event.extendedProps.end_date).format('DD-MM-YYYY');
                    } else if (event.end) {
                        // FullCalendar's end date is exclusive, so subtract one day for display
                        tooltipContent += '<strong>@lang("lang_v1.end_date"):</strong> ' + moment(event.end).subtract(1, 'day').format('DD-MM-YYYY');
                    }
                    
                    element.attr('title', '');
                    element.popover({
                        content: tooltipContent,
                        html: true,
                        placement: 'auto',
                        trigger: 'hover',
                        container: 'body'
                    });
                    
                    // Add hover effect
                    element.hover(
                        function() { $(this).addClass('fc-event-hover'); },
                        function() { $(this).removeClass('fc-event-hover'); }
                    );
                },
                eventClick: function(event, jsEvent, view) {
                    if (event.extendedProps) {
                        showTaskDetails(event);
                    }
                }
            });
        }
    }

    function showTaskDetails(event) {
        var details = '<div class="task-detail-item">';
        details += '<h5><i class="fas fa-tasks"></i> ' + event.title + '</h5>';
        
        if (event.extendedProps.task_id) {
            details += '<div class="detail-row"><strong><i class="fas fa-hashtag"></i> @lang("essentials::lang.task_id"):</strong> <span>' + event.extendedProps.task_id + '</span></div>';
        }
        
        if (event.extendedProps.description) {
            details += '<div class="detail-row"><strong><i class="fas fa-align-left"></i> @lang("essentials::lang.description"):</strong><br><span>' + event.extendedProps.description + '</span></div>';
        }
        
        if (event.extendedProps.assigned_to) {
            details += '<div class="detail-row"><strong><i class="fas fa-user"></i> @lang("essentials::lang.assigned_to"):</strong> <span>' + event.extendedProps.assigned_to + '</span></div>';
        }
        
        if (event.extendedProps.assigned_by) {
            details += '<div class="detail-row"><strong><i class="fas fa-user-tie"></i> @lang("essentials::lang.assigned_by"):</strong> <span>' + event.extendedProps.assigned_by + '</span></div>';
        }
        
        if (event.extendedProps.priority) {
            var priorityClass = 'label-default';
            switch(event.extendedProps.priority) {
                case 'urgent': priorityClass = 'label-danger'; break;
                case 'high': priorityClass = 'label-warning'; break;
                case 'medium': priorityClass = 'label-info'; break;
                case 'low': priorityClass = 'label-success'; break;
            }
            details += '<div class="detail-row"><strong><i class="fas fa-exclamation-triangle"></i> @lang("essentials::lang.priority"):</strong> <span class="label ' + priorityClass + '">' + event.extendedProps.priority + '</span></div>';
        }
        
        if (event.extendedProps.status) {
            var statusClass = 'label-default';
            switch(event.extendedProps.status) {
                case 'Completed': statusClass = 'label-success'; break;
                case 'In progress': statusClass = 'label-warning'; break;
                case 'Incomplete': statusClass = 'label-danger'; break;
                case 'on_hold': statusClass = 'label-default'; break;
            }
            details += '<div class="detail-row"><strong><i class="fas fa-check-circle"></i> @lang("sale.status"):</strong> <span class="label ' + statusClass + '">' + event.extendedProps.status + '</span></div>';
        }
        
        if (event.start) {
            details += '<div class="detail-row"><strong><i class="fas fa-calendar-plus"></i> @lang("lang_v1.start_date"):</strong> <span>' + moment(event.start).format('dddd, DD MMMM YYYY') + '</span></div>';
        }
        
        if (event.end) {
            details += '<div class="detail-row"><strong><i class="fas fa-calendar-check"></i> @lang("lang_v1.end_date"):</strong> <span>' + moment(event.end).format('dddd, DD MMMM YYYY') + '</span></div>';
        }
        
        if (event.extendedProps.estimated_hours) {
            details += '<div class="detail-row"><strong><i class="fas fa-clock"></i> @lang("essentials::lang.estimated_hours"):</strong> <span>' + event.extendedProps.estimated_hours + ' hours</span></div>';
        }
        
        details += '</div>';
        
        $('#task_detail_content').html(details);
        $('#task_detail_title').text(event.title);
        $('#view_task_link').attr('href', event.url);
        $('#task_detail_modal').modal('show');
    }

    // Modal close event
    $('#task_modal').on('hidden.bs.modal', function () {
        if ($('#todo_calendar').hasClass('fc')) {
            $('#todo_calendar').fullCalendar('refetchEvents');
        }
    });
});
</script>

<style>
.fc-event-hover {
    opacity: 0.8;
    transform: scale(1.02);
    transition: all 0.2s ease;
}

.task-detail-item {
    padding: 15px;
}

.detail-row {
    margin-bottom: 10px;
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
}

.detail-row:last-child {
    border-bottom: none;
}

.legend-container {
    padding: 10px;
    background: #f9f9f9;
    border-radius: 5px;
    margin-bottom: 15px;
}

.legend-item {
    font-size: 14px;
}

.fc-event {
    cursor: pointer;
    border-top: none !important;
    border-bottom: none !important;
}

.fc-day-grid-event {
    margin: 1px;
    border-top: none !important;
    border-bottom: none !important;
}

/* Remove top and bottom borders from all calendar event bars */
.fc-event,
.fc-day-grid-event,
.fc-event-container .fc-event {
    border-top: none !important;
    border-bottom: none !important;
}

#todo_calendar {
    min-height: 600px;
}

/* Remove horizontal grid lines from calendar */
.fc-day-grid .fc-row {
    border-top: none !important;
}

.fc-day-grid .fc-day {
    border-top: none !important;
}

.fc-day-grid .fc-day-top {
    border-top: none !important;
}

.fc-day-grid .fc-day-number {
    border-top: none !important;
}

/* Remove horizontal borders from calendar cells */
.fc-day-grid .fc-day,
.fc-day-grid .fc-day-top,
.fc-day-grid .fc-day-number,
.fc-day-grid .fc-row {
    border-top: none !important;
}

/* Remove all horizontal borders from calendar table structure */
.fc-day-grid table,
.fc-day-grid tbody,
.fc-day-grid tr,
.fc-day-grid td {
    border-top: none !important;
}

/* Remove horizontal borders from week rows */
.fc-day-grid .fc-week,
.fc-day-grid .fc-row {
    border-top: none !important;
    border-bottom: none !important;
}

/* Custom calendar styles */
.fc-toolbar {
    margin-bottom: 1em;
}

.fc-button-group .fc-button {
    background: #3c8dbc;
    border-color: #367fa9;
    color: #fff;
}

.fc-button-group .fc-button:hover {
    background: #367fa9;
}

.fc-button-group .fc-button.fc-state-active {
    background: #204d74;
    border-color: #204d74;
}

.fc-today {
    background: #fcf8e3 !important;
}

.fc-event-title {
    font-weight: bold;
}
</style>
@endsection