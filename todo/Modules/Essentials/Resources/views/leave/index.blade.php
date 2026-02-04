@extends('layouts.app')
@section('title', __('essentials::lang.leave'))

@section('content')
@include('essentials::layouts.nav_hrm')
<section class="content-header">
    <h1>@lang('essentials::lang.leave')
    </h1>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
        @component('components.filters', ['title' => __('report.filters'), 'class' => 'box-solid'])
            @if(!empty($users))
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('user_id_filter', __('essentials::lang.employee') . ':') !!}
                    {!! Form::select('user_id_filter', $users, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endif
            <div class="col-md-3">
                <div class="form-group">
                    <label for="status_filter">@lang( 'sale.status' ):</label>
                    <select class="form-control select2" name="status_filter" required id="status_filter" style="width: 100%;">
                        <option value="">@lang('lang_v1.all')</option>
                        @foreach($leave_statuses as $key => $value)
                            <option value="{{$key}}">{{$value['name']}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_type_filter', __('essentials::lang.leave_type') . ':') !!}
                    {!! Form::select('leave_type_filter', $leave_types, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('leave_filter_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('leave_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
        @endcomponent
        </div>
    </div>
    <!-- Quick Leave Balance Widget -->
    <div class="row" id="quick_leave_balance_container">
        @if(!empty($quick_balance) && count($quick_balance) > 0)
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-info', 'title' => '<i class="fa fa-calendar-check"></i> ' . __('essentials::lang.leave_remaining') . ' - <span id="balance_user_name">' . auth()->user()->user_full_name . '</span>'])
                <div class="row">
                    @foreach($quick_balance as $balance)
                    <div class="col-md-3 col-sm-6">
                        <div class="info-box" style="margin-bottom: 10px;">
                            <span class="info-box-icon" 
                                  style="background-color: 
                                  @if($balance['remaining'] === 'Unlimited') 
                                      #4caf50
                                  @elseif(is_numeric($balance['remaining']) && $balance['remaining'] > 5) 
                                      #4caf50
                                  @elseif(is_numeric($balance['remaining']) && $balance['remaining'] > 0) 
                                      #ff9800
                                  @else 
                                      #f44336
                                  @endif
                                  ;">
                                <i class="fa fa-calendar-minus"></i>
                            </span>
                            <div class="info-box-content">
                                <span class="info-box-text">{{$balance['leave_type']}}</span>
                                <span class="info-box-number" style="font-size: 16px;">
                                    @if($balance['remaining'] === 'Unlimited')
                                        <span style="color: #4caf50;">
                                            <i class="fa fa-infinity"></i> Unlimited
                                        </span>
                                    @else
                                        <span style="color: 
                                        @if($balance['remaining'] > 5) 
                                            #4caf50
                                        @elseif($balance['remaining'] > 0) 
                                            #ff9800
                                        @else 
                                            #f44336
                                        @endif
                                        ; font-weight: bold;">
                                            {{$balance['remaining']}} @lang('lang_v1.days')
                                        </span>
                                    @endif
                                </span>
                                <div class="progress" style="height: 4px; margin-top: 5px;">
                                    @if($balance['max_allowed'] !== 'Unlimited' && is_numeric($balance['max_allowed']))
                                        @php
                                            $percentage = $balance['max_allowed'] > 0 ? (($balance['remaining'] / $balance['max_allowed']) * 100) : 0;
                                        @endphp
                                        <div class="progress-bar" 
                                             style="width: {{$percentage}}%; 
                                             background-color: 
                                             @if($percentage > 50) 
                                                 #4caf50
                                             @elseif($percentage > 20) 
                                                 #ff9800
                                             @else 
                                                 #f44336
                                             @endif
                                             ;"></div>
                                    @else
                                        <div class="progress-bar" style="width: 100%; background-color: #4caf50;"></div>
                                    @endif
                                </div>
                                <span class="progress-description" style="font-size: 11px; color: #666;">
                                    Used: {{$balance['used']}} / 
                                    @if($balance['max_allowed'] === 'Unlimited')
                                        ∞
                                    @else
                                        {{$balance['max_allowed']}}
                                    @endif
                                    @lang('lang_v1.days')
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endcomponent
        </div>
        @endif
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-solid', 'title' => __( 'essentials::lang.all_leaves' )])
                @slot('tool')
                    <div class="box-tools">
                        <button type="button" class="btn btn-block btn-primary btn-modal" data-href="{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'create'])}}" data-container="#add_leave_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                    </div>
                @endslot
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="leave_table">
                        <thead>
                            <tr>
                                <th>@lang( 'purchase.ref_no' )</th>
                                <th>@lang( 'essentials::lang.leave_type' )</th>
                                <th>@lang('essentials::lang.employee')</th>
                                <th>@lang( 'lang_v1.date' )</th>
                                <th>@lang( 'essentials::lang.reason' )</th>
                                <th>@lang( 'sale.status' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
    <div class="row" id="user_leave_summary"></div>
</section>
<!-- /.content -->
<div class="modal fade" id="add_leave_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel"></div>

@include('essentials::leave.change_status_modal')

@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            leaves_table = $('#leave_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'index'])}}",
                    "data" : function(d) {
                        if ($('#user_id_filter').length) {
                            d.user_id = $('#user_id_filter').val();
                        }
                        d.status = $('#status_filter').val();
                        d.leave_type = $('#leave_type_filter').val();
                        if($('#leave_filter_date_range').val()) {
                            var start = $('#leave_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                            var end = $('#leave_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                            d.start_date = start;
                            d.end_date = end;
                        }
                    }
                },
                columnDefs: [
                    {
                        targets: 6,
                        orderable: false,
                        searchable: false,
                    },
                ],
                columns: [
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'leave_type', name: 'lt.leave_type' },
                    { data: 'user', name: 'user' },
                    { data: 'start_date', name: 'start_date'},
                    { data: 'reason', name: 'essentials_leaves.reason'},
                    { data: 'status', name: 'essentials_leaves.status'},
                    { data: 'action', name: 'action' },
                ],
            });

            $('#leave_filter_date_range').daterangepicker(
                dateRangeSettings,
                function (start, end) {
                    $('#leave_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
                }
            );
            $('#leave_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#leave_filter_date_range').val('');
                leaves_table.ajax.reload();
            });

            $(document).on( 'change', '#user_id_filter, #status_filter, #leave_filter_date_range, #leave_type_filter', function() {
                leaves_table.ajax.reload();
            });

            $('#add_leave_modal').on('shown.bs.modal', function(e) {
                $('#add_leave_modal .select2').select2();

                $('form#add_leave_form #start_date, form#add_leave_form #end_date').datepicker({
                    autoclose: true,
                });
                
                // Reset quota status when modal opens
                $('#quota_status_container').hide();
            });

            $(document).on('submit', 'form#add_leave_form', function(e) {
                e.preventDefault();
                $(this).find('button[type="submit"]').attr('disabled', true);
                var data = $(this).serialize();
                var ladda = Ladda.create(document.querySelector('.add-leave-btn'));
                ladda.start();
                $.ajax({
                    method: $(this).attr('method'),
                    url: $(this).attr('action'),
                    dataType: 'json',
                    data: data,
                    success: function(result) {
                        ladda.stop();
                        $(this).find('button[type="submit"]').attr('disabled', false);
                        
                        if (result.success == true) {
                            $('div#add_leave_modal').modal('hide');
                            toastr.success(result.msg);
                            leaves_table.ajax.reload();
                            // Refresh leave balance
                            update_quick_balance();
                            get_leave_summary();
                        } else {
                            toastr.error(result.msg);
                            
                            // Show error in quota status if it's quota related
                            if (result.msg.includes('quota') || result.msg.includes('remaining')) {
                                $('#quota_status_container').show();
                                $('#quota_status_alert').removeClass('alert-info alert-success alert-warning').addClass('alert-danger');
                                $('#quota_status_text').html('<i class="fa fa-exclamation-triangle"></i> ' + result.msg);
                            }
                        }
                    },
                    error: function(xhr) {
                        ladda.stop();
                        $(this).find('button[type="submit"]').attr('disabled', false);
                        toastr.error('Something went wrong. Please try again.');
                    }
                });
            });
            $(document).on( 'change', '#user_id_filter, #leave_filter_date_range', function() {
                get_leave_summary();
                update_quick_balance();
            });

            @if(!auth()->user()->can('essentials.crud_all_leave'))
                get_leave_summary();
            @else
                // For admin/subadmin, update quick balance on page load
                update_quick_balance();
            @endif
        });

        $(document).on('click', 'a.change_status', function(e) {
            e.preventDefault();
            $('#change_status_modal').find('select#status_dropdown').val($(this).data('orig-value')).change();
            $('#change_status_modal').find('#leave_id').val($(this).data('leave-id'));
            $('#change_status_modal').find('#status_note').val($(this).data('status_note'));
            $('#change_status_modal').modal('show');
        });

        $(document).on('submit', 'form#change_status_form', function(e) {
            e.preventDefault();
            var data = $(this).serialize();
            var ladda = Ladda.create(document.querySelector('.update-leave-status'));
            ladda.start();
            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                dataType: 'json',
                data: data,
                success: function(result) {
                    ladda.stop();
                    if (result.success == true) {
                        $('div#change_status_modal').modal('hide');
                        toastr.success(result.msg);
                        leaves_table.ajax.reload();
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });

        $(document).on('click', 'button.delete-leave', function() {
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
                                leaves_table.ajax.reload();
                            } else {
                                toastr.error(result.msg);
                            }
                        },
                    });
                }
            });
        });

        function get_leave_summary() {
            $('#user_leave_summary').html('');
            var user_id = $('#user_id_filter').length ? $('#user_id_filter').val() : '';
            var start = $('#leave_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
            var end = $('#leave_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
            $.ajax({
                url: '{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'getUserLeaveSummary'])}}?user_id=' + user_id + '&start_date=' + start + '&end_date=' + end ,
                dataType: 'html',
                success: function(html) {
                    $('#user_leave_summary').html(html);
                },
            });
        }

        function update_quick_balance() {
            var user_id = $('#user_id_filter').length ? $('#user_id_filter').val() : '';
            
            // If no user selected and admin can see all leave, don't show balance
            @if(auth()->user()->can('essentials.crud_all_leave'))
            if (!user_id) {
                $('#quick_leave_balance_container').html('<div class="col-md-12"><div class="box box-warning"><div class="box-header with-border"><h3 class="box-title"><i class="fa fa-info-circle"></i> Leave Balance</h3></div><div class="box-body"><div class="alert alert-info"><i class="fa fa-user"></i> Please select an employee from the filter above to view their leave balance.</div></div></div></div>');
                return;
            }
            @endif
            
            // Show loading indicator
            $('#quick_leave_balance_container').html('<div class="col-md-12"><div class="box box-info"><div class="box-body text-center"><i class="fa fa-spinner fa-spin"></i> Loading leave balance...</div></div></div>');
            
            $.ajax({
                url: '{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'getQuickLeaveBalanceView'])}}',
                data: { user_id: user_id || '{{auth()->user()->id}}' },
                dataType: 'html',
                success: function(html) {
                    $('#quick_leave_balance_container').html(html);
                },
                error: function() {
                    $('#quick_leave_balance_container').html('<div class="col-md-12"><div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Failed to load leave balance. Please try again.</div></div>');
                }
            });
        }

        // Real-time leave quota checking
        $(document).on('change', '#add_leave_form select[name="essentials_leave_type_id"], #add_leave_form input[name="start_date"], #add_leave_form input[name="end_date"]', function() {
            checkLeaveQuota();
        });

        function checkLeaveQuota() {
            var leave_type_id = $('#add_leave_form select[name="essentials_leave_type_id"]').val();
            var start_date = $('#add_leave_form input[name="start_date"]').val();
            var end_date = $('#add_leave_form input[name="end_date"]').val();
            var employees = $('#add_leave_form select[name="employees[]"]').val();
            
            console.log('checkLeaveQuota called with:', {
                leave_type_id: leave_type_id,
                start_date: start_date, 
                end_date: end_date,
                employees: employees
            });
            
            // For single employee or current user
            var user_id = '';
            if (employees && employees.length === 1) {
                user_id = employees[0];
            } else if (!employees || employees.length === 0) {
                user_id = '{{auth()->user()->id}}';
            } else if (employees && employees.length > 1) {
                // Multiple employees selected, show general message
                $('#quota_status_container').show();
                $('#quota_status_alert').removeClass('alert-success alert-danger alert-warning').addClass('alert-info');
                $('#quota_status_text').html('<i class="fa fa-info-circle"></i> Multiple employees selected. Quota will be checked individually for each employee.');
                return;
            }

            if (!leave_type_id || !start_date || !end_date) {
                $('#quota_status_container').hide();
                return;
            }

            // Show loading
            $('#quota_status_container').show();
            $('#quota_status_alert').removeClass('alert-success alert-danger alert-warning alert-info').addClass('alert-info');
            $('#quota_status_text').html('<i class="fa fa-spinner fa-spin"></i> Checking leave quota...');

            $.ajax({
                url: '{{action([\Modules\Essentials\Http\Controllers\EssentialsLeaveController::class, 'checkQuota'])}}',
                method: 'POST',
                data: {
                    user_id: user_id,
                    leave_type_id: leave_type_id,
                    start_date: start_date,
                    end_date: end_date,
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
                    $('#quota_status_container').show();
                    
                    if (response.available) {
                        $('#quota_status_alert').removeClass('alert-info alert-danger alert-warning').addClass('alert-success');
                        $('#quota_status_text').html('<i class="fa fa-check-circle"></i> ' + response.message);
                        
                        // Enable submit button
                        $('.add-leave-btn').prop('disabled', false);
                    } else {
                        $('#quota_status_alert').removeClass('alert-info alert-success alert-warning').addClass('alert-danger');
                        $('#quota_status_text').html('<i class="fa fa-exclamation-triangle"></i> ' + response.message);
                        
                        // Disable submit button
                        $('.add-leave-btn').prop('disabled', true);
                    }
                },
                error: function(xhr) {
                    $('#quota_status_container').show();
                    $('#quota_status_alert').removeClass('alert-info alert-success alert-warning').addClass('alert-warning');
                    $('#quota_status_text').html('<i class="fa fa-exclamation-triangle"></i> Error checking quota. Please try again.');
                    
                    // Enable submit button (allow submission, backend will validate)
                    $('.add-leave-btn').prop('disabled', false);
                }
            });
        }
    </script>
@endsection
