<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <style>
        body {
            font-family: "Figtree", sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: green;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            border-bottom: 4px solid #685b5bdd; /* Accent color */
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .content {
            padding: 20px;
        }
        .content h1 {
            color: green; /* Primary color */
            font-weight: 600;
            font-size: 24px;
            margin-top: 0;
            margin-bottom: 20px;
        }
        .content p {
            font-size: 16px;
            color: #555555;
            line-height: 1.6;
            margin: 20px 0;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #dddddd;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
            color: #333333;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your email verification code</h1>
        </div>
        <div class="content">

            <p>Your email verification code is:<strong>{{ $otp }}</strong></p>
            <p>Please, use this code to verify your email address.</p>
            <p>This code is valid for 10 minutes.</p>
             <div class="footer">
            If you did not request this email verification code, please ignore this email.
        </div>
        </div>
        @include('footer')
    </div>
</body>
</html>
