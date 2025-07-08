@extends('admin.layouts.app')

@section('title', 'Edit User')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title"> Users</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Users</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit User</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div>
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card form-component">
                <div class="card-body">
                    <h3 class="card-title">Edit User </h3>
                    <x-alert />

                    <form class="forms-sample" id="edit-user" action="{{ route('admin.user.edit', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">First Name</label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Enter First Name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                                    @error('first_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputLastName">Last Name</label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="exampleInputLastName" placeholder="Enter Last Name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                    @error('last_name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputEmail">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="email" value="{{ old('email', $user->email) }}">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPhoneNumber">Phone Number</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <!-- Display the flag icon based on the user's country -->
                                                <i id="flag-icon" class="flag-icon flag-icon-{{ old('country_shortname', $user->country_shortname) }}"></i>
                                            </div>
                                        </div>

                                        <input type="tel" id="phone" name="phone_number" value=" {{$user->phone_number}}" class="form-control @error('phone_number') is-invalid @enderror" placeholder="Enter phone number">
                                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $user->country_code) }}">
                                        <input type="hidden" name="country_shortname" id="country_shortname" value="{{ old('country_shortname', $user->country_shortname) }}">
                                    </div>
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
                                <div class="col-md-6">
                                    <label>Profile upload</label>
                                    <div class="input-group col-xs-12">
                                        <input type="file" name="profile_picture" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" onchange="previewImage(event)">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputBio">Bio</label>
                                    <textarea class="form-control @error('bio') is-invalid @enderror" id="exampleInputBio" name="bio">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if($user->profile_picture)
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="{{ url('storage/users/'.$user->profile_picture) }}" width="200" height="200" id="profilePreview" alt="Profile Picture">
                                </div>
                            </div>
                        </div>
                        @endif
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
    // Form validation
    $("#edit-user").validate({
        rules: {
            first_name: {
                required: true,
                noSpace: true,
                minlength: 3
            },
            last_name: {
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
            }
        },
        messages: {
            first_name: {
                required: "First name is required",
                minlength: "First name must be at least 3 characters long"
            },
            last_name: {
                required: "Last name is required",
                minlength: "Last name must be at least 3 characters long"
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

    // Initialize international telephone input
    var input = document.querySelector("#phone");
    var iti = window.intlTelInput(input, {
        initialCountry: '{{ old('country_shortname', $user->country_shortname) }}',
        separateDialCode: true,
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js',
    });

    // Update hidden input and flag icon when the country changes
    input.addEventListener('countrychange', function() {
        var countryData = iti.getSelectedCountryData();
        $('#country_code').val('+' + countryData.dialCode);
        $('#country_shortname').val(countryData.iso2);
        $('#flag-icon').removeClass().addClass('flag-icon flag-icon-' + countryData.iso2);
    });

  $(document).ready(function() {

    var initialCountryCode = '{{ old('country_code', $user->country_code) }}';
    var initialPhoneNumber = '{{ old('phone_number', $user->phone_number) }}';

    if (initialCountryCode) {
 	iti.setNumber(initialCountryCode);
    }

    if (initialPhoneNumber) {
        iti.setNumber(initialPhoneNumber);
    }
});
	
    
    // Image preview functionality
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('profilePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
});

</script>
@endsection
