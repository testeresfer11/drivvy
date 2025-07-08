<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{asset('admin/vendors/mdi/css/materialdesignicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('admin/vendors/css/vendor.bundle.base.css')}}">
    <!-- endinject -->

    <link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <style>
        label.error {
        color: #db7373;
        position: relative;
        padding-top: 0;
        bottom: 12px;
    }
    </style>
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{asset('images/carpool_logo.png')}}" />
</head>
<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper login-page full-page-wrapper d-flex align-items-center auth login-bg">
                <div class="row w-100 m-0">
                    <div class="col-12 col-md-6">
                        <div class="login-left-icon">
                            <img src="{{asset('images/carpool_logo.png')}}">
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="card">
                            <div class="card-body px-5">
                                <div class="login-title d-flex align-items-center justify-content-between">
                                    <p class="f-18">Welcome to <span class="dark bold">Drivvy</span></p>
                                </div>
                                <h2 class="pb-4 f-38">Sign In</h2>
                                <form action="{{ route('login') }}" method="POST" id="loginForm">
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">{{ __('Email Address') }} *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">{{ __('Password') }} *</label>
                                        <input name="password" id="password" type="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    {{--<div class="form-group d-flex align-items-center justify-content-end">
                                        <div class="forgot"> <a href="/" class="forgot-pass dark text-decoration-none">{{ __('Forgot Password?') }}</a></div>
                                    </div>--}}
                                     
                                    <div class="text-center">
                                        <button type="submit" class="btn default-btn btn-md w-100">{{ __('Login') }}</button>
                                    </div>   
                                         
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- content-wrapper ends -->
            </div>
            <!-- row ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="{{asset('admin/vendors/js/vendor.bundle.base.js')}}"></script>
    <!-- endinject -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <script>
        @if (session('status'))
            toastr.success("{{ session('status') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif

        $(document).ready(function () {
            $('#loginForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                },
                messages: {
                    email: {
                        required: 'Please enter Email Address.',
                        email: 'Please enter a valid Email Address.',
                    },
                    password: {
                        required: 'Please enter Password.',
                        minlength: 'Password must be at least 8 characters long.',
                    },
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>
      