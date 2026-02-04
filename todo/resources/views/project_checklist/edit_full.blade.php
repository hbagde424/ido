@extends('layouts.app')
@section('title', __('Edit Project'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('Edit Project')</h1>
</section>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action([\App\Http\Controllers\ProjectChecklistController::class, 'update'], $project->id), 'method' => 'put', 'id' => 'project_form']) !!}
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary'])
                    <div class="form-group">
                        {!! Form::label('project_name', __('Project Name') . ':*') !!}
                        {!! Form::text('project_name', $project->project_name, ['class' => 'form-control', 'required', 'placeholder' => __('Enter project name')]) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('assigned_users', __('Assign Users')) !!}
                        {!! Form::select('assigned_users[]', App\User::forDropdown(session('user.business_id')), $project->users->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple']) !!}
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
    });
</script>
@endpush
