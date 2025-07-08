
<style>
    .img-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 10px;
    }
</style>


@extends('admin.layouts.app')

@section('title', 'Add Drivers')

@section('breadcrumb')

<div class="page-header">
    <h3 class="page-title"> Drivers</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Drivers</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Driver</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Add Driver</h4>
                    <x-alert />

                    <form class="forms-sample" id="add-user" action="{{ route('admin.driver.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputName"> Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="Name" placeholder="Name" name="name" value="{{ old('name') }}">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputEmail">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" placeholder="Email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputPhoneNumber">Phone Number</label>
                                    <input type="number" class="form-control @error('phone_number') is-invalid @enderror" id="exampleInputPhoneNumber" placeholder="Phone Number" name="phone_number" value="{{ old('phone_number') }}">
                                    @error('phone_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputAddress">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name="address" value="{{ old('address') }}">
                                    @error('address')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputZipCode">Zip code</label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="exampleInputZipCode" placeholder="Zip code" name="zip_code" value="{{ old('zip_code') }}">
                                    @error('zip_code')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputTruckNumber">Truck Number</label>
                                    <input type="text" class="form-control @error('truck_number') is-invalid @enderror" id="exampleInputTruckNumber" placeholder="Truck Number" name="truck_number" value="{{ old('truck_number') }}">
                                    @error('truck_number')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputTotalNoOfTruck">Total No. of Trucks</label>
                                    <input type="text" class="form-control @error('total_no_of_truck') is-invalid @enderror" id="exampleInputTotalNoOfTruck" placeholder="Total No. of Trucks" name="total_no_of_truck" value="{{ old('total_no_of_truck') }}">
                                    @error('total_no_of_truck')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputTruckType">Truck Type</label>
                                    <select class="form-control @error('truck_type') is-invalid @enderror" id="exampleInputTruckType" name="truck_type">
                                        <option value="">Select Truck Type</option>
                                        <option value="Type1" {{ old('truck_type') == 'Type1' ? 'selected' : '' }}>Type1</option>
                                        <option value="Type2" {{ old('truck_type') == 'Type2' ? 'selected' : '' }}>Type2</option>
                                        <option value="Type3" {{ old('truck_type') == 'Type3' ? 'selected' : '' }}>Type3</option>
                                        <option value="Type4" {{ old('truck_type') == 'Type4' ? 'selected' : '' }}>Type4</option>
                                    </select>
                                    @error('truck_type')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputLicenseCard">License Card</label>
                                    <div class="input-group col-xs-12">
                                       <input type="file" name="license_card" class="form-control file-upload-info @error('license_card') is-invalid @enderror" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'licenseCardPreview')">

                                        <img id="licenseCardPreview" class="img-preview" src="#" alt="License Card Preview" style="display: none;">
                                        @error('license_card')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputPassport">Passport</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="passport" class="form-control file-upload-info @error('passport') is-invalid @enderror" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'passportPreview')">
                                        <img id="passportPreview" class="img-preview" src="#" alt="Passport Preview" style="display: none;">
                                        @error('passport')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputVehicleInsurance">Vehicle Insurance</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="vehicle_insurance" class="form-control file-upload-info @error('vehicle_insurance') is-invalid @enderror" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'vehicleInsurancePreview')">
                                        <img id="vehicleInsurancePreview" class="img-preview" src="#" alt="Vehicle Insurance Preview" style="display: none;">
                                        @error('vehicle_insurance')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputVehicleDetails">Vehicle Details</label>
                                    <input type="text" class="form-control @error('vehicle_detail') is-invalid @enderror" id="exampleInputVehicleDetails" placeholder="Vehicle Details" name="vehicle_detail" value="{{ old('vehicle_detail') }}">
                                    @error('vehicle_detail')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputPassword">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="exampleInputPassword" placeholder="******" name="password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputConfirmPassword">Confirm Password</label>
                                    <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="exampleInputConfirmPassword" placeholder="******" name="confirm_password">
                                    @error('confirm_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>

<script>
    $(document).ready(function() {
        // Add custom validation methods if they are not already defined
        $.validator.addMethod("noSpace", function(value, element) {
            return value.trim().length !== 0;
        }, "Spaces are not allowed");

        $.validator.addMethod("passwordStrength", function(value, element) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/.test(value);
        }, "Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character");

        $("#add-user").validate({
            rules: {
                name: {
                    required: true,
                    noSpace: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true,
                    noSpace: true
                },
                phone_number: {
                    required: true,
                    number: true,
                    minlength: 8,
                    maxlength: 12
                },
                address: {
                    required: true
                },
                zip_code: {
                    required: true,
                    digits: true,
                    minlength: 5,
                    maxlength: 6
                },
                total_no_of_truck: {
                    required: true
                },
                truck_type: {
                    required: true
                },
                truck_number: {
                    required: true
                },
                license_card: {
                    required: true,
                    accept: "image/*"
                },
                passport: {
                    required: true,
                    accept: "image/*"
                },
                vehicle_insurance: {
                    required: true,
                    accept: "image/*"
                },
                vehicle_detail: {
                    required: true,
                    minlength: 5
                },
                password: {
                    required: true,
                    minlength: 6,
                    passwordStrength: true
                },
                confirm_password: {
                    required: true,
                    equalTo: "#exampleInputPassword"
                }
            },
            messages: {
                name: {
                    required: "Name is required",
                    minlength: "Name must be at least 3 characters long"
                },
                email: {
                    required: "Email is required",
                    email: "Please enter a valid email address"
                },
                phone_number: {
                    required: "Phone number is required",
                    number: "Please enter a valid phone number",
                    minlength: "Phone number must be at least 8 digits long",
                    maxlength: "Phone number cannot exceed 12 digits"
                },
                address: {
                    required: "Address is required"
                },
                zip_code: {
                    required: "Zip code is required",
                    digits: "Zip code must be digits only",
                    minlength: "Zip code must be at least 5 digits long",
                    maxlength: "Zip code cannot exceed 6 digits"
                },
                truck_number: {
                    required: "Truck number is required"
                },
                total_no_of_truck: {
                    required: "Total number of trucks is required"
                },
                truck_type: {
                    required: "Please select a truck type"
                },
                license_card: {
                    required: "License card is required",
                    accept: "Only image files are allowed"
                },
                passport: {
                    required: "Passport is required",
                    accept: "Only image files are allowed"
                },
                vehicle_insurance: {
                    required: "Vehicle insurance is required",
                    accept: "Only image files are allowed"
                },
                vehicle_detail: {
                    required: "Vehicle detail is required",
                    minlength: "Vehicle detail must be at least 5 characters long"
                },
                password: {
                    required: "Password is required",
                    minlength: "Password must be at least 6 characters long"
                },
                confirm_password: {
                    required: "Confirm password is required",
                    equalTo: "Passwords do not match"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                if (element.attr("type") == "file") {
                    error.insertAfter(element.next());
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });

    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

@endsection
