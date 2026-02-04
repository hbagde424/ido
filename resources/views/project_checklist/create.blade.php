@extends('layouts.app')
@section('title', __('Add Project'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('Add Project')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\ProjectChecklistController::class, 'store']), 'method' => 'post', 'id' => 'project_form']) !!}
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary'])
                    <div class="form-group">
                        {!! Form::label('project_name', __('Project Name') . ':*') !!}
                        {!! Form::text('project_name', null, ['class' => 'form-control', 'required', 'placeholder' => __('Enter project name')]) !!}
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
                        {!! Form::label('project_lead_id', __('Project Lead')) !!}
                        {!! Form::select('project_lead_id', App\User::forDropdown(session('user.business_id')), null, ['class' => 'form-control select2', 'placeholder' => __('Select project lead')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('assigned_users', __('Assign Users')) !!}
                        {!! Form::select('assigned_users[]', App\User::forDropdown(session('user.business_id')), null, ['class' => 'form-control select2', 'multiple']) !!}
                    </div>
                @endcomponent
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
                    <a href="{{ action([\App\Http\Controllers\ProjectChecklistController::class, 'index']) }}" class="btn btn-default">@lang('messages.cancel')</a>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</section>
@endsection

@push('js')
<script type="text/javascript">
    $(document).ready(function() {
        $('#project_form').validate();
        $('.select2').select2({ width: '100%' });
        
        // Initialize date pickers
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
    });
</script>
@endpush