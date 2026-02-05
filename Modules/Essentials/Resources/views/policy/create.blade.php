@extends('layouts.app')
@section('title', 'Add New Policy')

@section('content')
@include('essentials::layouts.nav_hrm')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Add New Policy</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Policy Information</h3>
                </div>

                {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsPolicyController@store', 'method' => 'post', 'files' => true, 'id' => 'policy_form']) !!}

                <div class="box-body">
                    <div class="form-group">
                        <label>User <span class="text-danger">*</span></label>
                        {!! Form::select('user_id', $users, null, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>Policy Type <span class="text-danger">*</span></label>
                        {!! Form::select('policy_type', $policy_types, request()->get('policy_type'), ['class' => 'form-control', 'required' => true, 'readonly' => request()->has('policy_type')]) !!}
                    </div>

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        {!! Form::text('title', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter policy title']) !!}
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        <button type="button" class="btn btn-sm btn-info pull-right" id="load_template_btn">
                            <i class="fa fa-file-text"></i> Load Default Template
                        </button>
                        <div class="clearfix"></div>
                        {!! Form::textarea('content', null, ['class' => 'form-control', 'id' => 'policy_content', 'rows' => 6]) !!}
                    </div>

                    <div class="form-group">
                        <label>User Signature Photo</label>
                        <input type="file" name="signature_photo" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Upload user's signature photo (JPG, PNG, etc.)</small>
                    </div>

                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        {!! Form::select('status', ['pending' => 'Pending', 'signed' => 'Signed', 'rejected' => 'Rejected'], 'pending', ['class' => 'form-control', 'required' => true]) !!}
                    </div>
                </div>

                <div class="box-footer">
                    {!! Form::submit('Save Policy', ['class' => 'btn btn-primary']) !!}
                    <a href="{{ action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@index') }}" class="btn btn-default">Cancel</a>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</section>

@stop

@section('javascript')
<script type="text/javascript">
    $(document).ready(function() {
        tinymce.init({
            selector: '#policy_content',
            height: 400,
            plugins: 'link image code lists table',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
            menubar: false
        });

        $('#policy_form').validate({
            rules: {
                user_id: { required: true },
                policy_type: { required: true },
                title: { required: true }
            }
        });

        // Load template button
        $('#load_template_btn').click(function() {
            var policyType = $('select[name="policy_type"]').val();
            if (!policyType) {
                alert('Please select a policy type first');
                return;
            }

            if (confirm('This will replace current content. Continue?')) {
                $.ajax({
                    url: '{{ action("\Modules\Essentials\Http\Controllers\EssentialsPolicyController@getTemplate") }}',
                    type: 'GET',
                    data: { policy_type: policyType },
                    success: function(response) {
                        if (response.success) {
                            tinymce.get('policy_content').setContent(response.template);
                            alert('Template loaded successfully');
                        }
                    },
                    error: function() {
                        alert('Error loading template');
                    }
                });
            }
        });
    });
</script>
@endsection
