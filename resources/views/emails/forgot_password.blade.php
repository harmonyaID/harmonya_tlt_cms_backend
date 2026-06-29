<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #ffffff;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .header {
            text-align: center;
            padding: 10px 0;
        }
        .header h1 {
            color: #333333;
        }
        .content {
            margin: 20px 0;
            line-height: 1.6;
        }
        .content p {
            color: #555555;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            font-size: 16px;
            color: #ffffff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            color: #aaaaaa;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Password Reset Request</h1>
    </div>
    <div class="content">
        <p>Dear {{ $name }},</p>
        <p>We received a request to reset your password. Click the button below to reset your password:</p>
        <a href="{{ $resetLink }}" class="button">Reset Password</a>
        <p>If you did not request a password reset, please ignore this email or contact support if you have questions.</p>
    </div>
    <div class="footer">
        <p>Thank you,</p>
        <p>The {{ config('app.name') }} Team</p>
    </div>
</div>
</body>
</html>
