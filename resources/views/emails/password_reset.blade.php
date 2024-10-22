<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Hello, {{ $username }}!</h1>
        <p>We received a request to reset your password. Please click the button below to reset it:</p>
        <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        <p>If you did not request a password reset, no further action is required.</p>
    </div>
    <div class="footer">
        <p>If you have any questions, please contact our support team.</p>
        <p>Thank you for using our application!</p>
    </div>
</body>
</html>
