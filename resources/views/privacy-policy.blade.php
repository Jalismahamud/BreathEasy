<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            padding: 32px 24px;
        }
        h1 {
            color: #007aff;
            font-size: 2rem;
            margin-bottom: 24px;
            text-align: center;
        }
        .policy-content {
            color: #222;
            font-size: 1.05rem;
            line-height: 1.7;
        }
        @media (max-width: 600px) {
            .container {
                padding: 16px 6px;
            }
            h1 {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Privacy Policy</h1>
        <div class="policy-content">
            {!! $content !!}
        </div>
    </div>
</body>
</html>
