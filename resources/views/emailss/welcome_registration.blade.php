<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Document</title>
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
                       <th style="font-size: 42px;color: #7dbf43;text-align: left;font-weight: 600;">Drivvy</th>
                   </tr>
               </thead>
               <tbody>
                   <tr style="margin-top: 20px; display: block;">
                       <td>
                           <h1 style="color: #231f20;line-height: 57px;  font-weight: 600;">Welcome to Drivvy!</h1>
                       </td>
                   </tr>
                   <tr>
                       <td>
                           <p style="font-size: 18px; color: #808080;line-height: 26px;">Thank you for registering with
                               Drivvy!<br>
                               We’re excited to have you join our community<br>
                               of drivers and riders across Australia.</p>
                       </td>
                   </tr>
                   <tr>
                       <td>
                           <p style="font-size: 18px; color: #808080;line-height: 26px;">Are you ready to Drivvy?<br>
                               Publish or search your first ride.</p>
                       </td>
                   </tr>
                   <tr style="width: 100%;">
                       <td style="text-align: center;">
                           <h2
                               style=" font-weight: 600;  color: #231f20;border-top: 1px solid #dee2e6;padding-top: 40px;padding-bottom: 50px;">
                               Ready to Drivvy?</h2>
                       </td>
                   </tr>
                   <tr>
                       <td style="text-align:center;">
                           <div class="copy-right">
                               <span style="vertical-align: middle;padding-right: 5px;"><img
                                       src="{{ asset('admin/images/copyright.png')}}"></span><span style="color:#808080 ;">2024
                                   Drivvy</span>
                           </div>
                       </td>
                   </tr>
                   <tr>
                       <td style="text-align: center; padding-top: 15px;padding-bottom: 15px;">
                           <a href="https://www.facebook.com/share/KuefSgBKeuRDRCtn/" target="_blank" aria-label="Facebook" class="fb-icon">
                               <img src="{{ asset('admin/images/facebook.png') }}" alt="Facebook" class="social-icon-img" />
                           </a>
                           <a href="https://www.instagram.com/drivvy.australia?igsh=aGN5YmgzbjAwYmZq&utm_source=qr" target="_blank" aria-label="Instagram" class="insta-icon">
                               <img src="{{ asset('admin/images/instagram.png') }}" alt="Instagram" class="social-icon-img"style="padding: 0 7px;" />
                           </a>
                           <a href="linkedin.com/in/drivvy-australia" target="_blank" aria-label="LinkedIn" class="linkedin-icon">
                               <img src="{{ asset('admin/images/linkedin-logo.png') }}" alt="LinkedIn" class="social-icon-img" />
                           </a>
                           <!-- <span><img src="images/facebook.png"></span>
                           <span style="padding: 0 7px;"><img src="images/instagram.png"></span>
                           <span><img src="images/linkedin-logo.png"></span> -->
                       </td>
                   </tr>
               </tbody>
           </table>
       </div>
   </div>
</body>

</html>