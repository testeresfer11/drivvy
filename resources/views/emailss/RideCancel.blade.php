<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cancle</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" integrity="sha512-dPXYcDub/aeb08c63jRq/k6GaKccl256JQy/AnOq7CAnEZ9FzSL9wSbcZkMp4R26vBsMLFYH4kQ67/bbV8XaCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /*-------------------- global css start here --------------------*/

@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap');
body {

    font-family: "Outfit", sans-serif;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/*-------------------- global css end here --------------------*/




/*-------------------- header css start here --------------------*/
.gray-light {
    color: #666666;
}
.color-80 {
    color: #808080;
}
.f-bold {
    font-weight: 600;
}
.logo-color{
    color: #7dbf43;
}
.circle {
    width: 10px;
    height: 10px;
    display: block;
    border-radius: 50%;
    border: 2px solid #ddd;
}
.melb-text,.charle-text {
    display: flex;
    gap: 15px;
    align-items: baseline;
}
.melb-text span.circle:after {
    content: "";
    width: 2px;
    height: 58px;
    background: #ddd;
    position: absolute;
    top: 8px;
    left: 2px;
}

.melb-text span.circle {
    position: relative;
}

.green-circle span {
    background: #7dbf43;
    display: block;
    width: 60px;
    height: 60px;
    border-radius: 50%;
}
.michael-text {
    display: flex;
    gap: 100px;
    align-items: center;
}

    </style>
  </head>
  <body>
    <div class="main-container">
      <!------------------header start here----------------->
      <div class="header-bg">
      <div class="container px-0">
        <div class="banner-content container">
          <div class="row">
            <div class="col-lg-7">
              <div class="divvy-title mt-5">
                <h1 class="logo-color fw-700">Drivvy</h1>
              </div>
             
            </div>
          </div>
          <div class="welcome-text ">
            <div class="row">
              <div class="col-lg-12">
                <div class="welcome-title my-5">
                  <h1>Your ride has been<br> successfully cancelled.<br> We refunded you.</h1>
                  <div class="date-time mt-5">
                    <p class="color-80 f-20">{{ \Carbon\Carbon::parse($booking->created_at)->format('l, d F Y \a\t h.iA') }}</p>

                  </div>
                </div>
                <div class="airport-text pb-5">
                  <div class="melb-text mb-4">
                    <div class="c-span">
                      <span class="circle"></span>
                    </div>
                    <div class="air-text">
                       <h5 class="mb-0">{{ $ride->departure_city }}</h5>
                    </div>
                    
                  </div>
                  <div class="charle-text">
                    <div class="c-span">
                      <span class="circle"></span>
                    </div>
                    <div class="air-text">
                      <h5 class="mb-0">{{$ride->arrival_city }}</h5>
                    </div>
                    
                  </div>
                  <div class="michael-text mt-5">
                    <h5 class="mb-0">{{$driver->first_name}}</h5>
                    <div class="green-circle">
                        <span></span>
                    </div>
                  </div>
                  
                </div>
                <div class="total-text text-end d-flex gap-4 justify-content-end">
                    <p class="color-80 mb-0">Full refund</p>
                    <h5 class=" mb-0 color-80 f-bold">{{$refundAmount}}</h5>
                  </div>
               <div class="ready-drivvy py-5 border-bottom ">
                <h5 class="mb-0 color-80">Find a new available ride in the Drivvy app.</h5>
             
               </div>
               <div class="ready-drive text-center my-5 py-5">
                <h4 class="f-bold">Ready to Drivvy?</h4>
               </div>
              </div>
            </div>
          </div>
          
        </div>
      </div>
      @include('footer')
    </div>
     
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>