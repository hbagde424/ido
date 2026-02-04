<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title">{{ $policy->title }}</h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <p><strong>User:</strong> {{ $policy->user->first_name }} {{ $policy->user->last_name }}</p>
            <p><strong>Policy Type:</strong> {{ $policy->getPolicyTypeLabel() }}</p>
            <p><strong>Status:</strong> <span class="label label-{{ $policy->status == 'signed' ? 'success' : ($policy->status == 'rejected' ? 'danger' : 'warning') }}">{{ $policy->getStatusLabel() }}</span></p>
        </div>
        <div class="col-md-6">
            <p><strong>Created:</strong> {{ $policy->created_at->format('d-m-Y H:i') }}</p>
            @if($policy->signed_date)
                <p><strong>Signed Date:</strong> {{ \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') }}</p>
            @endif
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <h5><strong>Policy Content:</strong></h5>
            <div class="well">
                {!! $policy->content !!}
            </div>
        </div>
    </div>

    @if($policy->signature_photo)
    <div class="row">
        <div class="col-md-12">
            <h5><strong>User Signature:</strong></h5>
            <img src="{{ asset('uploads/policy_signatures/' . $policy->signature_photo) }}" alt="Signature" style="max-width: 300px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">
        </div>
    </div>
    @endif

    @if($policy->status == 'rejected' && $policy->rejection_reason)
    <div class="row">
        <div class="col-md-12">
            <h5><strong>Rejection Reason:</strong></h5>
            <div class="alert alert-danger">
                {{ $policy->rejection_reason }}
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal-footer">
    <a href="{{ action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@downloadPdf', $policy->id) }}" class="btn btn-success" target="_blank">
        <i class="fa fa-download"></i> Download PDF
    </a>
    <a href="{{ action('\Modules\Essentials\Http\Controllers\EssentialsPolicyController@edit', $policy->id) }}" class="btn btn-primary">
        <i class="fa fa-edit"></i> Edit
    </a>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
