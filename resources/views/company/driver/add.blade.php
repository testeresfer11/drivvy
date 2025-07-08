@extends('company.layouts.app')
@section('title', 'Add Driver')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Add New Driver</h3>
</div>
@endsection
@section('content')
<div class="add-driver-form">
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card rounded">
          <div class="card-body">
            <x-alert />
            <div class="flash-message"></div>
            <form class="forms-sample" id="add-user" action="{{route('company.driver.add')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label for="exampleInputName"> Name</label>
                        <input type="text" class="form-control" id="Name" placeholder="Name" name="name"> 
                    </div>
                    <!-- <div class="col-12 col-md-4">
                        <label for="exampleInputId">ID Number</label>
                        <input type="text" class="form-control" id="idNumber" placeholder="ID Number" name="id_number">
                    </div> -->
                    <div class="col-12 col-md-4">
                        <label for="exampleInputId">Email Id</label>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="email">
                    </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                 
                    <div class="col-12 col-md-4">
                        <label for="exampleInputPhoneNumber">Phone Number</label>
                        <input type="number" class="form-control" id="exampleInputPhoneNumber" placeholder="Phone Number" name="phone_number">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputDate">Registration Date</label>
                        <input type="date" class="form-control" id="exampleInputDate" name="registration_date">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Address</label>
                        <input type="text" class="form-control" id="exampleInputAddress" placeholder="Address" name = "address">
                    </div>
                </div>
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Truck Number</label>
                        <input type="text" class="form-control" id="exampleInputTruck" placeholder="Truck Number" name = "truck_number">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Total No. of Truck</label>
                        <input type="text" class="form-control" id="exampleInputTruck" placeholder="Total No. of Truck" name = "total_no_of_truck">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Truck Type</label>
                        <select class="form-control" name="truck_type">
                            <option>Type</option>
                            <option>Type</option>
                            <option>Type</option>
                            <option>Type</option>
                        </select>
                    </div>
                </div> 
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label>License Card</label>
                        <div class="input-group col-xs-12">
                            <input type="file" name="license_card" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label>Passport</label>
                        <div class="input-group col-xs-12">
                            <input type="file" name="passport" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label>Vehicle Insurance </label>
                        <div class="input-group col-xs-12">
                            <input type="file" name="vehicle_insurance" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
                        </div>
                    </div>
                </div> 
              </div>
              <div class="form-group">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Vehicle Details</label>
                        <input type="text" class="form-control" id="exampleInputVehicle" placeholder="Vehicle Details" name = "vehicle_detail">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Password</label>
                        <input type="password" class="form-control" id="exampleInputPass" placeholder="******" name = "password">
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="exampleInputAddress">Confirm Password</label>
                        <input type="password" class="form-control" id="exampleInputPass" placeholder="******" name = "confirm_password">
                    </div>
                </div> 
              </div>
              <div class="driver-btns d-flex justify-content-end">
                <button type="submit" class="btn default-btn mr-2">Add</button>
                <button class="btn btn-dark">Cancel</button>
              </div>
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
    $("#add-user").submit(function(e){
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
                email: true,
                noSpace: true,
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
                required: 'Email is required.',
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