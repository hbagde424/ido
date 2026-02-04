<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action([\App\Http\Controllers\ProjectChecklistController::class, 'update'], $project->id), 'method' => 'put', 'id' => 'project_form']) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('Edit Project')</h4>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('project_name', __('Project Name') . ':*') !!}
                {!! Form::text('project_name', $project->project_name, ['class' => 'form-control', 'required', 'placeholder' => __('Enter project name')]) !!}
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('start_date', __('Start Date')) !!}
                        {!! Form::text('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : null, ['class' => 'form-control date-picker', 'placeholder' => __('Select start date'), 'readonly']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('end_date', __('End Date')) !!}
                        {!! Form::text('end_date', $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d/m/Y') : null, ['class' => 'form-control date-picker', 'placeholder' => __('Select end date'), 'readonly']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('project_lead_id', __('Project Lead')) !!}
                {!! Form::select('project_lead_id', App\User::forDropdown(session('user.business_id')), $project->project_lead_id, ['class' => 'form-control select2', 'placeholder' => __('Select project lead')]) !!}
            </div>

            <div class="form-group">
                {!! Form::label('assigned_users', __('Assign Users')) !!}
                {!! Form::select('assigned_users[]', App\User::forDropdown(session('user.business_id')), $project->users->pluck('id')->toArray(), ['class' => 'form-control select2', 'multiple']) !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
    // Initialize select2 and validation when this modal content is inserted
    $(document).ready(function() {
        // Find the modal container - use project_modal class
        var $modal = $('.project_modal');
        
        // Wait a bit for modal to be fully loaded
        setTimeout(function() {
            // If select2 assets are available, init the select inside modal
            try {
                // Destroy existing select2 if any
                $('.select2', $modal).each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
                
                // Initialize select2 with proper configuration for modal
                $('.select2', $modal).select2({ 
                    width: '100%', 
                    dropdownParent: $modal,
                    closeOnSelect: false  // Keep open for multiple selection, but we'll close manually
                });
                
                // For multiple select (Assign Users), close dropdown after selection
                $('select.select2[multiple]', $modal).off('select2:select select2:unselect').on('select2:select select2:unselect', function (e) {
                    var $select = $(this);
                    // Close dropdown after a short delay to allow selection to complete
                    setTimeout(function() {
                        $select.select2('close');
                    }, 150);
                });
            } catch (e) {
                // select2 not loaded yet or already initialized
                console.log('Select2 initialization error:', e);
            }
        }, 100);

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

        // Initialize jQuery validate if available
        if ($.fn.validate) {
            $('#project_form').validate();
        }
    });
</script>