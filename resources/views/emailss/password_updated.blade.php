<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Drivvy</title>
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
            padding: 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .header {
            padding: 20px;
            text-align: left;
            position: relative;
        }
        .logo {
            font-size: 22px;
            font-weight: 600;
            color: #8bc34a; /* Light green color for the logo */
            margin-bottom: 5px;
        }
        .content {
            padding: 0 20px;
            text-align: left;
        }
        .content h1 {
            color: #000000;
            font-weight: 600;
            font-size: 22px;
            margin: 20px 0 10px 0;
        }
        .content p {
            font-size: 14px;
            color: #333;
            margin: 10px 0;
            line-height: 1.6;
        }
        .divider {
            border-top: 1px solid #e0e0e0;
            margin: 20px 0;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #333;
            background-color: #f9f9f9;
        }
        .footer p {
            margin: 5px 0;
        }
        .social-icons {
            margin-top: 10px;
        }
        .social-icons img {
            width: 18px;
            height: 18px;
            margin: 0 10px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Drivvy</div>
        </div>
        <div class="content">
           
            <p>{!! $content !!}</p>
            <div class="divider"></div>
            <p style="text-align: center; font-weight: bold;">Ready to Drivvy?</p>
        </div>
       @include('footer')
    </div>
</body>
</html>
