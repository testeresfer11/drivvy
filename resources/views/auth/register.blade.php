@extends('layouts.app')

<link rel="stylesheet" href="{{asset('admin/css/style.css')}}">
@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper login-page full-page-wrapper d-flex align-items-center auth login-bg">
            <div class="row w-100 m-0 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="login-left-icon">
                        <img src="{{asset('admin/images/truck.png')}}">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="card">
                        <div class="card-body px-5">
                            <div class="login-title d-flex align-items-center justify-content-between">
                                <p class="f-18">Welcome to <span class="dark bold">LOREM</span></p>
                                <p class="f-13">
                                    <span class="d-block">Already have an account ?</span>
                                    <span class="d-block"><a href="{{route('login')}}" class="dark text-decoration-none">Sign in</a></span>
                                </p>
                            </div>
                            <h2 class="pb-4 f-38">Sign up</h2>
                            <p class="details f-16 semi-bold">Company Details</p>
                            <x-alert />
                            <form action="{{ route('register') }}" method="POST" id="registerForm">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Company Name*</label>
                                    <input type="text" class="c-name form-control @error('name') is-invalid @enderror" placeholder="Company Name" name="name" id="name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone_number">Phone Number</label>
                                    <input type="text" class="c-name form-control @error('phone_number') is-invalid @enderror" placeholder="Phone Number" name="phone_number" id="phone_number">
                                    @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="c-name form-control @error('email') is-invalid @enderror" placeholder="Email" name="email" id="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="license_id">License ID</label>
                                    <input type="text" class="c-name form-control @error('license_id') is-invalid @enderror" placeholder="License ID" name="license_id" id="license_id">
                                    @error('license_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="insurance_id">Insurance ID</label>
                                    <input type="text" class="c-name form-control @error('insurance_id') is-invalid @enderror" placeholder="Insurance ID" name="insurance_id" id="insurance_id">
                                    @error('insurance_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="c-name form-control @error('password') is-invalid @enderror" placeholder="Password" name="password" id="password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn default-btn btn-md w-100">Sign Up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>

<script>
    $(document).ready(function() {
        // Validate signup form
        $('#registerForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                phone_number: {
                    required: true,
                    digits: true,
                    minlength: 10
                },
                email: {
                    required: true,
                    email: true
                },
                license_id: {
                    required: true
                },
                insurance_id: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 8
                }
            },
            messages: {
                name: {
                    required: "Company name is required.",
                    minlength: "Company name must be at least 3 characters long."
                },
                phone_number: {
                    required: "Phone number is required.",
                    digits: "Please enter only digits.",
                    minlength: "Phone number must be at least 10 digits long."
                },
                email: {
                    required: "Email address is required.",
                    email: "Please enter a valid email address."
                },
                license_id: {
                    required: "License ID is required."
                },
                insurance_id: {
                    required: "Insurance ID is required."
                },
                password: {
                    required: "Password is required.",
                    minlength: "Password must be at least 8 characters long."
                }
            }
        });
    });
</script>
@endsection
