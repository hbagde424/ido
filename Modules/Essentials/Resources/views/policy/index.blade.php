@extends('layouts.app')
@section('title', 'Company Policies')

@section('content')
@include('essentials::layouts.nav_hrm')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Company Policies</h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- User-Specific Policy Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> User-Specific Policy Management</h3>
                </div>
                <div class="box-body">
                    <!-- Filters -->
                    <div class="row mb-3" style="margin-bottom: 20px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select User <span class="text-danger">*</span></label>
                                <select id="user_filter" class="form-control select2" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Select Policy <span class="text-danger">*</span></label>
                                <select id="policy_filter" class="form-control" required>
                                    <option value="">-- Select Policy --</option>
                                    <option value="company_policy">Company Policy</option>
                                    <option value="hr_policy">HR Policy</option>
                                    <option value="leave_policy">Leave Policy</option>
                                    <option value="posh_policy">POSH Policy</option>
                                    <option value="nda_policy">NDA Policy</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label><br>
                                <button type="button" id="view_policy_btn" class="btn btn-primary">
                                    <i class="fa fa-eye"></i> View Policy
                                </button>
                                <button type="button" id="download_pdf_btn" class="btn btn-success">
                                    <i class="fa fa-download"></i> Download PDF
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Policy Content Display -->
                    <div id="policy_display" style="display: none;">
                        <div class="policy-header">
                            <h2 id="policy_title"></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="policy-content" id="policy_content_area">
                            <!-- Policy content will be loaded here -->
                        </div>

                        <!-- Signature Section -->
                        <div class="signature-section" style="margin-top: 50px; padding: 20px; border-top: 2px solid #8B1538;">
                            <h3 style="color: #8B1538; margin-bottom: 20px;">Employee Acknowledgment</h3>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Upload Signature <span class="text-danger">*</span></label>
                                        <input type="file" id="signature_upload" class="form-control" accept="image/*">
                                        <small class="text-muted">Upload your signature image (JPG, PNG)</small>
                                    </div>
                                    <div id="signature_preview" style="margin-top: 10px; display: none;">
                                        <img id="signature_img" src="" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="text" id="signature_date" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                    <div class="form-group">
                                        <label>Employee Name</label>
                                        <input type="text" id="employee_name" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <button type="button" id="save_signature_btn" class="btn btn-success">
                                        <i class="fa fa-save"></i> Save Signature & Acknowledge
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Policies Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> All Company Policies</h3>
                    <div class="pull-right">
                        <select id="user_filter_policies" class="form-control input-sm" style="width: 250px;">
                            <option value="">-- Select User for Signature --</option>
                            @foreach($users as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="box-body">
                    <!-- Tabs for Policy Types -->
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#company_policy_tab" data-toggle="tab" data-policy="company_policy">Company Policy</a></li>
                            <li><a href="#hr_policy_tab" data-toggle="tab" data-policy="hr_policy">HR Policy</a></li>
                            <li><a href="#leave_policy_tab" data-toggle="tab" data-policy="leave_policy">Leave Policy</a></li>
                            <li><a href="#posh_policy_tab" data-toggle="tab" data-policy="posh_policy">POSH Policy</a></li>
                            <li><a href="#nda_policy_tab" data-toggle="tab" data-policy="nda_policy">NDA Policy</a></li>
                        </ul>
                        <div class="tab-content">
                            <!-- Company Policy Tab -->
                            <div class="tab-pane active" id="company_policy_tab">
                                @include('essentials::policy.letterhead')
                                <div class="policy-header">
                                    <h2>Company Policy</h2>
                                    <button class="btn btn-success pull-right download-policy-pdf" data-policy="company_policy">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="policy-content-wrapper">
                                    <div class="policy-content">
                                        {!! \Modules\Essentials\Entities\PolicyTemplates::getTemplate('company_policy') !!}
                                    </div>
                                    <div class="signature-display" id="signature_company_policy" style="display: none;">
                                        <div style="text-align: right; margin-top: 30px; padding: 15px; border-top: 2px solid #8B1538;">
                                            <p style="margin-bottom: 10px;"><strong>Employee Signature:</strong></p>
                                            <img class="signature-image" src="" style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; padding: 5px; display: block; margin-left: auto;">
                                            <p class="employee-name" style="margin-top: 5px; font-size: 14px; text-align: right;"></p>
                                            <p class="signature-date" style="font-size: 12px; color: #666; text-align: right;"></p>
                                        </div>
                                    </div>
                                </div>
                                @include('essentials::policy.footer')
                            </div>

                            <!-- HR Policy Tab -->
                            <div class="tab-pane" id="hr_policy_tab">
                                @include('essentials::policy.letterhead')
                                <div class="policy-header">
                                    <h2>Human Resources Policy</h2>
                                    <button class="btn btn-success pull-right download-policy-pdf" data-policy="hr_policy">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="policy-content-wrapper">
                                    <div class="policy-content">
                                        {!! \Modules\Essentials\Entities\PolicyTemplates::getTemplate('hr_policy') !!}
                                    </div>
                                    <div class="signature-display" id="signature_hr_policy" style="display: none;">
                                        <div style="text-align: right; margin-top: 30px; padding: 15px; border-top: 2px solid #8B1538;">
                                            <p style="margin-bottom: 10px;"><strong>Employee Signature:</strong></p>
                                            <img class="signature-image" src="" style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; padding: 5px; display: block; margin-left: auto;">
                                            <p class="employee-name" style="margin-top: 5px; font-size: 14px; text-align: right;"></p>
                                            <p class="signature-date" style="font-size: 12px; color: #666; text-align: right;"></p>
                                        </div>
                                    </div>
                                </div>
                                @include('essentials::policy.footer')
                            </div>

                            <!-- Leave Policy Tab -->
                            <div class="tab-pane" id="leave_policy_tab">
                                @include('essentials::policy.letterhead')
                                <div class="policy-header">
                                    <h2>Leave Policy</h2>
                                    <button class="btn btn-success pull-right download-policy-pdf" data-policy="leave_policy">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="policy-content-wrapper">
                                    <div class="policy-content">
                                        {!! \Modules\Essentials\Entities\PolicyTemplates::getTemplate('leave_policy') !!}
                                    </div>
                                    <div class="signature-display" id="signature_leave_policy" style="display: none;">
                                        <div style="text-align: right; margin-top: 30px; padding: 15px; border-top: 2px solid #8B1538;">
                                            <p style="margin-bottom: 10px;"><strong>Employee Signature:</strong></p>
                                            <img class="signature-image" src="" style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; padding: 5px; display: block; margin-left: auto;">
                                            <p class="employee-name" style="margin-top: 5px; font-size: 14px; text-align: right;"></p>
                                            <p class="signature-date" style="font-size: 12px; color: #666; text-align: right;"></p>
                                        </div>
                                    </div>
                                </div>
                                @include('essentials::policy.footer')
                            </div>

                            <!-- POSH Policy Tab -->
                            <div class="tab-pane" id="posh_policy_tab">
                                @include('essentials::policy.letterhead')
                                <div class="policy-header">
                                    <h2>Prevention of Sexual Harassment (POSH) Policy</h2>
                                    <button class="btn btn-success pull-right download-policy-pdf" data-policy="posh_policy">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="policy-content-wrapper">
                                    <div class="policy-content">
                                        {!! \Modules\Essentials\Entities\PolicyTemplates::getTemplate('posh_policy') !!}
                                    </div>
                                    <div class="signature-display" id="signature_posh_policy" style="display: none;">
                                        <div style="text-align: right; margin-top: 30px; padding: 15px; border-top: 2px solid #8B1538;">
                                            <p style="margin-bottom: 10px;"><strong>Employee Signature:</strong></p>
                                            <img class="signature-image" src="" style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; padding: 5px; display: block; margin-left: auto;">
                                            <p class="employee-name" style="margin-top: 5px; font-size: 14px; text-align: right;"></p>
                                            <p class="signature-date" style="font-size: 12px; color: #666; text-align: right;"></p>
                                        </div>
                                    </div>
                                </div>
                                @include('essentials::policy.footer')
                            </div>

                            <!-- NDA Policy Tab -->
                            <div class="tab-pane" id="nda_policy_tab">
                                @include('essentials::policy.letterhead')
                                <div class="policy-header">
                                    <h2>Non-Disclosure Agreement (NDA) Policy</h2>
                                    <button class="btn btn-success pull-right download-policy-pdf" data-policy="nda_policy">
                                        <i class="fa fa-download"></i> Download PDF
                                    </button>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="policy-content-wrapper">
                                    <div class="policy-content">
                                        {!! \Modules\Essentials\Entities\PolicyTemplates::getTemplate('nda_policy') !!}
                                    </div>
                                    <div class="signature-display" id="signature_nda_policy" style="display: none;">
                                        <div style="text-align: right; margin-top: 30px; padding: 15px; border-top: 2px solid #8B1538;">
                                            <p style="margin-bottom: 10px;"><strong>Employee Signature:</strong></p>
                                            <img class="signature-image" src="" style="max-width: 150px; max-height: 80px; border: 1px solid #ddd; padding: 5px; display: block; margin-left: auto;">
                                            <p class="employee-name" style="margin-top: 5px; font-size: 14px; text-align: right;"></p>
                                            <p class="signature-date" style="font-size: 12px; color: #666; text-align: right;"></p>
                                        </div>
                                    </div>
                                </div>
                                @include('essentials::policy.footer')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@stop

@section('css')
<style>
    .letterhead-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background: white;
        border-bottom: 3px solid #8B1538;
        margin-bottom: 20px;
    }
    .letterhead-left {
        flex: 0 0 auto;
        padding-right: 30px;
    }
    .letterhead-logo {
        max-width: 180px;
        height: auto;
        display: block;
    }
    .letterhead-right {
        flex: 1;
        text-align: right;
        font-size: 13px;
        color: #555;
    }
    .letterhead-right p {
        margin: 3px 0;
        line-height: 1.4;
    }
    .letterhead-right p strong {
        font-weight: 600;
    }
    .letterhead-footer {
        background: #8B1538;
        color: white;
        text-align: center;
        padding: 15px;
        margin-top: 30px;
        font-size: 12px;
    }
    .letterhead-footer p {
        margin: 0;
    }
    .policy-header {
        border-bottom: 2px solid #8B1538;
        padding-bottom: 15px;
        margin-bottom: 20px;
    }
    .policy-header h2 {
        color: #8B1538;
        margin: 0;
        display: inline-block;
    }
    .policy-content {
        padding: 20px;
        background: #fff;
        line-height: 1.8;
    }
    .policy-content h2 {
        color: #8B1538;
        border-bottom: 2px solid #8B1538;
        padding-bottom: 10px;
        margin-top: 30px;
    }
    .policy-content h3 {
        color: #2c3e50;
        margin-top: 25px;
        font-size: 18px;
    }
    .policy-content h4 {
        color: #34495e;
        margin-top: 20px;
        font-size: 16px;
    }
    .policy-content ul {
        margin-left: 20px;
        margin-bottom: 15px;
    }
    .policy-content li {
        margin-bottom: 8px;
    }
    .policy-content p {
        margin-bottom: 15px;
        text-align: justify;
    }
    .signature-section {
        background: #f9f9f9;
        border-radius: 5px;
    }
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: #8B1538;
    }
    .nav-tabs-custom > .nav-tabs > li.active > a {
        border-top-color: #8B1538;
    }
    .box-success {
        border-top-color: #00a65a;
    }
    .signature-display {
        margin-top: 30px;
        padding: 15px;
        border-top: 2px solid #8B1538;
        text-align: right;
    }
    .signature-display img {
        max-width: 150px !important;
        max-height: 80px !important;
        border: 1px solid #ddd;
        padding: 5px;
        display: block;
        margin-left: auto;
        margin-right: 0;
        margin-bottom: 10px;
    }
    .signature-display p {
        margin: 5px 0;
        text-align: right;
    }
</style>
@endsection

@section('javascript')
<script type="text/javascript">
    var selectedUser = null;
    var selectedPolicy = null;
    var signatureFile = null;

    $(document).ready(function() {
        $('.select2').select2();

        // User filter for policies section
        $('#user_filter_policies').change(function() {
            var userId = $(this).val();
            var userName = $(this).find('option:selected').text();

            if (!userId) {
                $('.signature-display').hide();
                return;
            }

            // Load signatures for all policies
            var policyTypes = ['company_policy', 'hr_policy', 'leave_policy', 'posh_policy', 'nda_policy'];
            
            policyTypes.forEach(function(policyType) {
                $.ajax({
                    url: '{{ route("policy.getUserSignature") }}',
                    type: 'GET',
                    data: { 
                        user_id: userId,
                        policy_type: policyType 
                    },
                    dataType: 'json',
                    success: function(response) {
                        var signatureDiv = $('#signature_' + policyType);
                        
                        if (response.success && response.signature) {
                            signatureDiv.find('.signature-image').attr('src', response.signature).css('display', 'block');
                            signatureDiv.find('.employee-name').text(response.user_name || userName);
                            signatureDiv.find('.signature-date').text('Date: ' + response.signed_date);
                            signatureDiv.css('display', 'block');
                        } else {
                            signatureDiv.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error, xhr.status, xhr.responseText);
                    }
                });
            });
        });

        // Download policy PDF with user signature
        $('.download-policy-pdf').click(function() {
            var policyType = $(this).data('policy');
            var userId = $('#user_filter_policies').val();

            if (userId) {
                var url = '{{ route("policy.downloadUserPolicyPdf") }}?user_id=' + userId + '&policy_type=' + policyType;
                window.open(url, '_blank');
            } else {
                var url = '{{ route("policy.downloadPolicyPdf", "") }}/' + policyType;
                window.open(url, '_blank');
            }
        });

        // View Policy Button
        $('#view_policy_btn').click(function() {
            var userId = $('#user_filter').val();
            var policyType = $('#policy_filter').val();

            if (!userId) {
                alert('Please select a user');
                return;
            }

            if (!policyType) {
                alert('Please select a policy');
                return;
            }

            selectedUser = userId;
            selectedPolicy = policyType;

            // Get user name
            var userName = $('#user_filter option:selected').text();
            $('#employee_name').val(userName);

            // Load policy content
            $.ajax({
                url: '{{ route("policy.getPolicyContent") }}',
                type: 'GET',
                data: { 
                    user_id: userId,
                    policy_type: policyType 
                },
                success: function(response) {
                    if (response.success) {
                        var policyTitles = {
                            'company_policy': 'Company Policy',
                            'hr_policy': 'HR Policy',
                            'leave_policy': 'Leave Policy',
                            'posh_policy': 'POSH Policy',
                            'nda_policy': 'NDA Policy'
                        };
                        
                        $('#policy_title').text(policyTitles[policyType]);
                        $('#policy_content_area').html(response.content);
                        $('#policy_display').slideDown();

                        // If signature exists, show it
                        if (response.signature) {
                            $('#signature_img').attr('src', response.signature);
                            $('#signature_preview').show();
                        } else {
                            $('#signature_preview').hide();
                        }
                    }
                },
                error: function() {
                    alert('Error loading policy');
                }
            });
        });

        // Signature Upload Preview
        $('#signature_upload').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                signatureFile = file;
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#signature_img').attr('src', e.target.result);
                    $('#signature_preview').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Save Signature
        $('#save_signature_btn').click(function() {
            if (!selectedUser || !selectedPolicy) {
                alert('Please select user and policy first');
                return;
            }

            if (!signatureFile && !$('#signature_img').attr('src')) {
                alert('Please upload signature');
                return;
            }

            var formData = new FormData();
            formData.append('user_id', selectedUser);
            formData.append('policy_type', selectedPolicy);
            if (signatureFile) {
                formData.append('signature', signatureFile);
            }
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '{{ route("policy.saveSignature") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert('Signature saved successfully!');
                        // Refresh signatures in policy tabs if same user selected
                        if ($('#user_filter_policies').val() == selectedUser) {
                            $('#user_filter_policies').trigger('change');
                        }
                    }
                },
                error: function() {
                    alert('Error saving signature');
                }
            });
        });

        // Download PDF
        $('#download_pdf_btn').click(function() {
            var userId = $('#user_filter').val();
            var policyType = $('#policy_filter').val();

            if (!userId) {
                alert('Please select a user');
                return;
            }

            if (!policyType) {
                alert('Please select a policy');
                return;
            }

            var url = '{{ route("policy.downloadUserPolicyPdf") }}?user_id=' + userId + '&policy_type=' + policyType;
            window.open(url, '_blank');
        });
    });
</script>
@endsection
