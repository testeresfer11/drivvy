<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('admin/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{asset('admin/vendors/jvectormap/jquery-jvectormap.css')}}">
    <link rel="stylesheet" href="{{asset('admin/vendors/flag-icon-css/css/flag-icon.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/vendors/owl-carousel-2/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/vendors/owl-carousel-2/owl.theme.default.min.css')}}">
    <!-- End plugin css for this page -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('admin/images/favicon.png')}}" />
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('admin/cstm-css/custom.css')}}">
    @yield('styles')
</head>
<body>
    @php 
        $user = '';
        if(Auth::Check())
        $user = Auth::user();
    @endphp
    <div class="container-scroller">
        @include('company.layouts.navbar')
        <div class="container-fluid page-body-wrapper">
            @include('company.layouts.header')
            <div class="main-panel">
                <div class="preloader" style = "display:none;"></div>
                <div class="content-wrapper">
                    @yield('breadcrum')
                    {{-- main section  --}}
                    @yield('content')
                    {{-- end of main section --}}
                </div>
                
                
                {{-- footer section start --}}
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                      <span class="text-muted d-block text-center text-sm-left d-sm-inline-block">Copyright © Scratchy {{date('Y')}}</span>
                    </div>
                </footer>
                {{-- end footer section --}}
            </div>
        </div>
        
    </div>
    <!-- plugins:js -->
    <script src="{{asset('admin/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{asset('admin/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{asset('admin/vendors/progressbar.js/progressbar.min.js')}}"></script>
    <script src="{{asset('admin/vendors/jvectormap/jquery-jvectormap.min.js')}}"></script>
    <script src="{{asset('admin/vendors/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>
    <script src="{{asset('admin/vendors/owl-carousel-2/owl.carousel.min.js')}}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{asset('admin/js/off-canvas.js')}}"></script>
    <script src="{{asset('admin/js/hoverable-collapse.js')}}"></script>
    <script src="{{asset('admin/js/misc.js')}}"></script>
    <script src="{{asset('admin/js/settings.js')}}"></script>
    <script src="{{asset('admin/js/todolist.js')}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
   
    <!-- 
    <script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDo8-G2Mjsart_S35hbQvCR1pe425IhPY&loading=async&callback=initMap">
</script> -->

    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/additional-methods.min.js"></script>
    <script src="{{asset('admin/cstm-js/custom.js')}}"></script>
     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDDo8-G2Mjsart_S35hbQvCR1pe425IhPY&libraries=places&callback=initAutocomplete" async></script>
        <script>
            let autocomplete;

            /* ------------------------- Initialize Autocomplete ------------------------ */
            function initAutocomplete() {
                const input = document.getElementById("address");
                const options = {
                    componentRestrictions: { country: "IN" }
                }
                autocomplete = new google.maps.places.Autocomplete(input, options);
                autocomplete.addListener("place_changed", onPlaceChange)
            }

            /* --------------------------- Handle Place Change -------------------------- */
            function onPlaceChange() {
                const place = autocomplete.getPlace();
                console.log(place.formatted_address)
                console.log(place.geometry.location.lat())
                console.log(place.geometry.location.lng())
            }
        </script>

    

    @if(session('success'))
    <script>
        toastr.success('{{ session('success') }}');
    </script>
@endif
@if(session('error'))
    <script>
        toastr.error('{{ session('error') }}');
    </script>
@endif
    
    @yield('scripts')
</body>
</html>



