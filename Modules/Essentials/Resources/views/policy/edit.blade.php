@extends('layouts.app')
@section('title', 'Edit Policy')

@section('content')
@include('essentials::layouts.nav_hrm')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Edit Policy</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Policy Information</h3>
                </div>

                {!! Form::open(['action' => ['\Modules\Essentials\Http\Controllers\EssentialsPolicyController@update', $policy->id], 'method' => 'put', 'files' => true, 'id' => 'policy_form']) !!}

                <div class="box-body">
                    <div class="form-group">
                        <label>User <span class="text-danger">*</span></label>
                        {!! Form::select('user_id', $users, $policy->user_id, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>Policy Type <span class="text-danger">*</span></label>
                        {!! Form::select('policy_type', $policy_types, $policy->policy_type, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    <div class="form-group">
                        <label>Title <span class="text-danger">*</span></label>
                        {!! Form::text('title', $policy->title, ['class' => 'form-control', 'required' => true, 'placeholder' => 'Enter policy title']) !!}
                    </div>

                    <div class="form-group">
                        <label>Content</label>
                        {!! Form::textarea('content', $policy->content, ['class' => 'form-control', 'id' => 'policy_content', 'rows' => 6]) !!}
                    </div>

                    <div class="form-group">
                        <label>User Signature Photo</label>
                        @if($policy->signature_photo)
                            <div class="mb-2">
                                <img src="{{ asset('uploads/policy_signatures/' . $policy->signature_photo) }}" alt="Signature" style="max-width: 200px; max-height: 100px;">
                                <br>
                                <small class="text-muted">Current signature</small>
                            </div>
                        @endif
                        <input type="file" name="signature_photo" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Upload new signature photo to replace (JPG, PNG, etc.)</small>
                    </div>

                    <div class="form-group">
                        <label>Status <span class="text-danger">*</span></label>
                        {!! Form::select('status', ['pending' => 'Pending', 'signed' => 'Signed', 'rejected' => 'Rejected'], $policy->status, ['class' => 'form-control', 'required' => true]) !!}
                    </div>

                    @if($policy->status == 'rejected')
                    <div class="form-group">
                        <label>Rejection Reason</label>
                        {!! Form::textarea('rejection_reason', $policy->rejection_reason, ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Enter reason for rejection']) !!}
                    </div>
                    @endif
                </div>

                <div class="box-footer">
                    {!! Form::submit('Update Policy', ['class' => 'btn btn-primary']) !!}
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
