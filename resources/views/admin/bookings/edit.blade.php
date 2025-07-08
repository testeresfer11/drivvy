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
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Edit User</h4>
                    <x-alert />

                    <form class="forms-sample" id="edit-user" action="{{ route('admin.user.edit', ['id' => $user->user_id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">First Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Enter First Name" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">Last Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Enter last Name" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                                    @error('name')
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
                                            <i id="flag-icon" class="flag-icon flag-icon-us"></i> <!-- Default flag is US -->
                                        </div>
                                    </div>
                                    <input type="tel" id="phone" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control file-upload-info @error('phone_number') is-invalid @enderror" placeholder="Enter phone number" >
                                    <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $user->country_code) }}">
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
                                    <div class="form-group">
                                        <label>Profile upload</label>
                                        <div class="input-group col-xs-12">
                                        <input type="file" name="profile_picture" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" value="{{ old('profile_picture', $user->profile_picture) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputEmail">Bio</label>
                                    <input type="text" class="form-control @error('bio') is-invalid @enderror" id="exampleInputEmail" placeholder="Enter Bio" name="bio" value="{{ old('bio', $user->bio) }}">
                                    @error('bio')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if($user->profile_picture != "")
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                        <image src="{{url('')}}/storage/users/{{$user->profile_picture}}" width="200" hieght="200">
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
        $("#edit-user").validate({
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
                    maxlength: 15
                }
            },
            messages: {
                first_name: {
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
                initialCountry: 'us', // Default country
                separateDialCode: true,
                utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js', // Path to utils.js    
        });

        // Update flag icon based on selected country
        $('#phone').on('countrychange', function() {
            var countryCode =$('.iti__selected-dial-code').html();
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
