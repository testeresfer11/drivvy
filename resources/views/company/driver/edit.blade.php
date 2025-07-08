@extends('company.layouts.app')
@section('title', 'Edit User')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Drivers</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('company.user.list')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
            <h4 class="card-title">Edit Driver</h4>
            <x-alert />
            <div class="flash-message"></div>
            <form class="forms-sample" id="Edit-User" action="{{route('company.driver.edit',['id' => $user->id])}}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">Profile</label>
                        <img 
                            class=" img-lg  rounded-circle"
                            @if(isset($user->userDetail) && !is_null($user->userDetail->profile))
                                src="{{ asset('storage/images/' . $user->userDetail->profile) }}"
                            @else
                                src="{{ asset('admin/images/faces/face15.jpg') }}"
                            @endif
                            onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                            alt="User profile picture">
                    </div>
                </div>
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
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name = "address" value = {{$user->userDetail ? ($user->userDetail->address ?? '') : ''}}>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">Pin Code</label>
                        <input type="text" class="form-control @error('zip_code') is-invalid @enderror" id="exampleInputPinCode" placeholder="Pin Code" name="zip_code" value = {{$user->userDetail ?($user->userDetail->zip_code ?? '') : ''}}>
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
              <h4 class="card-title">Other Detail</h4>
                <div class="form-group">
                <div class="row">
                    <div class="col-6">

                        <label for="exampleInputAddress">Vehical No.</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="exampleInputAddress" placeholder="Vehical No" name = "truck_number" value = {{$user->userDetail ?($user->truck_number ?? '') : ''}}>
                        @error('address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">License ID</label>
                        <input type="text" class="form-control @error('license_no') is-invalid @enderror" id="exampleInputPinCode" placeholder="License ID" name="license_no" value = {{$user->userDetail ?($user->license_no ?? '') : ''}}>
                        @error('license_no')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleInputPinCode">RC</label>
                        <input type="text" class="form-control @error('rc') is-invalid @enderror" id="exampleInputPinCode" placeholder="RC" name="rc" value = {{$user ?($user->rc ?? '') : ''}}>
                        @error('rc')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>

                    <div class="col-6">
                        <label for="exampleInputPinCode">Insurance ID</label>
                        <input type="text" class="form-control @error('insurance_id') is-invalid @enderror" id="exampleInputPinCode" placeholder="Insurance Id" name="insurance_id" value = {{$user ?($user->insurance_id ?? '') : ''}}>
                        @error('insurance_id')
                          <span class="invalid-feedback" role="alert">
                              <strong>{{ $message }}</strong>
                          </span>
                        @enderror
                    </div>
                </div> 
              </div>
              <button type="submit" class="btn btn-primary mr-2" >Update</button>
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
    $("#Edit-User").submit(function(e){
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
          form.submit();
      }

    });
  });
  </script>
@stop