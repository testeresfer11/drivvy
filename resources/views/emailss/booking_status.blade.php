<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'confirmed' ? 'Booking Confirmed' : 'Booking Rejected' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .content h2 {
            color: #4CAF50;
            font-size: 24px;
            margin: 0;
        }
        .content p {
            font-size: 16px;
            margin: 10px 0;
        }
        .footer {
            background-color: #f4f4f4;
            padding: 10px;
            text-align: center;
        }
        .footer p {
            margin: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $status === 'confirmed' ? 'Booking Confirmed' : 'Booking Rejected' }}</h1>
        </div>
        <div class="content">
            <h2>{{ $status === 'confirmed' ? 'Your Booking is Confirmed!' : 'Your Booking has been Rejected.' }}</h2>
            <p>Hello Passanger,</p>
            <p>
                {{ $status === 'confirmed' ? 'Your booking for the ride from ' . $booking->departure_location . ' to ' . $booking->arrival_location . ' has been confirmed.' : 'Unfortunately, your booking request for the ride from ' . $booking->departure_location . ' to ' . $booking->arrival_location . ' has been rejected.' }}
            </p>
            <p>
                If you have any questions or need further assistance, please feel free to contact us.
            </p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>Drivvy Team</p>
        </div>
    </div>
</body>
</html>
