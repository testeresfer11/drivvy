@extends('company.layouts.app')
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
            <div class="flash-message"></div>
            <form class="forms-sample" id="profile-setting" action="{{route('company.profile')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">First Name</label>
                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="exampleInputFirstName" placeholder="First Name" name="first_name" value="{{$user->first_name ?? ''}}">
                        @error('first_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputLastName">Last Name</label>
                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="exampleInputLastName" placeholder="Last Name" name="last_name" value="{{$user->last_name ?? ''}}">
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
                    <div class="col-6">
                        <label for="exampleInputEmail">Email address</label>
                        <input type="email" class="form-control  @error('email') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="email" value="{{$user->email ?? ''}}" readonly>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPhoneNumber">Phone Number</label>
                        <input type="number" class="form-control  @error('phone_number') is-invalid @enderror" id="exampleInputPhoneNumber" placeholder="Phone Number" name="phone_number" value="{{$user->userDetail ? ($user->userDetail->phone_number ?? '') : ''}}">
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
                    <div class="col-6">
                        <label for="exampleInputAddress">Address</label>
                            
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Address" name = "address" value = {{$user->userDetail ? ($user->companyDetail->address ?? '') : ''}}>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">Pin Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="exampleInputPinCode" placeholder="Pin Code" name="zip_code" value = {{$user->userDetail ?($user->companyDetail->zip_code ?? '') : ''}}>
                        @error('zip_code')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                </div> 
              </div>

              <div class="form-group">
                <label>Profile upload</label>
                <div class="input-group col-xs-12">
                  <input type="file" name="profile" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
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
                maxlength: 10,
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
                minlength:  'Phone number must be 10 digits',
                maxlength:  'Phone number must be 10 digits'
            },
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
  });


  </script>
@stop