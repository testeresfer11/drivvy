@extends('admin.layouts.app')
@section('title', 'Profile Detail')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Settings</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Profile</a></li>
        <li class="breadcrumb-item active" aria-current="page">Settings</li>
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
            <h4 class="card-title">Personal Information</h4>
            <x-alert />
           
            <form class="forms-sample" id="profile-setting" action="{{route('admin.profile')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="exampleInputFirstName">Name</label>
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
                        <input type="tel" id="phone" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="form-control file-upload-info  placeholder="Enter phone number" >
                        <input type="hidden" name="country_code" id="country_code" value="{{ old('country_code', $user->country_code) }}">
                        <input type="hidden" name="country_shortname" id="country_shortname" value="{{$country_shortname}}">
                    </div>
                    <div class="@error('phone_number') is-invalid @enderror"> </div>
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
                            <input type="file" name="profile_picture" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*" value="{{ old('profile_picture', $user->profile_picture) }}">
                            </div>
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
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="exampleInputEmail">Bio</label>
                        <textarea  class="form-control @error('bio') is-invalid @enderror" id="exampleInputEmail"  name="bio" value="{{ old('bio', $user->bio) }}" >{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                </div>
            </div>
          
              <button type="submit" class="btn btn-primary mr-2">Update</button>
              {{-- <button class="btn btn-dark">Cancel</button> --}}
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
    $("#profile-setting").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            first_name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
            last_name: {
                required: true,
                noSpace: true,
                minlength: 3
            },
            email: {
                required: true,
                email: true
            },
            phone_number: {
                number: true,
                minlength:10,
                maxlength: 15,
                noSpace: true,
            },
        },
        messages: {
            first_name: {
                required: "First name is required",
                minlength: "First name must consist of at least 3 characters"
            },
            last_name: {
                required: "Last name is required",
                minlength: "Last name must consist of at least 3 characters"
            },
            email: {
                email: "Please enter a valid email address"
            },
            phone_number: {
                number: 'Only numeric value is acceptable',
                minlength:  'Phone number must be min 10 digits',
                maxlength:  'Phone number must be max 15 digits'
            },
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
          var formData = new FormData(form);
          var action = $(form).attr('action'); // Corrected to use jQuery
          $.ajax({
              url: action, // Use the form's action attribute
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response) {
                console.log(response);  
                  // Handle success
                  if (response.status == "success") {
                    toastr.success(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                  } else {
                    toastr.error(response.message);
                  }
              },
              error: function(xhr, status, error) {
                  // Handle error 
                  let response = xhr.responseJSON;
                  toastr.error(response.message);
              }
          });
      }

    });


    var input = document.querySelector("#phone");
    var iti=  window.intlTelInput(input, {
            initialCountry: 'au', // Default country
            separateDialCode: true,
            utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js', // Path to utils.js    
    });

    var country_shortname= $('#country_shortname').val();

        iti.setCountry(country_shortname);

    // Update flag icon based on selected country
    $('#phone').on('countrychange', function() {
        var countryCode =$('.iti__selected-dial-code').html();
        $('#country_code').val(countryCode);
    });

  });
  </script>
@stop