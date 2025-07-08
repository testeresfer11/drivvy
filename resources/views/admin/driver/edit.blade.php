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
@section('title', 'Edit Driver')
@section('breadcrum')

<div class="page-header">
    <h3 class="page-title"> Edit Driver</h3>
</div>
@endsection
@section('content')
<div class="add-driver-form">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card rounded">
                <div class="card-body">
                  

                    <form class="forms-sample" id="Edit-User" action="{{ route('admin.driver.edit', ['id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputName">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="Name" placeholder="Name" name="name" value="{{ $user->full_name }}">
                                     @if ($errors->has('name'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputId">Email</label>
                                    <input type="email" class="form-control" id="email" placeholder="Email" name="email" value="{{ $user->email }}">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                 <div class="col-12 col-md-4">
                                    <label for="exampleInputPhoneNumber">Phone Number</label>
                                    <input type="number" class="form-control" id="exampleInputPhoneNumber" placeholder="Phone Number" name="phone_number" value="{{ $user->driverDetail->phone_number ?? 'N/A' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                               
                                {{--<div class="col-12 col-md-4">
                                    <label for="exampleInputDate">Registration Date</label>
                                    <input type="date" class="form-control" id="exampleInputDate" name="registration_date" value="{{ $user->driverDetail->registration_date ?? 'N/A' }}">
                                </div>--}}
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputAddress">Address</label>
                                    <input type="text" class="form-control" id="exampleInputAddress" placeholder="Address" name="address" value="{{ $user->driverDetail->zip_code ?? 'N/A' }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputAddress">Zip Code</label>
                                    <input type="text" class="form-control" id="exampleInputAddress" placeholder="Zip Code" name="zip_code" value="{{ $user->driverDetail->zip_code ?? 'N/A' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputTruck">Truck Number</label>
                                    <input type="text" class="form-control" id="exampleInputTruck" placeholder="Truck Number" name="truck_number" value="{{ $user->driverDetail->truck_number ?? 'N/A' }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputTruck">Total No. of Trucks</label>
                                    <input type="text" class="form-control" id="exampleInputTruck" placeholder="Total No. of Trucks" name="total_no_of_truck" value="{{ $user->driverDetail->total_no_of_truck ?? 'N/A' }}">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputAddress">Truck Type</label>
                                    <select class="form-control" name="truck_type">
                                        <option {{ $user->driverDetail->truck_type == 'Type1' ? 'selected' : '' }}>Type1</option>
                                        <option {{ $user->driverDetail->truck_type == 'Type2' ? 'selected' : '' }}>Type2</option>
                                        <option {{ $user->driverDetail->truck_type == 'Type3' ? 'selected' : '' }}>Type3</option>
                                        <option {{ $user->driverDetail->truck_type == 'Type4' ? 'selected' : '' }}>Type4</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label>License Card</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="license_card" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'licenseCardPreview')">
                                        @if ($user->driverDetail->license_card)
                                            <img id="licenseCardPreview" class="img-preview" src="{{ asset('storage/license_card/' . $user->driverDetail->license_card) }}" alt="License Card">
                                        @else
                                            <img id="licenseCardPreview" class="img-preview" src="#" alt="License Card" style="display: none;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Passport</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="passport" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'passportPreview')">
                                        @if ($user->driverDetail->passport)
                                            <img id="passportPreview" class="img-preview" src="{{ asset('storage/passport/' . $user->driverDetail->passport) }}" alt="Passport">
                                        @else
                                            <img id="passportPreview" class="img-preview" src="#" alt="Passport" style="display: none;">
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label>Vehicle Insurance</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="vehicle_insurance" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" onchange="previewImage(event, 'vehicleInsurancePreview')">
                                        @if ($user->driverDetail->vehicle_insurance)
                                            <img id="vehicleInsurancePreview" class="img-preview" src="{{ asset('storage/vehicle_insurance/' . $user->driverDetail->vehicle_insurance) }}" alt="Vehicle Insurance">
                                        @else
                                            <img id="vehicleInsurancePreview" class="img-preview" src="#" alt="Vehicle Insurance" style="display: none;">
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputVehicle">Vehicle Details</label>
                                    <input type="text" class="form-control" id="exampleInputVehicle" placeholder="Vehicle Details" name="vehicle_detail" value="{{ $user->driverDetail->vehicle_detail ?? 'N/A' }}">
                                </div>
                            </div>
                        </div>
                        <div class="driver-btns d-flex justify-content-end">
                            <button type="submit" class="btn default-btn mr-2">Update</button>
                            <button type="button" class="btn btn-dark" onclick="window.location.href='{{ route('admin.driver.list') }}'">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
@section('scripts')
<style>
    .img-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 10px;
    }
</style>
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


@stop
