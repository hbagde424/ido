<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
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
            padding: 0 40px 100px 40px;
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
        .content {
            margin: 30px 0;
            line-height: 1.8;
            text-align: justify;
        }
        .content h2 {
            color: #8B1538;
            border-bottom: 2px solid #8B1538;
            padding-bottom: 10px;
            margin-top: 30px;
            font-size: 20px;
        }
        .content h3 {
            color: #2c3e50;
            margin-top: 25px;
            font-size: 16px;
        }
        .content h4 {
            color: #34495e;
            margin-top: 20px;
            font-size: 14px;
        }
        .content ul {
            margin-left: 20px;
            margin-bottom: 15px;
        }
        .content li {
            margin-bottom: 8px;
        }
        .content p {
            margin-bottom: 15px;
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
                <h1 class="company-name">AKALP</h1>
                <p class="company-tagline">TECHNO MEDIA SOLUTIONS</p>
            </div>
            <div class="contact-info">
                <p><span class="contact-icon">🌐</span> www.akalptechnomediasolutions.com</p>
                <p><span class="contact-icon">✉</span> akalptechnomediasolutions@gmail.com</p>
                <p><span class="contact-icon">📞</span> +91 8085504485, +91 9826068413</p>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="header">
            <h1>{{ $title }}</h1>
            <span class="policy-badge">Official Company Policy</span>
        </div>

        <div class="content">
            {!! $content !!}
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>📍 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
        <p style="margin-top: 5px; font-size: 10px;">This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
