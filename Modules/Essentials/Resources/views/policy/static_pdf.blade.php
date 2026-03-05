<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
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
            line-height: 1.8;
            text-align: justify;
            font-size: 12px;
            margin-bottom: 30px;
        }
        .content h2 {
            color: #8B1538;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 12px;
            border-bottom: 2px solid #8B1538;
            padding-bottom: 8px;
        }
        .content h3 {
            color: #8B1538;
            font-size: 14px;
            margin-top: 18px;
            margin-bottom: 10px;
            border-bottom: 2px solid #8B1538;
            padding-bottom: 6px;
        }
        .content h4 {
            color: #8B1538;
            font-size: 12px;
            margin-top: 15px;
            margin-bottom: 10px;
        }
        .content ul {
            margin-left: 20px;
            margin-bottom: 15px;
            line-height: 2;
        }
        .content li {
            margin-bottom: 8px;
        }
        .content p {
            margin-bottom: 12px;
            line-height: 1.8;
        }
        .content table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .content table th {
            background-color: #8B1538;
            color: white;
            padding: 12px;
            text-align: center;
            border: 1px solid #8B1538;
        }
        .content table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }
        .content table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .content div {
            margin-bottom: 20px;
        }
        .content strong {
            color: #333;
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
            <h1>{{ $title }}</h1>
        </div>

        <div class="content">
            {!! $content !!}
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p style="margin: 0;">📍 Address: 3rd Floor B5-B6 Platinum Plaza, Near Mata Mandir, Bhopal</p>
        <p style="margin: 0; font-size: 7px;">This is an official document. Generated on {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>
</body>
</html>
