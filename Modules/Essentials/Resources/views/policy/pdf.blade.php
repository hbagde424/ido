<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $policy->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .content {
            margin: 30px 0;
            line-height: 1.6;
        }
        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 10px;
        }
        .signature-image {
            max-width: 150px;
            max-height: 100px;
            margin-bottom: 10px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 30%;
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $policy->title }}</h1>
        <p><strong>Policy Type:</strong> {{ $policy->getPolicyTypeLabel() }}</p>
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
            @if($policy->signature_photo)
                <img src="{{ public_path('uploads/policy_signatures/' . $policy->signature_photo) }}" alt="Signature" class="signature-image">
            @else
                <p style="color: #999;">No signature provided</p>
            @endif
            <p>{{ $policy->user->first_name }} {{ $policy->user->last_name }}</p>
            <p style="font-size: 12px; color: #666;">Date: {{ $policy->signed_date ? \Carbon\Carbon::parse($policy->signed_date)->format('d-m-Y') : '___________' }}</p>
        </div>

        <div class="signature-box">
            <p><strong>Company Representative</strong></p>
            <p style="height: 80px;"></p>
            <p>___________________________</p>
            <p style="font-size: 12px; color: #666;">Date: ___________</p>
        </div>
    </div>

    <div class="footer">
        <p>This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
        <p>Policy ID: {{ $policy->id }}</p>
    </div>
</body>
</html>
