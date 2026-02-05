<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $policy->title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }
        @page {
            margin: 0;
            padding: 0;
        }
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .letterhead {
            background: white;
            padding: 8px 20px;
            border-bottom: 3px solid #8B1538;
            flex-shrink: 0;
        }
        .letterhead-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 15px;
        }
        .company-logo {
            flex: 0 0 auto;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            line-height: 1;
        }
        .company-tagline {
            font-size: 9px;
            color: #e74c3c;
            margin: 0;
            line-height: 1;
        }
        .contact-info {
            text-align: right;
            font-size: 9px;
            color: #555;
            line-height: 1.3;
            flex: 1;
        }
        .contact-info p {
            margin: 0;
            padding: 0;
        }
        .content-wrapper {
            flex: 1;
            padding: 15px 25px 80px 25px;
            overflow-y: auto;
        }
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #8B1538;
        }
        .header h1 {
            margin: 0;
            color: #8B1538;
            font-size: 18px;
            line-height: 1.2;
        }
        .header p {
            margin: 2px 0;
            color: #666;
            font-size: 11px;
        }
        .content {
            margin: 15px 0;
            line-height: 1.6;
            text-align: justify;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .info-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
            color: #8B1538;
        }
        .policy-badge {
            display: inline-block;
            padding: 3px 10px;
            background: #8B1538;
            color: white;
            border-radius: 15px;
            font-size: 10px;
            margin: 5px 0;
        }
        .signature-section {
            margin-top: 30px;
            display: table;
            width: 100%;
            font-size: 11px;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 8px;
            border: 1px solid #ddd;
            padding: 3px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin: 8px auto;
            width: 150px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #8B1538;
            color: white;
            text-align: center;
            padding: 5px 10px;
            font-size: 8px;
            line-height: 1.3;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-content">
            <div class="company-logo">
                @php
                    $svgPath = public_path('images/logo-akalp.svg');
                    $svgBase64 = null;
                    if(file_exists($svgPath)) {
                        $svgContent = file_get_contents($svgPath);
                        $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
                    }
                @endphp
                @if($svgBase64)
                    <img src="{{ $svgBase64 }}" alt="AKALP Logo" style="max-width: 100px; height: auto; margin-bottom: 5px;">
                @endif
                <h1 class="company-name">AKALP</h1>
                <p class="company-tagline">TECHNO MEDIA SOLUTIONS</p>
            </div>
            <div class="contact-info">
                <p><strong>Web:</strong> www.akalptechnomediasolutions.com</p>
                <p><strong>Email:</strong> akalptechnomediasolutions@gmail.com</p>
                <p><strong>Phone:</strong> +91 8085504485, +91 9826068413</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="header">
            <h1>{{ $policy->title }}</h1>
            <span class="policy-badge">{{ $policy->getPolicyTypeLabel() }}</span>
            <p><strong>Status:</strong> {{ $policy->getStatusLabel() }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>Employee Name:</td>
                <td>{{ $policy->user->first_name }} {{ $policy->user->last_name }}</td>
            </tr>
            <tr>
                <td>Employee ID:</td>
                <td>{{ $policy->user->id }}</td>
            </tr>
            <tr>
                <td>Document Date:</td>
                <td>{{ $policy->created_at->format('d-m-Y') }}</td>
            </tr>
            @if($policy->signed_date)
            <tr>
                <td>Signed Date:</td>
                <td>{{ \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="content">
            {!! $policy->content !!}
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Employee Signature</strong></p>
                @php
                    $signaturePath = null;
                    if($policy->user && $policy->user->signature_photo) {
                        $signaturePath = public_path('uploads/user_signatures/' . $policy->user->signature_photo);
                    } elseif($policy->signature_photo) {
                        $signaturePath = public_path('uploads/policy_signatures/' . $policy->signature_photo);
                    }
                    
                    $signatureBase64 = null;
                    if($signaturePath && file_exists($signaturePath)) {
                        $imageData = base64_encode(file_get_contents($signaturePath));
                        $imageType = pathinfo($signaturePath, PATHINFO_EXTENSION);
                        $signatureBase64 = 'data:image/' . $imageType . ';base64,' . $imageData;
                    }
                @endphp
                @if($signatureBase64)
                    <img src="{{ $signatureBase64 }}" alt="Signature" class="signature-image">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div class="signature-line"></div>
                <p>{{ $policy->user->first_name }} {{ $policy->user->last_name }}</p>
                <p style="font-size: 9px; color: #666;">Date: {{ $policy->signed_date ? \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') : '___________' }}</p>
            </div>

            <div class="signature-box">
                <p><strong>Company Representative</strong></p>
                <div style="height: 60px;"></div>
                <div class="signature-line"></div>
                <p>Authorized Signatory</p>
                <p style="font-size: 9px; color: #666;">Date: ___________</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0;">📍 Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
        <p style="margin: 0; font-size: 7px;">This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}@if($policy->id) | Policy ID: {{ $policy->id }}@endif</p>
    </div>
</body>
</html>
