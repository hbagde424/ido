@extends('layouts.app')
@section('title', 'Company Policies')

@section('content')
@include('essentials::layouts.nav_hrm')

<style>
    .policy-pages-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 20px;
        max-width: 900px;
        margin: 0 auto;
    }
    
    .policy-page-header-section {
        background: white;
        padding: 20px 40px;
        border-bottom: 3px solid #8B1538;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0;
    }
    
    .policy-page-logo {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .policy-page-logo img {
        max-width: 100px;
        height: auto;
    }
    
    .policy-page-logo-text {
        font-size: 20px;
        font-weight: bold;
        color: #8B1538;
    }
    
    .policy-page-contact {
        text-align: right;
        font-size: 10px;
        color: #555;
        line-height: 1.6;
    }
    
    .policy-page-contact p {
        margin: 2px 0;
        padding: 0;
    }
    
    .policy-page-contact img {
        width: 12px;
        height: 12px;
        margin-right: 4px;
        vertical-align: middle;
    }
    
    .policy-page-footer-section {
        background: #8B1538;
        color: white;
        padding: 12px 40px;
        text-align: center;
        font-size: 10px;
        line-height: 1.4;
        margin-top: 0;
    }
    
    .policy-page-footer-section p {
        margin: 2px 0;
        padding: 0;
    }
    
    .policy-page-footer-icon {
        margin-right: 5px;
    }
    
    .policy-page {
        background: white;
        border: 1px solid #ddd;
        padding: 40px;
        min-height: 600px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        page-break-after: always;
        position: relative;
    }
    
    .policy-page:first-of-type {
        border-top: none;
    }
    
    .policy-page-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #8B1538;
    }
    
    .policy-page-header h2 {
        color: #8B1538;
        margin: 0;
        font-size: 24px;
    }
    
    .policy-page-number {
        position: absolute;
        bottom: 20px;
        right: 40px;
        color: #999;
        font-size: 12px;
    }
    
    .policy-content-section {
        font-size: 14px;
        line-height: 1.8;
        color: #333;
        margin-bottom: 30px;
    }
    
    .policy-content-section h3 {
        color: #8B1538;
        margin-top: 20px;
        margin-bottom: 10px;
        font-size: 16px;
    }
    
    .policy-content-section ul {
        margin-left: 20px;
        margin-bottom: 15px;
    }
    
    .policy-content-section li {
        margin-bottom: 8px;
    }
    
    .acknowledgement-box {
        background: #f5f5f5;
        border-left: 4px solid #8B1538;
        padding: 15px;
        margin-top: 30px;
        border-radius: 3px;
    }
    
    .acknowledgement-box label {
        font-weight: 600;
        color: #8B1538;
        margin: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
    }
    
    .acknowledgement-box input[type="checkbox"] {
        margin-right: 10px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .signature-section-final {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 2px solid #8B1538;
    }
    
    .signature-section-final h3 {
        color: #8B1538;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .signature-boxes {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 40px;
    }
    
    .signature-box {
        text-align: center;
    }
    
    .signature-box p {
        font-weight: 600;
        margin-bottom: 20px;
        color: #8B1538;
    }
    
    .signature-line {
        border-top: 1px solid #333;
        margin: 60px 0 10px 0;
        height: 0;
    }
    
    .signature-name {
        font-size: 14px;
        font-weight: 600;
        margin-top: 10px;
    }
    
    .signature-date {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }
    
    .signature-upload-area {
        margin-top: 20px;
        padding: 15px;
        background: #f9f9f9;
        border: 1px dashed #ddd;
        border-radius: 3px;
        text-align: center;
        min-height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .signature-upload-area img {
        max-width: 150px;
        max-height: 80px;
        border: 1px solid #ddd;
        padding: 5px;
    }
    
    .signature-upload-input {
        display: none;
    }
    
    .upload-btn {
        background: #8B1538;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        font-size: 12px;
        margin-top: 10px;
    }
    
    .upload-btn:hover {
        background: #6b0f2a;
    }
    
    @media print {
        .policy-page {
            page-break-after: always;
            box-shadow: none;
            border: none;
        }
    }
</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>Company Policies</h1>
</section>

<!-- Main content -->
<section class="content">
    <!-- User-Specific Policy Section -->
    <!-- <div class="row">
        <div class="col-md-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-user"></i> User-Specific Policy Management</h3>
                </div>
                <div class="box-body">
                    
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

                  
                    <div id="policy_display" style="display: none;">
                        <div class="policy-header">
                            <h2 id="policy_title"></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="policy-content" id="policy_content_area">
                          
                        </div>

                       
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
                                        <label>Upload Photo <span class="text-danger">*</span></label>
                                        <input type="file" id="photo_upload" class="form-control" accept="image/*">
                                        <small class="text-muted">Upload your profile photo (JPG, PNG)</small>
                                    </div>
                                    <div id="photo_preview" style="margin-top: 10px; display: none;">
                                        <img id="photo_img" src="" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; padding: 5px;">
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Date</label>
                                        <input type="text" id="signature_date" class="form-control" value="{{ date('d-m-Y') }}" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Employee Name</label>
                                        <input type="text" id="employee_name" class="form-control" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">
                                    <button type="button" id="save_signature_btn" class="btn btn-success">
                                        <i class="fa fa-save"></i> Save Signature & Photo
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- All Policies Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-book"></i> All Company Policies</h3>
                </div>
                <div class="box-body">
                    <!-- User Selection -->
                    <div class="row mb-3" style="margin-bottom: 20px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Select User <span class="text-danger">*</span></label>
                                <select id="user_filter_policies" class="form-control select2" required>
                                    <option value="">-- Select User --</option>
                                    @foreach($users as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

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
                                <div class="policy-pages-container">
                                    @php
                                        $pages = \Modules\Essentials\Entities\PolicyTemplates::getTemplatePages('company_policy');
                                        $totalPages = count($pages);
                                    @endphp
                                    
                                    @foreach($pages as $pageIndex => $pageContent)
                                        <div class="policy-page">
                                            <!-- Header on every page -->
                                            <div class="policy-page-header-section">
                                                <div class="policy-page-logo">
                                                    @php
                                                        $svgPath = public_path('images/logo-akalp.svg');
                                                        $svgBase64 = null;
                                                        if(file_exists($svgPath)) {
                                                            $svgContent = file_get_contents($svgPath);
                                                            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                        }
                                                    @endphp
                                                    @if($svgBase64)
                                                        <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                    @else
                                                        <span class="policy-page-logo-text">AKALP</span>
                                                    @endif
                                                </div>
                                                <div class="policy-page-contact">
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                                </div>
                                            </div>
                                            
                                            <div class="policy-page-header">
                                                <h2>Company Policy - Part {{ $pageIndex + 1 }} of {{ $totalPages }}</h2>
                                            </div>
                                            <div class="policy-content-section">
                                                {!! $pageContent !!}
                                            </div>
                                            
                                            <!-- Acknowledgement on every page -->
                                            <div class="acknowledgement-box">
                                                <label>
                                                    <input type="checkbox" class="policy-acknowledgement" data-policy="company_policy">
                                                    I acknowledge that I have read this section of the Company Policy
                                                </label>
                                            </div>
                                            
                                            <div class="policy-page-number">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                            
                                            <!-- Footer -->
                                            <div class="policy-page-footer-section">
                                                <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                                <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Last Page - Signature -->
                                    <div class="policy-page">
                                        <!-- Header on final page -->
                                        <div class="policy-page-header-section">
                                            <div class="policy-page-logo">
                                                @php
                                                    $svgPath = public_path('images/logo-akalp.svg');
                                                    $svgBase64 = null;
                                                    if(file_exists($svgPath)) {
                                                        $svgContent = file_get_contents($svgPath);
                                                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                    }
                                                @endphp
                                                @if($svgBase64)
                                                    <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                @else
                                                    <span class="policy-page-logo-text">AKALP</span>
                                                @endif
                                            </div>
                                            <div class="policy-page-contact">
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-header">
                                            <h2>Employee Acknowledgment & Signature</h2>
                                        </div>
                                        
                                        <div class="signature-section-final">
                                            <h3>Signatures</h3>
                                            <div class="signature-boxes">
                                                <div class="signature-box">
                                                    <p>Employee Signature</p>
                                                    <div class="signature-upload-area" id="signature_upload_company_policy">
                                                        <div style="text-align: center;">
                                                            <input type="file" class="signature-upload-input signature-file-input" data-policy="company_policy" accept="image/*">
                                                            <button class="upload-btn" onclick="$('.signature-file-input[data-policy=\'company_policy\']').click();">
                                                                Upload Signature
                                                            </button>
                                                            <img class="signature-preview-img" src="" style="display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name" id="employee_name_company">Employee Name</div>
                                                    <div class="signature-date" id="signature_date_company">Date: ___________</div>
                                                </div>
                                                
                                                <div class="signature-box">
                                                    <p>Company Representative</p>
                                                    <div style="height: 80px;"></div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">Authorized Signatory</div>
                                                    <div class="signature-date">Date: ___________</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-number">Page {{ $totalPages + 1 }} (Final)</div>
                                        
                                        <!-- Footer -->
                                        <div class="policy-page-footer-section">
                                            <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                            <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="hr_policy_tab">
                                <div class="policy-pages-container">
                                    @php
                                        $pages = \Modules\Essentials\Entities\PolicyTemplates::getTemplatePages('hr_policy');
                                        $totalPages = count($pages);
                                    @endphp
                                    
                                    @foreach($pages as $pageIndex => $pageContent)
                                        <div class="policy-page">
                                            <!-- Header on every page -->
                                            <div class="policy-page-header-section">
                                                <div class="policy-page-logo">
                                                    @php
                                                        $svgPath = public_path('images/logo-akalp.svg');
                                                        $svgBase64 = null;
                                                        if(file_exists($svgPath)) {
                                                            $svgContent = file_get_contents($svgPath);
                                                            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                        }
                                                    @endphp
                                                    @if($svgBase64)
                                                        <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                    @else
                                                        <span class="policy-page-logo-text">AKALP</span>
                                                    @endif
                                                </div>
                                                <div class="policy-page-contact">
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                                </div>
                                            </div>
                                            
                                            <div class="policy-page-header">
                                                <h2>HR Policy - Part {{ $pageIndex + 1 }} of {{ $totalPages }}</h2>
                                            </div>
                                            <div class="policy-content-section">
                                                {!! $pageContent !!}
                                            </div>
                                            
                                            <!-- Acknowledgement on every page -->
                                            <div class="acknowledgement-box">
                                                <label>
                                                    <input type="checkbox" class="policy-acknowledgement" data-policy="hr_policy">
                                                    I acknowledge that I have read this section of the HR Policy
                                                </label>
                                            </div>
                                            
                                            <div class="policy-page-number">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                            
                                            <!-- Footer -->
                                            <div class="policy-page-footer-section">
                                                <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                                <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Last Page - Signature -->
                                    <div class="policy-page">
                                        <!-- Header on final page -->
                                        <div class="policy-page-header-section">
                                            <div class="policy-page-logo">
                                                @php
                                                    $svgPath = public_path('images/logo-akalp.svg');
                                                    $svgBase64 = null;
                                                    if(file_exists($svgPath)) {
                                                        $svgContent = file_get_contents($svgPath);
                                                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                    }
                                                @endphp
                                                @if($svgBase64)
                                                    <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                @else
                                                    <span class="policy-page-logo-text">AKALP</span>
                                                @endif
                                            </div>
                                            <div class="policy-page-contact">
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-header">
                                            <h2>Employee Acknowledgment & Signature</h2>
                                        </div>
                                        
                                        <div class="acknowledgement-box" style="margin-top: 40px;">
                                            <p style="margin: 0 0 15px 0; font-weight: 600; color: #8B1538;">
                                                By signing below, I confirm that:
                                            </p>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <li>I have read and understood all sections of the HR Policy</li>
                                                <li>I agree to comply with all terms and conditions</li>
                                                <li>I understand the consequences of non-compliance</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="signature-section-final">
                                            <h3>Signatures</h3>
                                            <div class="signature-boxes">
                                                <div class="signature-box">
                                                    <p>Employee Signature</p>
                                                    <div class="signature-upload-area" id="signature_upload_hr_policy">
                                                        <div style="text-align: center;">
                                                            <input type="file" class="signature-upload-input signature-file-input" data-policy="hr_policy" accept="image/*">
                                                            <button class="upload-btn" onclick="$('.signature-file-input[data-policy=\'hr_policy\']').click();">
                                                                Upload Signature
                                                            </button>
                                                            <img class="signature-preview-img" src="" style="display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name" id="employee_name_hr">Employee Name</div>
                                                    <div class="signature-date" id="signature_date_hr">Date: ___________</div>
                                                </div>
                                                
                                                <div class="signature-box">
                                                    <p>Company Representative</p>
                                                    <div style="height: 80px;"></div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">Authorized Signatory</div>
                                                    <div class="signature-date">Date: ___________</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-number">Page {{ $totalPages + 1 }} (Final)</div>
                                        
                                        <!-- Footer -->
                                        <div class="policy-page-footer-section">
                                            <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                            <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leave Policy Tab -->
                            <div class="tab-pane" id="leave_policy_tab">
                                <div class="policy-pages-container">
                                    @php
                                        $pages = \Modules\Essentials\Entities\PolicyTemplates::getTemplatePages('leave_policy');
                                        $totalPages = count($pages);
                                    @endphp
                                    
                                    @foreach($pages as $pageIndex => $pageContent)
                                        <div class="policy-page">
                                            <!-- Header on every page -->
                                            <div class="policy-page-header-section">
                                                <div class="policy-page-logo">
                                                    @php
                                                        $svgPath = public_path('images/logo-akalp.svg');
                                                        $svgBase64 = null;
                                                        if(file_exists($svgPath)) {
                                                            $svgContent = file_get_contents($svgPath);
                                                            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                        }
                                                    @endphp
                                                    @if($svgBase64)
                                                        <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                    @else
                                                        <span class="policy-page-logo-text">AKALP</span>
                                                    @endif
                                                </div>
                                                <div class="policy-page-contact">
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                                </div>
                                            </div>
                                            
                                            <div class="policy-page-header">
                                                <h2>Leave Policy - Part {{ $pageIndex + 1 }} of {{ $totalPages }}</h2>
                                            </div>
                                            <div class="policy-content-section">
                                                {!! $pageContent !!}
                                            </div>
                                            
                                            <!-- Acknowledgement on every page -->
                                            <div class="acknowledgement-box">
                                                <label>
                                                    <input type="checkbox" class="policy-acknowledgement" data-policy="leave_policy">
                                                    I acknowledge that I have read this section of the Leave Policy
                                                </label>
                                            </div>
                                            
                                            <div class="policy-page-number">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                            
                                            <!-- Footer -->
                                            <div class="policy-page-footer-section">
                                                <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                                <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Last Page - Signature -->
                                    <div class="policy-page">
                                        <!-- Header on final page -->
                                        <div class="policy-page-header-section">
                                            <div class="policy-page-logo">
                                                @php
                                                    $svgPath = public_path('images/logo-akalp.svg');
                                                    $svgBase64 = null;
                                                    if(file_exists($svgPath)) {
                                                        $svgContent = file_get_contents($svgPath);
                                                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                    }
                                                @endphp
                                                @if($svgBase64)
                                                    <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                @else
                                                    <span class="policy-page-logo-text">AKALP</span>
                                                @endif
                                            </div>
                                            <div class="policy-page-contact">
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-header">
                                            <h2>Employee Acknowledgment & Signature</h2>
                                        </div>
                                        
                                        <div class="acknowledgement-box" style="margin-top: 40px;">
                                            <p style="margin: 0 0 15px 0; font-weight: 600; color: #8B1538;">
                                                By signing below, I confirm that:
                                            </p>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <li>I have read and understood all sections of the Leave Policy</li>
                                                <li>I agree to comply with all terms and conditions</li>
                                                <li>I understand the consequences of non-compliance</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="signature-section-final">
                                            <h3>Signatures</h3>
                                            <div class="signature-boxes">
                                                <div class="signature-box">
                                                    <p>Employee Signature</p>
                                                    <div class="signature-upload-area" id="signature_upload_leave_policy">
                                                        <div style="text-align: center;">
                                                            <input type="file" class="signature-upload-input signature-file-input" data-policy="leave_policy" accept="image/*">
                                                            <button class="upload-btn" onclick="$('.signature-file-input[data-policy=\'leave_policy\']').click();">
                                                                Upload Signature
                                                            </button>
                                                            <img class="signature-preview-img" src="" style="display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name" id="employee_name_leave">Employee Name</div>
                                                    <div class="signature-date" id="signature_date_leave">Date: ___________</div>
                                                </div>
                                                
                                                <div class="signature-box">
                                                    <p>Company Representative</p>
                                                    <div style="height: 80px;"></div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">Authorized Signatory</div>
                                                    <div class="signature-date">Date: ___________</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-number">Page {{ $totalPages + 1 }} (Final)</div>
                                        
                                        <!-- Footer -->
                                        <div class="policy-page-footer-section">
                                            <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                            <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- POSH Policy Tab -->
                            <div class="tab-pane" id="posh_policy_tab">
                                <div class="policy-pages-container">
                                    @php
                                        $pages = \Modules\Essentials\Entities\PolicyTemplates::getTemplatePages('posh_policy');
                                        $totalPages = count($pages);
                                    @endphp
                                    
                                    @foreach($pages as $pageIndex => $pageContent)
                                        <div class="policy-page">
                                            <!-- Header on every page -->
                                            <div class="policy-page-header-section">
                                                <div class="policy-page-logo">
                                                    @php
                                                        $svgPath = public_path('images/logo-akalp.svg');
                                                        $svgBase64 = null;
                                                        if(file_exists($svgPath)) {
                                                            $svgContent = file_get_contents($svgPath);
                                                            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                        }
                                                    @endphp
                                                    @if($svgBase64)
                                                        <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                    @else
                                                        <span class="policy-page-logo-text">AKALP</span>
                                                    @endif
                                                </div>
                                                <div class="policy-page-contact">
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                                </div>
                                            </div>
                                            
                                            <div class="policy-page-header">
                                                <h2>POSH Policy - Part {{ $pageIndex + 1 }} of {{ $totalPages }}</h2>
                                            </div>
                                            <div class="policy-content-section">
                                                {!! $pageContent !!}
                                            </div>
                                            
                                            <!-- Acknowledgement on every page -->
                                            <div class="acknowledgement-box">
                                                <label>
                                                    <input type="checkbox" class="policy-acknowledgement" data-policy="posh_policy">
                                                    I acknowledge that I have read this section of the POSH Policy
                                                </label>
                                            </div>
                                            
                                            <div class="policy-page-number">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                            
                                            <!-- Footer -->
                                            <div class="policy-page-footer-section">
                                                <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                                <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Last Page - Signature -->
                                    <div class="policy-page">
                                        <!-- Header on final page -->
                                        <div class="policy-page-header-section">
                                            <div class="policy-page-logo">
                                                @php
                                                    $svgPath = public_path('images/logo-akalp.svg');
                                                    $svgBase64 = null;
                                                    if(file_exists($svgPath)) {
                                                        $svgContent = file_get_contents($svgPath);
                                                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                    }
                                                @endphp
                                                @if($svgBase64)
                                                    <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                @else
                                                    <span class="policy-page-logo-text">AKALP</span>
                                                @endif
                                            </div>
                                            <div class="policy-page-contact">
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-header">
                                            <h2>Employee Acknowledgment & Signature</h2>
                                        </div>
                                        
                                        <div class="acknowledgement-box" style="margin-top: 40px;">
                                            <p style="margin: 0 0 15px 0; font-weight: 600; color: #8B1538;">
                                                By signing below, I confirm that:
                                            </p>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <li>I have read and understood all sections of the POSH Policy</li>
                                                <li>I agree to comply with all terms and conditions</li>
                                                <li>I understand the consequences of non-compliance</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="signature-section-final">
                                            <h3>Signatures</h3>
                                            <div class="signature-boxes">
                                                <div class="signature-box">
                                                    <p>Employee Signature</p>
                                                    <div class="signature-upload-area" id="signature_upload_posh_policy">
                                                        <div style="text-align: center;">
                                                            <input type="file" class="signature-upload-input signature-file-input" data-policy="posh_policy" accept="image/*">
                                                            <button class="upload-btn" onclick="$('.signature-file-input[data-policy=\'posh_policy\']').click();">
                                                                Upload Signature
                                                            </button>
                                                            <img class="signature-preview-img" src="" style="display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name" id="employee_name_posh">Employee Name</div>
                                                    <div class="signature-date" id="signature_date_posh">Date: ___________</div>
                                                </div>
                                                
                                                <div class="signature-box">
                                                    <p>Company Representative</p>
                                                    <div style="height: 80px;"></div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">Authorized Signatory</div>
                                                    <div class="signature-date">Date: ___________</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-number">Page {{ $totalPages + 1 }} (Final)</div>
                                        
                                        <!-- Footer -->
                                        <div class="policy-page-footer-section">
                                            <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                            <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- NDA Policy Tab -->
                            <div class="tab-pane" id="nda_policy_tab">
                                <div class="policy-pages-container">
                                    @php
                                        $pages = \Modules\Essentials\Entities\PolicyTemplates::getTemplatePages('nda_policy');
                                        $totalPages = count($pages);
                                    @endphp
                                    
                                    @foreach($pages as $pageIndex => $pageContent)
                                        <div class="policy-page">
                                            <!-- Header on every page -->
                                            <div class="policy-page-header-section">
                                                <div class="policy-page-logo">
                                                    @php
                                                        $svgPath = public_path('images/logo-akalp.svg');
                                                        $svgBase64 = null;
                                                        if(file_exists($svgPath)) {
                                                            $svgContent = file_get_contents($svgPath);
                                                            $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                        }
                                                    @endphp
                                                    @if($svgBase64)
                                                        <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                    @else
                                                        <span class="policy-page-logo-text">AKALP</span>
                                                    @endif
                                                </div>
                                                <div class="policy-page-contact">
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                    <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                                </div>
                                            </div>
                                            
                                            <div class="policy-page-header">
                                                <h2>NDA Policy - Part {{ $pageIndex + 1 }} of {{ $totalPages }}</h2>
                                            </div>
                                            <div class="policy-content-section">
                                                {!! $pageContent !!}
                                            </div>
                                            
                                            <!-- Acknowledgement on every page -->
                                            <div class="acknowledgement-box">
                                                <label>
                                                    <input type="checkbox" class="policy-acknowledgement" data-policy="nda_policy">
                                                    I acknowledge that I have read this section of the NDA Policy
                                                </label>
                                            </div>
                                            
                                            <div class="policy-page-number">Page {{ $pageIndex + 1 }} of {{ $totalPages }}</div>
                                            
                                            <!-- Footer -->
                                            <div class="policy-page-footer-section">
                                                <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                                <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <!-- Last Page - Signature -->
                                    <div class="policy-page">
                                        <!-- Header on final page -->
                                        <div class="policy-page-header-section">
                                            <div class="policy-page-logo">
                                                @php
                                                    $svgPath = public_path('images/logo-akalp.svg');
                                                    $svgBase64 = null;
                                                    if(file_exists($svgPath)) {
                                                        $svgContent = file_get_contents($svgPath);
                                                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                                                    }
                                                @endphp
                                                @if($svgBase64)
                                                    <img src="{{ $svgBase64 }}" alt="AKALP Logo">
                                                @else
                                                    <span class="policy-page-logo-text">AKALP</span>
                                                @endif
                                            </div>
                                            <div class="policy-page-contact">
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> www.akalptechnomediasolutions.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> akalptechnomediasolutions@gmail.com</p>
                                                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> +91 8085504485, +91 9826068413</p>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-header">
                                            <h2>Employee Acknowledgment & Signature</h2>
                                        </div>
                                        
                                        <div class="acknowledgement-box" style="margin-top: 40px;">
                                            <p style="margin: 0 0 15px 0; font-weight: 600; color: #8B1538;">
                                                By signing below, I confirm that:
                                            </p>
                                            <ul style="margin: 0; padding-left: 20px;">
                                                <li>I have read and understood all sections of the NDA Policy</li>
                                                <li>I agree to comply with all terms and conditions</li>
                                                <li>I understand the consequences of non-compliance</li>
                                            </ul>
                                        </div>
                                        
                                        <div class="signature-section-final">
                                            <h3>Signatures</h3>
                                            <div class="signature-boxes">
                                                <div class="signature-box">
                                                    <p>Employee Signature</p>
                                                    <div class="signature-upload-area" id="signature_upload_nda_policy">
                                                        <div style="text-align: center;">
                                                            <input type="file" class="signature-upload-input signature-file-input" data-policy="nda_policy" accept="image/*">
                                                            <button class="upload-btn" onclick="$('.signature-file-input[data-policy=\'nda_policy\']').click();">
                                                                Upload Signature
                                                            </button>
                                                            <img class="signature-preview-img" src="" style="display: none;">
                                                        </div>
                                                    </div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name" id="employee_name_nda">Employee Name</div>
                                                    <div class="signature-date" id="signature_date_nda">Date: ___________</div>
                                                </div>
                                                
                                                <div class="signature-box">
                                                    <p>Company Representative</p>
                                                    <div style="height: 80px;"></div>
                                                    <div class="signature-line"></div>
                                                    <div class="signature-name">Authorized Signatory</div>
                                                    <div class="signature-date">Date: ___________</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="policy-page-number">Page {{ $totalPages + 1 }} (Final)</div>
                                        
                                        <!-- Footer -->
                                        <div class="policy-page-footer-section">
                                            <p><span class="policy-page-footer-icon">📍</span> Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
                                            <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
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

        // Acknowledgement checkbox - show signature upload section when checked
        $(document).on('change', '.policy-acknowledgement', function() {
            var policy = $(this).data('policy');
            var isChecked = $(this).is(':checked');
            var uploadSection = $('#signature_upload_' + policy);
            
            if (isChecked) {
                uploadSection.slideDown();
            } else {
                uploadSection.slideUp();
            }
        });

        // Signature file preview
        $(document).on('change', '.signature-file-input', function(e) {
            var file = e.target.files[0];
            var policy = $(this).data('policy');
            
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var previewDiv = $(this).closest('.signature-upload-section').find('.signature-preview');
                    previewDiv.find('.signature-preview-img').attr('src', e.target.result);
                    previewDiv.show();
                }.bind(this);
                reader.readAsDataURL(file);
            }
        });

        // Save signature button
        $(document).on('click', '.save-signature-btn', function() {
            var policy = $(this).data('policy');
            var userId = $('#user_filter_policies').val();
            var fileInput = $('input.signature-file-input[data-policy="' + policy + '"]');
            var file = fileInput[0].files[0];
            
            if (!userId) {
                alert('Please select a user first');
                return;
            }
            
            if (!file) {
                alert('Please select a signature image');
                return;
            }
            
            var formData = new FormData();
            formData.append('user_id', userId);
            formData.append('policy_type', policy);
            formData.append('signature', file);
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
                        
                        // Hide upload section and show display section
                        $('#signature_upload_' + policy).slideUp();
                        var displayDiv = $('#signature_display_' + policy);
                        displayDiv.find('.signature-image').attr('src', response.signature);
                        displayDiv.find('.employee-name').text(response.user_name);
                        displayDiv.find('.signature-date').text('Date: ' + response.signed_date);
                        displayDiv.slideDown();
                        
                        // Clear file input
                        fileInput.val('');
                        fileInput.closest('.signature-upload-section').find('.signature-preview').hide();
                    }
                },
                error: function() {
                    alert('Error saving signature');
                }
            });
        });

        // Download PDF button
        $(document).on('click', '.download-policy-pdf-btn', function() {
            var policy = $(this).data('policy');
            var userId = $('#user_filter_policies').val();
            
            if (!userId) {
                alert('Please select a user');
                return;
            }
            
            var url = '{{ route("policy.downloadUserPolicyPdf") }}?user_id=' + userId + '&policy_type=' + policy;
            window.open(url, '_blank');
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

        // Photo Upload Preview
        var photoFile = null;
        $('#photo_upload').change(function(e) {
            var file = e.target.files[0];
            if (file) {
                photoFile = file;
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo_img').attr('src', e.target.result);
                    $('#photo_preview').show();
                }
                reader.readAsDataURL(file);
            }
        });

        // Save Signature and Photo
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
            if (photoFile) {
                formData.append('photo', photoFile);
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
                        alert('Signature and photo saved successfully!');
                        // Refresh signatures in policy tabs if same user selected
                        if ($('#user_filter_policies').val() == selectedUser) {
                            $('#user_filter_policies').trigger('change');
                        }
                    }
                },
                error: function() {
                    alert('Error saving signature and photo');
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
