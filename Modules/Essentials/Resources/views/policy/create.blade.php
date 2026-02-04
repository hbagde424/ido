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
                        {!! Form::select('policy_type', $policy_types, null, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        {!! Form::text('title', null, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter policy title']) !!}
                    </div>

                    <div class="form-group">
                        <label>Content</label>
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
            height: 300,
            plugins: 'link image code',
            toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright | link image | code'
        });

        $('#policy_form').validate({
            rules: {
                user_id: { required: true },
                policy_type: { required: true },
                title: { required: true }
            }
        });
    });
</script>
@endsection
