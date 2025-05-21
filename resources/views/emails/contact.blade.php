<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Message</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            color: #ffffff;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            margin-top: 50px;
            
            background-color: #1b263b;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .header {
            background-color: #1e6091;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h2 {
            font-size: 24px;
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
            margin: 0 0 10px;
        }
        .content strong {
            color: #38bdf8;
        }
        .footer {
            background-color: #0d1b2a;
            text-align: center;
            padding: 10px;
            font-size: 12px;
            color: #9ca3af;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>New Message from {{ $contactData['name'] }}</h2>
        </div>
        <div class="content">
            <p><strong>Email:</strong> {{ $contactData['email'] }}</p>
            <p><strong>Subject:</strong> {{ $contactData['subject'] }}</p>
            <p><strong>Message:</strong></p>
            <p>{{ $contactData['message'] }}</p>
        </div>
        <div class="footer">
            <p>Thank you for reaching out to us.</p>
        </div>
    </div>
</body>
</html>
