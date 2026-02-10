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
            display: table;
            width: 100%;
            height: 120px;
        }
        .company-logo {
            display: table-cell;
            vertical-align: middle;
            width: 150px;
        }
        .logo-image {
            display: inline-block;
        }
        .logo-image img {
            max-width: 120px;
            height: auto;
            display: block;
        }
        .logo-text {
            font-size: 24px;
            font-weight: bold;
            color: #8B1538;
            display: block;
        }
        .contact-info {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            font-size: 11px;
            color: #555;
            padding-right: 10px;
        }
.contact-info p { 
    margin: 5px 0;
    padding: 0;
    font-size: 11px;
    line-height: 1.6;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}

.contact-info img {
    width: 7px;
    height: 7px;
    margin-right: 8px;
    margin-top: -5px;
}

        .content-wrapper {
            flex: 1;
            padding: 10px 25px 10px 25px;
            overflow-y: auto;
            position: relative;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 1px solid #8B1538;
        }
        .header h1 {
            margin: 0;
            color: #8B1538;
            font-size: 18px;
            line-height: 1.2;
        }
        .header p {
            margin: 1px 0;
            color: #666;
            font-size: 11px;
        }
        .content {
            margin: 10px 0;
            line-height: 1.6;
            text-align: justify;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 10px;
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
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #8B1538;
            display: table;
            width: 100%;
            font-size: 11px;
            page-break-inside: avoid;
        }
        .signature-box {
            display: table-cell;
            width: 45%;
            text-align: center;
            vertical-align: top;
            padding: 8px;
        }
        .signature-image {
            max-width: 150px;
            max-height: 60px;
            margin-bottom: 6px;
            border: 1px solid #ddd;
            padding: 3px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin: 6px auto;
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
            padding: 4px 10px;
            font-size: 8px;
            line-height: 1.2;
            flex-shrink: 0;
        }
    </style>
</head>
<body>
    <!-- Letterhead -->
    <div class="letterhead">
        <div class="letterhead-content">
            <div class="company-logo">
                <div class="logo-image">
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
                    @endif
                </div>
            </div>
            <div class="contact-info">
                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48Y2lyY2xlIGN4PSIxMiIgY3k9IjEyIiByPSIxMCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiLz48cGF0aCBkPSJNMTIgNlYxMkwxNiAxNCIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjEuNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PC9zdmc+" alt="web"> <span>www.akalptechnomediasolutions.com</span></p>
                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB4PSIyIiB5PSI0IiB3aWR0aD0iMjAiIGhlaWdodD0iMTYiIHJ4PSIyIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxwYXRoIGQ9Ik0yIDZMMTIgMTNMMjIgNiIgc3Ryb2tlPSIjOEIxNTM4IiBzdHJva2Utd2lkdGg9IjIiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIgc3Ryb2tlLWxpbmVqb2luPSJyb3VuZCIvPjwvc3ZnPg==" alt="email"> <span>akalptechnomediasolutions@gmail.com</span></p>
                <p><img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAiIGhlaWdodD0iMTAiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cGF0aCBkPSJNMyA5QzMgNS4xMzQwMSA2LjEzNDAxIDIgMTAgMkMxMy44NjYgMiAxNyA1LjEzNDAxIDE3IDlDMTcgMTQgMTAgMjIgMTAgMjJTMyAxNCAzIDlaIiBzdHJva2U9IiM4QjE1MzgiIHN0cm9rZS13aWR0aD0iMiIvPjxjaXJjbGUgY3g9IjEwIiBjeT0iOSIgcj0iMiIgZmlsbD0iIzhCMTUzOCIvPjwvc3ZnPg==" alt="phone"> <span>+91 8085504485, +91 9826068413</span></p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-wrapper">
        <div class="header">
            <h1>{{ $policy->title ?? 'Policy Document' }}</h1>
            <span class="policy-badge">{{ isset($policy->policy_type) ? \Modules\Essentials\Entities\EssentialsPolicy::$policy_types[$policy->policy_type] ?? $policy->policy_type : 'Policy' }}</span>
            <p><strong>Status:</strong> {{ isset($policy->status) ? \Modules\Essentials\Entities\EssentialsPolicy::$statuses[$policy->status] ?? $policy->status : 'Pending' }}</p>
        </div>

        <table class="info-table">
            <tr>
                <td>Employee Name:</td>
                <td>{{ $policy->user->first_name ?? 'N/A' }} {{ $policy->user->last_name ?? '' }}</td>
            </tr>
            <tr>
                <td>Employee ID:</td>
                <td>{{ $policy->user->id ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td>Document Date:</td>
                <td>{{ isset($policy->created_at) ? $policy->created_at->format('d-m-Y') : date('d-m-Y') }}</td>
            </tr>
            @if(isset($policy->signed_date) && $policy->signed_date)
            <tr>
                <td>Signed Date:</td>
                <td>{{ \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') }}</td>
            </tr>
            @endif
        </table>

        <div class="content">
            {!! $policy->content !!}
        </div>

        <!-- Acknowledgement Section -->
        <div style="margin-top: 30px; padding: 12px; background: #f5f5f5; border-left: 4px solid #8B1538; page-break-inside: avoid;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 25px; vertical-align: top; padding-right: 10px; padding-top: 1px;">
                        <div style="width: 18px; height: 18px; border: 2px solid #8B1538; display: inline-block; background: white; position: relative; box-sizing: border-box;">
                            <div style="position: absolute; top: 2px; left: 4px; width: 10px; height: 6px; border-left: 2px solid #8B1538; border-bottom: 2px solid #8B1538; transform: rotate(-45deg);"></div>
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <p style="margin: 0; font-size: 11px; font-weight: 600; color: #333;">
                            I acknowledge that I have read and understood this policy document and agree to comply with all its terms and conditions.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p><strong>Employee Signature</strong></p>
                @php
                    $signaturePath = null;
                    if($policy->user && $policy->user->signature_photo) {
                        $signaturePath = public_path('uploads/user_signatures/' . $policy->user->signature_photo);
                    } elseif(isset($policy->signature_photo) && $policy->signature_photo) {
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
                <p>{{ $policy->user->first_name ?? 'Employee' }} {{ $policy->user->last_name ?? '' }}</p>
                <p style="font-size: 9px; color: #666;">Date: {{ isset($policy->signed_date) && $policy->signed_date ? \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') : '___________' }}</p>
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
