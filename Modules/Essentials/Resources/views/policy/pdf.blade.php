<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $policy->title }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .letterhead {
            background: linear-gradient(to right, #8B1538 0%, #8B1538 15%, white 15%);
            padding: 20px;
            border-bottom: 3px solid #8B1538;
            margin-bottom: 30px;
        }
        .letterhead-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .company-logo {
            flex: 1;
        }
        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
        }
        .company-tagline {
            font-size: 14px;
            color: #e74c3c;
            margin: 0;
        }
        .contact-info {
            text-align: right;
            font-size: 12px;
            color: #555;
        }
        .contact-info p {
            margin: 3px 0;
        }
        .contact-icon {
            color: #e74c3c;
            margin-right: 5px;
        }
        .content-wrapper {
            padding: 0 40px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #8B1538;
        }
        .header h1 {
            margin: 0;
            color: #8B1538;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .content {
            margin: 30px 0;
            line-height: 1.8;
            text-align: justify;
        }
        .signature-section {
            margin-top: 60px;
            display: table;
            width: 100%;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
            padding: 10px;
        }
        .signature-image {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            padding: 5px;
        }
        .signature-line {
            border-top: 2px solid #333;
            margin: 10px auto;
            width: 200px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #8B1538;
            color: white;
            text-align: center;
            padding: 15px;
            font-size: 11px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 10px;
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
            padding: 5px 15px;
            background: #8B1538;
            color: white;
            border-radius: 20px;
            font-size: 12px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-content">
            <div class="company-logo">
                @php
                    $logoPath = public_path('images/logo-akalp.png');
                    $logoBase64 = null;
                    if(file_exists($logoPath)) {
                        $imageData = base64_encode(file_get_contents($logoPath));
                        $logoBase64 = 'data:image/png;base64,' . $imageData;
                    }
                @endphp
                @if($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="AKALP Logo" style="max-width: 150px; height: auto; margin-bottom: 10px;">
                @else
                    <div style="max-width: 150px; height: 80px; background: #f0f0f0; margin-bottom: 10px;"></div>
                @endif
                <h1 class="company-name">AKALP</h1>
                <p class="company-tagline">TECHNO MEDIA SOLUTIONS</p>
            </div>
            <div class="contact-info">
                <p>Web: www.akalptechnomediasolutions.com</p>
                <p>Email: akalptechnomediasolutions@gmail.com</p>
                <p>Phone: +91 8085504485, +91 9826068413</p>
            </div>
        </div>
    </div>

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
                    <div style="height: 80px;"></div>
                @endif
                <div class="signature-line"></div>
                <p>{{ $policy->user->first_name }} {{ $policy->user->last_name }}</p>
                <p style="font-size: 11px; color: #666;">Date: {{ $policy->signed_date ? \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') : '___________' }}</p>
            </div>

            <div class="signature-box">
                <p><strong>Company Representative</strong></p>
                <div style="height: 80px;"></div>
                <div class="signature-line"></div>
                <p>Authorized Signatory</p>
                <p style="font-size: 11px; color: #666;">Date: ___________</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
        <p style="margin-top: 5px; font-size: 10px;">This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}@if($policy->id) | Policy ID: {{ $policy->id }}@endif</p>
    </div>
</body>
</html>
