<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>
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
        .credentials {
            font-weight: bold;
            color: #333333;
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
        <h1>Account Details</h1>
    </div>
    <div class="content">
        <p>Dear {{ $name }},</p>
        <p>Your account has been created/updated. Below are your new account details:</p>
        <p class="credentials">Email: {{ $email }}</p>
        <p class="credentials">Password: {{ $password }}</p>
        <p>Please make sure to change this password after logging in to keep your account secure.</p>
    </div>
    <div class="footer">
        <p>If you did not request this change, please contact our support team immediately.</p>
        <p>Thank you,</p>
        <p>The {{ config('app.name') }} Team</p>
    </div>
</div>
</body>
</html>
