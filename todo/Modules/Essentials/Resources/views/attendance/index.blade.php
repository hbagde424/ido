@extends('layouts.app')
@section('title', __('essentials::lang.attendance'))

@section('css')
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css">
    <!-- Font Awesome for better icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Modern Calendar Styling */
        .calendar-container {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin: 20px 0;
        }
        
        .calendar-header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .employee-filter-section {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 300px;
        }
        
        .filter-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
            font-size: 14px;
        }
        
        .modern-select {
            width: 100%;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            background: white;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .modern-select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .legend-container {
            display: flex;
            gap: 25px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            background: rgba(255,255,255,0.8);
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #4a5568;
        }
        
        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .calendar-main {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        /* Modern Tooltip Styles */
        .modern-popover {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .modern-popover .arrow::after {
            border-top-color: #667eea;
        }
        
        .modern-tooltip {
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        
        .tooltip-header {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }
        
        .tooltip-date {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 8px;
        }
        
        .tooltip-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            margin: 4px 0;
            display: inline-block;
        }
        
        .tooltip-status.success {
            background: rgba(16, 185, 129, 0.3);
            border: 1px solid rgba(16, 185, 129, 0.5);
        }
        
        .tooltip-status.warning {
            background: rgba(245, 158, 11, 0.3);
            border: 1px solid rgba(245, 158, 11, 0.5);
        }
        
        .tooltip-status.danger {
            background: rgba(239, 68, 68, 0.3);
            border: 1px solid rgba(239, 68, 68, 0.5);
        }
        
        .tooltip-status.info {
            background: rgba(139, 92, 246, 0.3);
            border: 1px solid rgba(139, 92, 246, 0.5);
        }
        
        .tooltip-status.sunday {
            background: rgba(249, 115, 22, 0.3);
            border: 1px solid rgba(249, 115, 22, 0.5);
        }
        
        .tooltip-time, .tooltip-duration, .tooltip-leave-info {
            font-size: 11px;
            margin: 2px 0;
            opacity: 0.9;
        }
        
        .tooltip-leave-info {
            padding: 2px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .tooltip-leave-info:last-child {
            border-bottom: none;
        }
        
        .fc-event-hover {
            transform: scale(1.05);
            z-index: 999;
        }
    </style>
@endsection

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.attendance')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    @if (session('notification') || !empty($notification))
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    @if(!empty($notification['msg']))
                        {{$notification['msg']}}
                    @elseif(session('notification.msg'))
                        {{ session('notification.msg') }}
                    @endif
                </div>
            </div>  
        </div>     
    @endif
    @if($is_employee_allowed)
        <div class="row">
            <div class="col-md-12 text-center">
                <button 
                    type="button" 
                    class="btn btn-app bg-blue clock_in_btn
                        @if(!empty($clock_in))
                            hide
                        @endif
                    "
                    data-type="clock_in"
                    >
                    <i class="fas fa-arrow-circle-down"></i> @lang('essentials::lang.clock_in')
                </button>
            &nbsp;&nbsp;&nbsp;
                <button 
                    type="button" 
                    class="btn btn-app bg-yellow clock_out_btn
                        @if(empty($clock_in))
                            hide
                        @endif
                    "  
                    data-type="clock_out"
                    >
                    <i class="fas fa-hourglass-half fa-spin"></i> @lang('essentials::lang.clock_out')
                </button>
                @if(!empty($clock_in))
                    <br>
                    <small class="text-muted">@lang('essentials::lang.clocked_in_at'): {{@format_datetime($clock_in->clock_in_time)}}</small>
                @endif
            </div>
        </div>
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    @can('essentials.crud_all_attendance')
                        <li class="active">
                            <a href="#shifts_tab" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-user-clock" aria-hidden="true"></i>
                                @lang('essentials::lang.shifts')
                                @show_tooltip(__('essentials::lang.shift_datatable_tooltip'))
                            </a>
                        </li>
                    @endcan
                    <li @if(!auth()->user()->can('essentials.crud_all_attendance')) class="active" @endif>
                        <a href="#attendance_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-check-square" aria-hidden="true"></i> @lang( 'essentials::lang.all_attendance' )</a>
                    </li>
                    @can('essentials.crud_all_attendance')
                    <!-- <li>
                        <a href="#attendance_by_shift_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-user-check" aria-hidden="true"></i> @lang('essentials::lang.attendance_by_shift')</a>
                    </li> -->
                    <li>
                        <a href="#attendance_by_date_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-calendar" aria-hidden="true"></i> @lang('essentials::lang.attendance_by_date')</a>
                    </li>
                    <li>
                        <a href="#import_attendance_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-download" aria-hidden="true"></i> @lang('essentials::lang.import_attendance')</a>
                    </li>
                    @endcan
                    @if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
                    <li>
                        <a href="#calendar_view_tab" data-toggle="tab" aria-expanded="true"><i class="fas fa-calendar-alt" aria-hidden="true"></i> Calendar View</a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @can('essentials.crud_all_attendance')
                        <div class="tab-pane active" id="shifts_tab">
                            <button type="button" class="btn btn-primary pull-right"  data-toggle="modal" data-target="#shift_modal"> <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                            <br>
                            <br>
                            <br>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="shift_table">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang_v1.name' )</th>
                                            <th>@lang( 'essentials::lang.shift_type' )</th>
                                            <th>@lang( 'restaurant.start_time' )</th>
                                            <th>@lang( 'restaurant.end_time' )</th>
                                            <th>@lang( 'essentials::lang.holiday' )</th>
                                            <th>@lang( 'messages.action' )</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    @endcan
                    <div class="tab-pane @if(!auth()->user()->can('essentials.crud_all_attendance')) active @endif" id="attendance_tab">
                        <div class="row">
                            @can('essentials.crud_all_attendance')
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('employee_id', __('essentials::lang.employee') . ':') !!}
                                        {!! Form::select('employee_id', $employees, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                                    </div>
                                </div>
                            @endcan
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                                </div>
                            </div>
                            @can('essentials.crud_all_attendance')
                            <div class="col-md-6 spacer">
                                <!-- Hidden buttons -->
                                <button type="button" class="btn btn-success pull-right" onclick="exportToExcel()" style="margin-right: 10px; display: none;">
                                    <i class="fa fa-file-excel-o"></i>
                                    Export to Excel
                                </button>
                                <button type="button" class="btn btn-info pull-right" onclick="exportToCSV()" style="margin-right: 10px;">
                                    <i class="fa fa-file-text-o"></i>
                                    Export to CSV
                                </button>
                                <button type="button" class="btn btn-warning pull-right" onclick="testExport()" style="margin-right: 10px; display: none;">
                                    <i class="fa fa-bug"></i>
                                    Test Export
                                </button>
                                <button type="button" class="btn btn-primary btn-modal pull-right" data-href="{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'create'])}}" data-container="#attendance_modal">
                                    <i class="fa fa-plus"></i>
                                    @lang( 'essentials::lang.add_latest_attendance' )
                                </button>
                            </div>
                            @endcan
                        </div>
                        <div id="user_attendance_summary" class="hide">
                            <h3>
                                <strong>@lang('essentials::lang.total_work_hours'):</strong>
                                <span id="total_work_hours"></span>
                            </h3>
                        </div>
                        <br><br>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="attendance_table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>@lang( 'lang_v1.date' )</th>
                                        <th>@lang('essentials::lang.employee')</th>
                                        <th>Department</th>
                                        <th>location</th>
                                        
                                        <th>@lang('essentials::lang.clock_in')</th>
                                        <th>@lang('essentials::lang.clock_out')</th>
                                        <th>@lang('essentials::lang.work_duration')</th>
                                        <th>@lang('essentials::lang.ip_address')</th>
                                        <th>@lang('essentials::lang.shift')</th>
                                        @can('essentials.crud_all_attendance')
                                            <th>@lang( 'messages.action' )</th>
                                        @endcan
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="attendance_by_shift_tab">
                        @include('essentials::attendance.attendance_by_shift')
                    </div>
                    <div class="tab-pane" id="attendance_by_date_tab">
                        @include('essentials::attendance.attendance_by_date')
                    </div>
                    @if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
                    <div class="tab-pane" id="calendar_view_tab">
                        @include('essentials::attendance.calendar_view')
                    </div>
                    @endif
                    @can('essentials.crud_all_attendance')
                        <div class="tab-pane" id="import_attendance_tab">
                            @include('essentials::attendance.import_attendance')
                        </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    
</section>
<!-- /.content -->
<div class="modal fade" id="attendance_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="edit_attendance_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="user_shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="edit_shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade" id="shift_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    @include('essentials::attendance.shift_modal')
</div>

@endsection

@section('javascript')
    <!-- FullCalendar JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
    
    <script type="text/javascript">
        $(document).ready(function() {
            attendance_table = $('#attendance_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'index'])}}",
                    "data" : function(d) {
                        if ($('#employee_id').length) {
                            d.employee_id = $('#employee_id').val();
                        }
                        if($('#date_range').val()) {
                            var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    }
                },
                columns: [
                    { data: 'date', name: 'clock_in_time' },
                    { data: 'user', name: 'user' },
                    { data: 'department', name: 'department' },
                    { data: 'location', name: 'location' },
                    
                    
                    { data: 'clock_in', name: 'clock_in', orderable: false, searchable: false},
                    { data: 'clock_out', name: 'clock_out', orderable: false, searchable: false},
                    { data: 'work_duration', name: 'work_duration', orderable: false, searchable: false},
                    { data: 'ip_address', name: 'ip_address'},
                    { data: 'shift_name', name: 'es.name'},
                    @can('essentials.crud_all_attendance')
                        { data: 'action', name: 'action', orderable: false, searchable: false},
                    @endcan
                ],
            });

            $('#date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#date_range').val('');
                attendance_table.ajax.reload();
            });

            $(document).on('change', '#employee_id, #date_range', function() {
                attendance_table.ajax.reload();
            });
            
            // Export functions
            window.exportToExcel = function() {
                var employeeId = $('#employee_id').val();
                var startDate = '';
                var endDate = '';
                
                // Use the same date parsing logic as the DataTable
                if($('#date_range').val()) {
                    var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    startDate = start;
                    endDate = end;
                }
                
                var url = '{{ route("attendance.export") }}?format=excel';
                if (employeeId) url += '&employee_id=' + employeeId;
                if (startDate) url += '&start_date=' + startDate;
                if (endDate) url += '&end_date=' + endDate;
                
                console.log('Export URL:', url); // Debug log
                window.open(url, '_blank');
            };
            
            window.exportToCSV = function() {
                var employeeId = $('#employee_id').val();
                var startDate = '';
                var endDate = '';
                
                // Use the same date parsing logic as the DataTable
                if($('#date_range').val()) {
                    var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    startDate = start;
                    endDate = end;
                }
                
                var url = '{{ route("attendance.export") }}?format=csv';
                if (employeeId) url += '&employee_id=' + employeeId;
                if (startDate) url += '&start_date=' + startDate;
                if (endDate) url += '&end_date=' + endDate;
                
                console.log('Export URL:', url); // Debug log
                window.open(url, '_blank');
            };
            
            window.testExport = function() {
                var url = '{{ route("attendance.test.export") }}';
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        console.log('Test export response:', response);
                        alert('Test successful! Check console for details. URL: ' + response.url);
                    },
                    error: function(xhr, status, error) {
                        console.error('Test export error:', error);
                        alert('Test failed: ' + error);
                    }
                });
            };

            $(document).on('submit', 'form#attendance_form', function(e) {
                e.preventDefault();
                if($(this).valid()) {
                    $(this).find('button[type="submit"]').attr('disabled', true);
                    var data = $(this).serialize();
                    $.ajax({
                        method: $(this).attr('method'),
                        url: $(this).attr('action'),
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                $('div#attendance_modal').modal('hide');
                                $('div#edit_attendance_modal').modal('hide');
                                toastr.success(result.msg);
                                attendance_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });

            $(document).on( 'change', '#employee_id, #date_range', function() {
                get_attendance_summary();
            });

            @if(!auth()->user()->can('essentials.crud_all_attendance'))
                get_attendance_summary();
            @endif

            shift_table = $('#shift_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action([\Modules\Essentials\Http\Controllers\ShiftController::class, 'index'])}}",
                },
                columnDefs: [
                    {
                        targets: 4,
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'type', name: 'type' },
                    { data: 'start_time', name: 'start_time'},
                    { data: 'end_time', name: 'end_time' },
                    { data: 'holidays', name: 'holidays'},
                    { data: 'action', name: 'action' },
                ],
            });

            $('#shift_modal, #edit_shift_modal').on('shown.bs.modal', function(e) {
                $('form#add_shift_form').validate();
                $('#shift_modal #start_time, #shift_modal #end_time, #edit_shift_modal #start_time, #edit_shift_modal #end_time').datetimepicker({
                    format: moment_time_format,
                    ignoreReadonly: true,
                });
                $('#shift_modal .select2, #edit_shift_modal .select2').select2();

                if ($('select#shift_type').val() == 'fixed_shift') {
                    $('div.time_div').show();
                } else if ($('select#shift_type').val() == 'flexible_shift') {
                    $('div.time_div').hide();
                }

                $('select#shift_type').change(function() {
                    var shift_type = $(this).val();
                    if (shift_type == 'fixed_shift') {
                        $('div.time_div').fadeIn();
                    } else if (shift_type == 'flexible_shift') {
                        $('div.time_div').fadeOut();
                    }
                });

                //toggle auto clockout
                if($('#is_allowed_auto_clockout').is(':checked')) {
                    $("div.enable_auto_clock_out_time").show();
                } else {
                    $("div.enable_auto_clock_out_time").hide(); 
                }

                $('#is_allowed_auto_clockout').on('change', function(){
                    if ($(this).is(':checked')) {
                        $("div.enable_auto_clock_out_time").show();
                    } else {
                       $("div.enable_auto_clock_out_time").hide(); 
                    }
                });
                
                $('#shift_modal #auto_clockout_time, #edit_shift_modal #auto_clockout_time').datetimepicker({
                    format: moment_time_format,
                    stepping: 30,
                    ignoreReadonly: true,
                });
            });
            $('#shift_modal, #edit_shift_modal').on('hidden.bs.modal', function(e) {
                $('#shift_modal #start_time').data("DateTimePicker").destroy();
                $('#shift_modal #end_time').data("DateTimePicker").destroy();
                $('#add_shift_form')[0].reset();
                $('#add_shift_form').find('button[type="submit"]').attr('disabled', false);

                $('#is_allowed_auto_clockout').attr('checked', false);
                $('#auto_clockout_time').data("DateTimePicker").destroy();
                $("div.enable_auto_clock_out_time").hide(); 
            });
            $('#user_shift_modal').on('shown.bs.modal', function(e) {
                $('#user_shift_modal').find('.date_picker').each( function(){
                    $(this).datetimepicker({
                        format: moment_date_format,
                        ignoreReadonly: true,
                    });
                });
            });

            @can('essentials.crud_all_attendance')
                get_attendance_by_shift();
                $('#attendance_by_shift_date_filter').datetimepicker({
                    format: moment_date_format,
                    ignoreReadonly: true,
                });
                var attendanceDateRangeSettings = dateRangeSettings;
                attendanceDateRangeSettings.startDate = moment().subtract(6, 'days');
                attendanceDateRangeSettings.endDate = moment();
                $('#attendance_by_date_filter').daterangepicker(
                    dateRangeSettings,
                    function (start, end) {
                        $('#attendance_by_date_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                    }
                );
                get_attendance_by_date();
                $(document).on('change', '#attendance_by_date_filter', function(){
                    get_attendance_by_date();
                });
            @endcan

            $('a[href="#attendance_tab"]').click(function(){
                attendance_table.ajax.reload();
            });
            $('a[href="#attendance_by_shift_tab"]').click(function(){
                get_attendance_by_shift();
            });
            $('a[href="#attendance_by_date_tab"]').click(function(){
                get_attendance_by_date();
            });
            $('a[href="#calendar_view_tab"]').click(function(){
                setTimeout(function() {
                    initializeCalendarView();
                    // Initialize select2 for calendar employee filter with modern styling
                    $('#calendar_employee_filter').select2({
                        placeholder: "🌟 Select Employee",
                        allowClear: true,
                        theme: 'default',
                        width: '100%'
                    });
                    
                    // Add custom button handlers
                    $('#calendar_refresh').off('click').on('click', function() {
                        if ($('#attendance_calendar').hasClass('fc')) {
                            var btn = $(this);
                            btn.prop('disabled', true);
                            btn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
                            
                            $('#attendance_calendar').fullCalendar('refetchEvents');
                            
                            setTimeout(function() {
                                btn.prop('disabled', false);
                                btn.html('<i class="fas fa-sync-alt"></i> Refresh');
                                toastr.success('Calendar refreshed successfully!');
                            }, 1000);
                        }
                    });
                    
                    $('#calendar_today').off('click').on('click', function() {
                        if ($('#attendance_calendar').hasClass('fc')) {
                            $('#attendance_calendar').fullCalendar('today');
                        }
                    });
                }, 100);
            });
        });

        $(document).on('click', 'button.delete-attendance', function() {
            swal({
                title: LANG.sure,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then(willDelete => {
                if (willDelete) {
                    var href = $(this).data('href');
                    var data = $(this).serialize();
                    $.ajax({
                        method: 'DELETE',
                        url: href,
                        dataType: 'json',
                        data: data,
                        success: function(result) {
                            if (result.success == true) {
                                toastr.success(result.msg);
                                attendance_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });
        $('#edit_attendance_modal').on('hidden.bs.modal', function(e) {
            $('#edit_attendance_modal #clock_in_time').data("DateTimePicker").destroy();
            $('#edit_attendance_modal #clock_out_time').data("DateTimePicker").destroy();
        });

        $('#attendance_modal').on('shown.bs.modal', function(e) {
            $('#attendance_modal .select2').select2();
        });
        $('#edit_attendance_modal').on('shown.bs.modal', function(e) {
            $('#edit_attendance_modal .select2').select2();
            $('#edit_attendance_modal #clock_in_time, #edit_attendance_modal #clock_out_time').datetimepicker({
                format: moment_date_format + ' ' + moment_time_format,
                ignoreReadonly: true,
            });

            validate_clockin_clock_out = {
                url: '/hrm/validate-clock-in-clock-out',
                type: 'post',
                data: {
                    user_ids: function() {
                        return $('#employees').val();
                    },
                    clock_in_time: function() {
                        return $('#clock_in_time').val();
                    },
                    clock_out_time: function() {
                        return $('#clock_out_time').val();
                    },
                    attendance_id: function() {
                        if($('form#attendance_form #attendance_id').length) {
                           return $('form#attendance_form #attendance_id').val();
                        } else {
                            return '';
                        }
                    },
                },
            };

            $('form#attendance_form').validate({
                rules: {
                    clock_in_time: {
                        remote: validate_clockin_clock_out,
                    },
                    clock_out_time: {
                        remote: validate_clockin_clock_out,
                    },
                },
                messages: {
                    clock_in_time: {
                        remote: "{{__('essentials::lang.clock_in_clock_out_validation_msg')}}",
                    },
                    clock_out_time: {
                        remote: "{{__('essentials::lang.clock_in_clock_out_validation_msg')}}",
                    },
                },
            });
        });

        function get_attendance_summary() {
            $('#user_attendance_summary').addClass('hide');
            var user_id = $('#employee_id').length ? $('#employee_id').val() : '';
            
            var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $.ajax({
                url: '{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'getUserAttendanceSummary'])}}?user_id=' + user_id + '&start_date=' + start + '&end_date=' + end ,
                dataType: 'html',
                success: function(response) {
                    $('#total_work_hours').html(response);
                    $('#user_attendance_summary').removeClass('hide');
                },
            });
        }

    //Set mindate for clockout time greater than clockin time
    $('#attendance_modal').on('dp.change', '#clock_in_time', function(){
        if ($('#clock_out_time').data("DateTimePicker")) {
            $('#clock_out_time').data("DateTimePicker").options({minDate: $(this).data("DateTimePicker").date()});
            $('#clock_out_time').data("DateTimePicker").clear();
        }
    });

    $(document).on('submit', 'form#add_shift_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    if ($('div#edit_shift_modal').hasClass('in')) {
                        $('div#edit_shift_modal').modal("hide");
                    } else if ($('div#shift_modal').hasClass('in')) {
                        $('div#shift_modal').modal('hide');    
                    }
                    toastr.success(result.msg);
                    shift_table.ajax.reload();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });

    $(document).on('submit', 'form#add_user_shift_form', function(e) {
        e.preventDefault();
        $(this).find('button[type="submit"]').attr('disabled', true);
        var data = $(this).serialize();

        $.ajax({
            method: $(this).attr('method'),
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success == true) {
                    $('div#user_shift_modal').modal('hide');
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
                $('form#add_user_shift_form').find('button[type="submit"]').attr('disabled', false);
            },
        });
    });

    function get_attendance_by_shift() {
        data = {date: $('#attendance_by_shift_date_filter').val()};
        $.ajax({
            url: "{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'getAttendanceByShift'])}}",
            data: data,
            dataType: 'html',
            success: function(result) {
                $('table#attendance_by_shift_table tbody').html(result);
            },
        });
    }
    function get_attendance_by_date() {
        data = {
                start_date: $('#attendance_by_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                end_date: $('#attendance_by_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD')
            };
        $.ajax({
            url: "{{action([\Modules\Essentials\Http\Controllers\AttendanceController::class, 'getAttendanceByDate'])}}",
            data: data,
            dataType: 'html',
            success: function(result) {
                $('table#attendance_by_date_table tbody').html(result);
            },
        });
    }
    $(document).on('dp.change', '#attendance_by_shift_date_filter', function(){
        get_attendance_by_shift();
    });
    $(document).on('change', '#select_employee', function(e) {
        var user_id = $(this).val();
        var count = 0;
        $('table#employee_attendance_table tbody').find('tr').each( function(){
            if ($(this).data('user_id') == user_id) {
                count++;
            }
        });
        
        if (user_id && count == 0) {
            $.ajax({
                url: "/hrm/get-attendance-row/" + user_id,
                dataType: 'html',
                success: function(result) {
                    $('table#employee_attendance_table tbody').append(result);
                    var tr = $('table#employee_attendance_table tbody tr:last');

                    tr.find('.date_time_picker').each( function(){
                        $(this).datetimepicker({
                            format: moment_date_format + ' ' + moment_time_format,
                            ignoreReadonly: true,
                            maxDate: moment(),
                            widgetPositioning: {
                                horizontal: 'auto',
                                vertical: 'bottom'
                             }
                        });
                        $(this).val('');
                    });
                    $('#select_employee').val('').change();
                },
            });
        }
    });
    $(document).on('click', 'button.remove_attendance_row', function(e) {
        $(this).closest('tr').remove();
    });

    // Calendar View Functions
    function initializeCalendarView() {
        console.log('Initializing calendar view...');
        if ($('#attendance_calendar').length && !$('#attendance_calendar').hasClass('fc')) {
            console.log('Calendar container found, initializing FullCalendar...');
            $('#attendance_calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'month',
                editable: false,
                loading: function(bool) {
                    if (bool) {
                        $('#calendar_loading').show();
                        $('#attendance_calendar').css('opacity', '0.5');
                    } else {
                        $('#calendar_loading').hide();
                        $('#attendance_calendar').css('opacity', '1');
                    }
                },
                eventSources: [{
                    url: '{{ action([\Modules\Essentials\Http\Controllers\AttendanceController::class, "getCalendarData"]) }}',
                    data: function() {
                        var employee_id = $('#calendar_employee_filter').val();
                        // For normal users without crud_all_attendance permission, force to their own ID
                        @cannot('essentials.crud_all_attendance')
                        employee_id = {{ auth()->id() }};
                        @endcannot
                        return {
                            employee_id: employee_id,
                            _token: '{{ csrf_token() }}'
                        };
                    },
                    success: function(data) {
                        console.log('Calendar data received:', data);
                        if (data.length === 0) {
                            toastr.info('No attendance data found for the selected period');
                        } else {
                            console.log('Total events loaded:', data.length);
                            // Count different event types
                            var eventTypes = {};
                            data.forEach(function(event) {
                                var type = event.extendedProps ? event.extendedProps.type : 'unknown';
                                eventTypes[type] = (eventTypes[type] || 0) + 1;
                                
                                // Log leave events specifically
                                if (type === 'leave') {
                                    console.log('Leave event found:', event);
                                }
                            });
                            console.log('Event types:', eventTypes);
                            
                            if (eventTypes.leave) {
                                toastr.success('Found ' + eventTypes.leave + ' leave events!');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Calendar data fetch error:', error);
                        toastr.error('Failed to load calendar data. Please try again.');
                        $('#calendar_loading').hide();
                    }
                }],
                eventRender: function(event, element) {
                    // Add modern tooltip styling
                    var tooltip = '<div class="modern-tooltip">';
                    
                    if (event.extendedProps && event.extendedProps.employee_name) {
                        tooltip += '<div class="tooltip-header"><i class="fas fa-user-circle"></i> ' + event.extendedProps.employee_name + '</div>';
                        tooltip += '<div class="tooltip-date"><i class="fas fa-calendar"></i> ' + moment(event.start).format('dddd, DD MMM YYYY') + '</div>';
                        
                        if (event.extendedProps.type === 'attendance') {
                            tooltip += '<div class="tooltip-status success"><i class="fas fa-check-circle"></i> Present</div>';
                            if (event.extendedProps.clock_in) {
                                tooltip += '<div class="tooltip-time"><i class="fas fa-sign-in-alt"></i> In: ' + event.extendedProps.clock_in + '</div>';
                            }
                            if (event.extendedProps.clock_out) {
                                tooltip += '<div class="tooltip-time"><i class="fas fa-sign-out-alt"></i> Out: ' + event.extendedProps.clock_out + '</div>';
                            }
                            if (event.extendedProps.duration) {
                                tooltip += '<div class="tooltip-duration"><i class="fas fa-hourglass-half"></i> ' + event.extendedProps.duration + '</div>';
                            }
                        } else if (event.extendedProps.type === 'leave') {
                            var leaveIcon = 'fas fa-calendar-check';
                            var statusClass = 'success';
                            if (event.extendedProps.status === 'pending') {
                                leaveIcon = 'fas fa-calendar-times';
                                statusClass = 'warning';
                            } else if (event.extendedProps.status === 'rejected') {
                                leaveIcon = 'fas fa-calendar-minus';
                                statusClass = 'danger';
                            }
                            
                            tooltip += '<div class="tooltip-status ' + statusClass + '"><i class="' + leaveIcon + '"></i> ' + event.extendedProps.leave_type + ' Leave</div>';
                            tooltip += '<div class="tooltip-leave-info"><strong>Status:</strong> ' + event.extendedProps.status.charAt(0).toUpperCase() + event.extendedProps.status.slice(1) + '</div>';
                            tooltip += '<div class="tooltip-leave-info"><strong>Type:</strong> ' + (event.extendedProps.is_paid == 1 ? 'Paid' : 'Unpaid') + '</div>';
                            if (event.extendedProps.leave_days) {
                                tooltip += '<div class="tooltip-leave-info"><strong>Days:</strong> ' + event.extendedProps.leave_days + '</div>';
                            }
                        } else if (event.extendedProps.type === 'holiday') {
                            tooltip += '<div class="tooltip-status info"><i class="fas fa-gift"></i> Holiday</div>';
                        } else if (event.extendedProps.type === 'sunday') {
                            tooltip += '<div class="tooltip-status sunday"><i class="fas fa-sun"></i> Sunday</div>';
                        } else if (event.extendedProps.type === 'absent') {
                            tooltip += '<div class="tooltip-status danger"><i class="fas fa-times-circle"></i> Absent</div>';
                        }
                    }
                    
                    tooltip += '</div>';
                    
                    element.attr('title', '');
                    element.popover({
                        content: tooltip,
                        trigger: 'hover',
                        placement: 'top',
                        container: 'body',
                        html: true,
                        template: '<div class="popover modern-popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>'
                    });
                    
                    // Add hover effect
                    element.hover(
                        function() { $(this).addClass('fc-event-hover'); },
                        function() { $(this).removeClass('fc-event-hover'); }
                    );
                },
                eventClick: function(event, jsEvent, view) {
                    if (event.extendedProps) {
                        showAttendanceDetails(event);
                    }
                },
                viewRender: function(view, element) {
                    // Load month summary when view changes
                    loadMonthSummary(view.start, view.end);
                }
            });
        }
        if ($('#attendance_calendar').hasClass('fc')) {
            $('#attendance_calendar').fullCalendar('refetchEvents');
            // Load summary for initial view
            var calendar = $('#attendance_calendar');
            if (calendar.hasClass('fc')) {
                var view = calendar.fullCalendar('getView');
                if (view) {
                    loadMonthSummary(view.start, view.end);
                }
            }
        }
    }

    // Function to load and display month summary
    function loadMonthSummary(start, end) {
        var employee_id = $('#calendar_employee_filter').val();
        // For normal users without crud_all_attendance permission, force to their own ID
        @cannot('essentials.crud_all_attendance')
        employee_id = {{ auth()->id() }};
        @endcannot
        
        $.ajax({
            url: '{{ action([\Modules\Essentials\Http\Controllers\AttendanceController::class, "getMonthSummary"]) }}',
            method: 'GET',
            data: {
                start: moment(start).format('YYYY-MM-DD'),
                end: moment(end).format('YYYY-MM-DD'),
                employee_id: employee_id,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    displayMonthSummary(response.summary, response.month);
                } else {
                    console.error('Error loading summary:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading month summary:', error);
            }
        });
    }

    // Function to display month summary in Attendance Details section
    function displayMonthSummary(summary, month) {
        var summaryHtml = '<div class="attendance-detail-item">';
        summaryHtml += '<h5><i class="fas fa-chart-bar"></i> Month Summary - ' + month + '</h5>';
        
        summaryHtml += '<div class="detail-row">';
        summaryHtml += '<strong><i class="fas fa-check-circle" style="color: #10b981;"></i> Total Present Days:</strong>';
        summaryHtml += '<span style="color: #059669; font-weight: 600; font-size: 16px;">' + summary.total_present + '</span>';
        summaryHtml += '</div>';
        
        summaryHtml += '<div class="detail-row">';
        summaryHtml += '<strong><i class="fas fa-times-circle" style="color: #ef4444;"></i> Total Absent Days:</strong>';
        summaryHtml += '<span style="color: #dc2626; font-weight: 600; font-size: 16px;">' + summary.total_absent + '</span>';
        summaryHtml += '</div>';
        
        summaryHtml += '<div class="detail-row">';
        summaryHtml += '<strong><i class="fas fa-clock" style="color: #06b6d4;"></i> Total Half Days:</strong>';
        summaryHtml += '<span style="color: #0891b2; font-weight: 600; font-size: 16px;">' + summary.total_half_day + '</span>';
        summaryHtml += '</div>';
        
        summaryHtml += '<div class="detail-row" style="border-top: 2px solid #e2e8f0; margin-top: 10px; padding-top: 10px;">';
        summaryHtml += '<strong><i class="fas fa-calendar-check" style="color: #f59e0b;"></i> Total Leave Days:</strong>';
        summaryHtml += '<span style="color: #d97706; font-weight: 600; font-size: 16px;">' + summary.total_leave + '</span>';
        summaryHtml += '</div>';
        
        if (summary.total_leave > 0) {
            summaryHtml += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f1f5f9;">';
            summaryHtml += '<strong style="color: #374151; font-size: 14px;">Leave Breakdown:</strong>';
            summaryHtml += '<div style="margin-top: 10px;">';
            
            if (summary.pl_leave > 0) {
                summaryHtml += '<div class="detail-row" style="padding: 5px 0;">';
                summaryHtml += '<strong style="min-width: 100px;">PL (Privilege Leave):</strong>';
                summaryHtml += '<span style="color: #667eea; font-weight: 600;">' + summary.pl_leave + ' day(s)</span>';
                summaryHtml += '</div>';
            }
            
            if (summary.cl_leave > 0) {
                summaryHtml += '<div class="detail-row" style="padding: 5px 0;">';
                summaryHtml += '<strong style="min-width: 100px;">CL (Casual Leave):</strong>';
                summaryHtml += '<span style="color: #667eea; font-weight: 600;">' + summary.cl_leave + ' day(s)</span>';
                summaryHtml += '</div>';
            }
            
            if (summary.sick_leave > 0) {
                summaryHtml += '<div class="detail-row" style="padding: 5px 0;">';
                summaryHtml += '<strong style="min-width: 100px;">Sick Leave:</strong>';
                summaryHtml += '<span style="color: #667eea; font-weight: 600;">' + summary.sick_leave + ' day(s)</span>';
                summaryHtml += '</div>';
            }
            
            if (summary.other_leave > 0) {
                summaryHtml += '<div class="detail-row" style="padding: 5px 0;">';
                summaryHtml += '<strong style="min-width: 100px;">Other Leave:</strong>';
                summaryHtml += '<span style="color: #667eea; font-weight: 600;">' + summary.other_leave + ' day(s)</span>';
                summaryHtml += '</div>';
            }
            
            summaryHtml += '</div>';
            summaryHtml += '</div>';
        }
        
        summaryHtml += '</div>';
        
        // Update the attendance details section
        $('#attendance_details').html(summaryHtml);
    }

    $(document).on('change', '#calendar_employee_filter', function() {
        if ($('#attendance_calendar').hasClass('fc')) {
            $('#attendance_calendar').fullCalendar('refetchEvents');
            // Reload summary when employee filter changes
            var calendar = $('#attendance_calendar');
            if (calendar.hasClass('fc')) {
                var view = calendar.fullCalendar('getView');
                if (view) {
                    loadMonthSummary(view.start, view.end);
                }
            }
        }
    });

    // Add CSRF token to AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Enhanced show attendance details function
    function showAttendanceDetails(event) {
        var details = '<div class="attendance-detail-item">';
        details += '<h5><i class="fas fa-user-circle"></i> ' + event.extendedProps.employee_name + '</h5>';
        details += '<div class="detail-row"><strong><i class="fas fa-calendar"></i> Date:</strong> <span>' + moment(event.start).format('dddd, DD MMMM YYYY') + '</span></div>';
        
        if (event.extendedProps.type === 'attendance') {
            details += '<div class="detail-row"><strong><i class="fas fa-check-circle"></i> Status:</strong> <span class="label label-success"><i class="fas fa-user-check"></i> Present</span></div>';
            if (event.extendedProps.clock_in) {
                details += '<div class="detail-row"><strong><i class="fas fa-sign-in-alt"></i> Clock In:</strong> <span style="color: #059669; font-weight: 600;">' + event.extendedProps.clock_in + '</span></div>';
            }
            if (event.extendedProps.clock_out) {
                details += '<div class="detail-row"><strong><i class="fas fa-sign-out-alt"></i> Clock Out:</strong> <span style="color: #dc2626; font-weight: 600;">' + event.extendedProps.clock_out + '</span></div>';
            }
            if (event.extendedProps.duration) {
                details += '<div class="detail-row"><strong><i class="fas fa-hourglass-half"></i> Duration:</strong> <span style="color: #667eea; font-weight: 600;">' + event.extendedProps.duration + '</span></div>';
            }
            if (event.extendedProps.shift_name) {
                details += '<div class="detail-row"><strong><i class="fas fa-clock"></i> Shift:</strong> <span>' + event.extendedProps.shift_name + '</span></div>';
            }
            if (event.extendedProps.ip_address) {
                details += '<div class="detail-row"><strong><i class="fas fa-globe"></i> IP Address:</strong> <span>' + event.extendedProps.ip_address + '</span></div>';
            }
        } else if (event.extendedProps.type === 'leave') {
            // Determine status label class
            var statusLabelClass = 'label-success';
            var statusIcon = 'fas fa-check-circle';
            var statusText = 'Approved';
            
            if (event.extendedProps.status === 'pending') {
                statusLabelClass = 'label-warning';
                statusIcon = 'fas fa-clock';
                statusText = 'Pending Approval';
            } else if (event.extendedProps.status === 'rejected') {
                statusLabelClass = 'label-danger';
                statusIcon = 'fas fa-times-circle';
                statusText = 'Rejected';
            }
            
            details += '<div class="detail-row"><strong><i class="fas fa-calendar-times"></i> Status:</strong> <span class="label ' + statusLabelClass + '"><i class="' + statusIcon + '"></i> ' + statusText + '</span></div>';
            
            if (event.extendedProps.leave_type) {
                details += '<div class="detail-row"><strong><i class="fas fa-list"></i> Leave Type:</strong> <span style="color: #667eea; font-weight: 600;">' + event.extendedProps.leave_type.toUpperCase() + '</span></div>';
            }
            
            if (event.extendedProps.is_paid !== undefined) {
                var paidStatus = event.extendedProps.is_paid == 1 ? 'Paid Leave' : 'Unpaid Leave';
                var paidColor = event.extendedProps.is_paid == 1 ? '#059669' : '#dc2626';
                var paidIcon = event.extendedProps.is_paid == 1 ? 'fas fa-money-bill-wave' : 'fas fa-ban';
                details += '<div class="detail-row"><strong><i class="fas fa-dollar-sign"></i> Payment:</strong> <span style="color: ' + paidColor + '; font-weight: 600;"><i class="' + paidIcon + '"></i> ' + paidStatus + '</span></div>';
            }
            
            if (event.extendedProps.leave_days) {
                details += '<div class="detail-row"><strong><i class="fas fa-calendar-day"></i> Duration:</strong> <span style="color: #f59e0b; font-weight: 600;">' + event.extendedProps.leave_days + ' day(s)</span></div>';
            }
            
            if (event.extendedProps.reason) {
                details += '<div class="detail-row"><strong><i class="fas fa-comment"></i> Reason:</strong> <span style="font-style: italic; color: #6b7280;">' + event.extendedProps.reason + '</span></div>';
            }
            
            if (event.extendedProps.max_leave_count) {
                details += '<div class="detail-row"><strong><i class="fas fa-chart-bar"></i> Max Allowed:</strong> <span style="color: #8b5cf6; font-weight: 600;">' + event.extendedProps.max_leave_count + ' days/year</span></div>';
            }
        } else if (event.extendedProps.type === 'holiday') {
            details += '<div class="detail-row"><strong><i class="fas fa-gift"></i> Status:</strong> <span class="label label-default"><i class="fas fa-calendar-day"></i> Holiday</span></div>';
            details += '<div class="detail-row"><strong><i class="fas fa-star"></i> Holiday:</strong> <span>' + event.title.replace(' (Holiday)', '') + '</span></div>';
        } else if (event.extendedProps.type === 'sunday') {
            details += '<div class="detail-row"><strong><i class="fas fa-sun"></i> Status:</strong> <span class="label label-sunday"><i class="fas fa-calendar-week"></i> Sunday</span></div>';
            details += '<div class="detail-row"><strong><i class="fas fa-info-circle"></i> Note:</strong> <span>Weekly off day</span></div>';
        } else if (event.extendedProps.type === 'absent') {
            details += '<div class="detail-row"><strong><i class="fas fa-times-circle"></i> Status:</strong> <span class="label label-danger"><i class="fas fa-user-slash"></i> Absent</span></div>';
            details += '<div class="detail-row"><strong><i class="fas fa-exclamation-triangle"></i> Note:</strong> <span>No attendance record found for this day</span></div>';
        }
        
        details += '</div>';
        
        // Add animation effect
        $('#attendance_details').fadeOut(200, function() {
            $(this).html(details).fadeIn(300);
        });
    }

</script>
@endsection
