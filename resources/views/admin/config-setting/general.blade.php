@extends('admin.layouts.app')

@section('title', 'General setting')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title"> General setting</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">General setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">General setting</li>
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
                    <h4 class="card-title"><u>General setting</u></h4>
                    <x-alert />

                    <form class="forms-sample" id="edit-user" action="{{ route('admin.settings.general')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">Site Name</label>
                                    <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Enter Site Name" name="site_name" @if(!empty($general)) value="{{ old('site_name', $general->site_name) }}" @endif>
                                    @error('site_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="email" @if(!empty($general)) value="{{ old('email', $general->email) }}" @endif>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6 input-phone">
                                    <label for="exampleInputPhoneNumber">Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i id="flag-icon" class="flag-icon flag-icon-us"></i> <!-- Default flag is US -->
                                            </div>
                                        </div>
                                        <input type="tel" id="phone" name="phone_number" @if(!empty($general)) value="{{ old('phone_number', $general->phone) }}" @endif class="form-control file-upload-info @error('phone_number') is-invalid @enderror" placeholder="Enter phone number" >
                                        <input type="hidden" name="country_code" id="country_code" @if(!empty($general)) value="{{ old('country_code', $general->country_code) }}" @endif>
                                    </div>
                                    @error('phone_number')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Address</label>
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name="address" @if(!empty($general)) value="{{ old('address', $general->address) }}" @endif>
                                    @error('address')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                       {{-- <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label>logo upload</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="logo" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" @if(!empty($general)) value="{{ old('logo', $general->logo) }}" @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($general)
                            @if($general->logo != "")
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src="{{url('')}}/storage/logo/{{$general->logo}}" width="200" height="200">
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endif--}}  
                        
                        <!-- New Fields for Platform Fee and Commission Setup -->
                        <h4 class="card-title"><u>Fee setting</u></h4>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="platformFee">Platform Fee (%)</label>
                                    <input type="number" step="0.01" class="form-control @error('platform_fee') is-invalid @enderror" id="platformFee" placeholder="Enter Platform Fee" name="platform_fee" @if(!empty($general)) value="{{ old('platform_fee', $general->platform_fee) }}" @endif>
                                    @error('platform_fee')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                {{--<div class="col-md-6">
                                    <label for="commissionSetup">Commission Setup (%)</label>
                                    <input type="number" step="0.01" class="form-control @error('commission') is-invalid @enderror" id="commissionSetup" placeholder="Enter Commission Setup" name="commission" @if(!empty($general)) value="{{ old('commission', $general->commission) }}" @endif>
                                    @error('commission')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>--}}

                                <div class="col-md-6">
                                    <label for="per_km_price">Per km price</label>
                                    <input type="number" step="0.01" class="form-control @error('per_km_price') is-invalid @enderror" id="per_km_price" placeholder="Enter Commission Setup" name="per_km_price" @if(!empty($general)) value="{{ old('per_km_price', $general->per_km_price) }}" @endif>
                                    @error('commission')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        
                        <button type="submit" class="btn btn-primary mr-2">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.3.2/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        $("#edit-user").validate({
            rules: {
                site_name: {
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
                    maxlength: 15
                },
                platform_fee: {
                    required: true,
                    number: true,
                    min: 0
                },
                commission_setup: {
                    required: true,
                    number: true,
                    min: 0
                }
            },
            messages: {
                site_name: {
                    required: "Site name is required",
                    minlength: "Site name must be at least 3 characters long"
                },
                email: {
                    required: "Email is required",
                    email: "Please enter a valid email address"
                },
                phone_number: {
                    required: "Phone number is required",
                    number: "Please enter a valid phone number",
                    minlength: "Phone number must be at least 8 digits long",
                    maxlength: "Phone number cannot exceed 15 digits"
                },
                platform_fee: {
                    required: "Platform fee is required",
                    number: "Please enter a valid number",
                    min: "Platform fee must be at least 0"
                },
                commission_setup: {
                    required: "Commission setup is required",
                    number: "Please enter a valid number",
                    min: "Commission setup must be at least 0"
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

        // Custom method to check for spaces
        $.validator.addMethod("noSpace", function(value, element) {
            return value.trim().length !== 0;
        }, "Spaces are not allowed");

        var input = document.querySelector("#phone");
        window.intlTelInput(input, {
            initialCountry: 'au', // Default country
            separateDialCode: true,
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js', // Path to utils.js    
        });

        // Update flag icon based on selected country
        $('#phone').on('countrychange', function() {
            var countryCode = $('.iti__selected-dial-code').html();
            $('#country_code').val(countryCode);
        });

    });

    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('profilePreview');
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
