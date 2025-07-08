<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>passenger-book</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Outfit", sans-serif;
        }

    </style>
</head>

<body>
    <div class="welcome-email">
        <div class="container" style="max-width: 500px; margin:0 auto">
            <table style="width: 100%;">
                <thead>
                    <tr>
                        <th style="text-align: left;">
                            <img src="{{ asset('images/Drivvy_Logo.png') }}" alt="Drivvy Logo" style="height: 50px;">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="margin-top: 20px; display: block;">
                        <td>
                            <h1 style="color: #231f20;  font-weight: 600;">A passenger has booked  your ride.</h1>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <div class="booking" style="margin:25px 0;">
                                <p style="font-size: 18px; color: #808080;margin: 0;font-weight: 400;">A passenger wants to book your ride.
                                    Please, go to your app under "Your Rides". Accept or decline booking request before {{$formattedAdjustedTime}}.</p>
                               
                            </div>
                           

                        </td>
                    </tr>
                     <td>
                            <p style="font-size: 18px; color: #808080;">    {{ \Carbon\Carbon::parse($booking->booking_date)->format('l, d F Y') }}
</p>
                        </td>
                    <tr>
                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 0 auto;">
                                <!-- First Location -->
                            <tr>
                                <td style="padding: 0px 0; text-align: left;">
                                    <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="width: 20px; text-align: center; vertical-align: top;">
                                                <!-- Circle -->
                                                <div style="width: 10px; height: 10px; border: 2px solid #ddd; border-radius: 50%; background-color: #fff; margin: 0 auto;"></div>
                                                <!-- Line -->
                                                <div style="width: 2px; height: 80px; background-color: #ddd; margin: 0 auto;"></div>
                                            </td>
                                            <td style="padding-left: 10px;vertical-align: top;">
                                                <span style="margin: 0; color: #666666; font-size: 18px; font-weight: 600;">{{ $ride->departure_city }}</span>
                                                <br>
                                                <span style="font-size: 12px; color: #888;">{{ $booking->departure_location }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                                <!-- Second Location -->
                                <tr>
                                    <td style="padding: 0px 0; text-align: left;">
                                        <table role="presentation" style="width: 100%; border-collapse: collapse;">
                                            <tr>
                                                <td style="width: 20px; text-align: center; vertical-align: top;">
                                                    

                                                    <!-- Circle -->
                                                    <div style="width: 10px; height: 10px; border: 2px solid #ddd; border-radius: 50%; background-color: #fff; margin: 0 auto;"></div>
                                                    <!-- Line -->
                                                    <div style="width: 2px; height: 100%; background-color: #ddd; margin: 0 auto;"></div>
                                                </td>

                                                <td style="padding-left: 10px;vertical-align: top;">
                                                    <span style="margin: 0; color: #666666; font-size: 18px; font-weight: 600;">{{ $ride->arrival_city }}</span>
                                                    <br>
                                                    <span style="font-size: 12px; color: #888;">{{ $booking->arrival_location }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                           
                        </table>
                    </tr>
                    
                    <table style="width: 100%;">
                        
                          <tr>
                    <td style="text-align: center;">
                        <h2 style="font-weight: 600; color: #231f20; border-top: 1px solid #231f20; padding-top: 60px; padding-bottom: 0px;">Ready to Drivvy?</h2>
                    </td>
                </tr>
                <tr>
                     <td style="text-align:center; padding-bottom: 20px;">
                            <div class="copy-right">
                                <span><a href="https://apps.apple.com/au/app/drivvy/id6738778933" target="_blank" aria-label="Facebook" style="text-decoration: none;">
                                    <img src="{{ asset('admin/images/app.png') }}" alt="Facebook" style="width:135px;" />
                                </a></span>
                                <span><a href="https://play.google.com/store/apps/details?id=com.taxi.drivvyCar" target="_blank" aria-label="Facebook" style="text-decoration: none; padding-left: 10px;">
                                    <img src="{{ asset('admin/images/google.png') }}" alt="Facebook" style="width:135px;" />
                                </a></span>
                            </div>
                        </td>
                </tr>
                <tr>
                    <td style="text-align:center;">
                        <div class="copy-right">
                            <span style="vertical-align: middle; padding-right: 5px; font-size:19px; color: #808080;">&#169;</span>
                            <span style="color:#808080;">2024 Drivvy</span>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <td style="text-align: center; padding-top: 15px; padding-bottom: 15px;">
                        <a href="https://www.facebook.com/share/KuefSgBKeuRDRCtn/" target="_blank" aria-label="Facebook" style="text-decoration: none;">
                            <img src="{{ asset('admin/images/Facebook_Icon.png') }}" alt="Facebook" style="width:25px;" />
                        </a>
                        <a href="https://www.instagram.com/drivvy.australia?igsh=aGN5YmgzbjAwYmZq&utm_source=qr" target="_blank" aria-label="Instagram" style="text-decoration: none;">
                            <img src="{{ asset('admin/images/Instagram_Icon.png') }}" alt="Instagram" style="padding: 0 7px; width:25px" />
                        </a>
                         <a href="https://www.linkedin.com/company/drivvy-australia/" target="_blank" aria-label="LinkedIn" style="text-decoration: none;">
                            <img src="{{ asset('admin/images/LinkedIn_Icon.png') }}" alt="LinkedIn" style="width:25px;" />
                        </a>
                    </td>
                </tr>
                    </table>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>