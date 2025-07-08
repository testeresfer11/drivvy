@extends('admin.layouts.app')

@section('title', 'Add Rides')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title"> Rides</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Rides</a></li>
            <!-- <li class="breadcrumb-item active" aria-current="page">Add Rides</li> -->
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
                    <h4 class="card-title">Add Ride</h4>
                    <x-alert />

                    <form class="forms-sample" id="edit-user" action="{{ route('admin.ride.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Select User</label>
                                    <select class="form-control @error('driver_id') is-invalid @enderror"  name="driver_id">
                                        <option value=""> </option>
                                    </select>
                                    @error('driver_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Arrival City</label>
                                    <input type="text" class="form-control @error('arrival_city') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name="arrival_city" >
                                    @error('arrival_city')
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
                                    <label for="exampleInputAddress">Departure City</label>
                                    <input type="text" class="form-control @error('departure_city') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name="departure_city" >
                                    @error('departure_city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Arrival City</label>
                                    <input type="text" class="form-control @error('arrival_city') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name="arrival_city" >
                                    @error('arrival_city')
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
                                        <label for="exampleInputEmail">Departure Time </label>
                                        <input type="datetime-local" class="form-control @error('departure_time') is-invalid @enderror" id="exampleInputEmail" placeholder="Email" name="departure_time">
                                        @error('departure_time')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPhoneNumber">Arrival Time</label>
                                    <input type="datetime-local" class="form-control @error('arrival_time') is-invalid @enderror" id="exampleInputEmail" placeholder="Arrival Time" name="arrival_time">
                                    @error('arrival_time')
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
                                    <label for="exampleInputEmail">Price per seat</label>
                                    <input type="text" class="form-control @error('price_per_seat') is-invalid @enderror" id="exampleInputEmail" placeholder="Price per seat" name="price_per_seat">
                                    @error('price_per_seat')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPhoneNumber">Available Seats</label>
                                    <input type="text" class="form-control @error('available_seats') is-invalid @enderror" id="exampleInputEmail" placeholder="Available Seats" name="available_seats">
                                    @error('available_seats')
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
                                    <label for="exampleInputEmail">Luggage size</label>
                                    <input type="text" class="form-control @error('price_per_seat') is-invalid @enderror" id="exampleInputEmail" placeholder="Luggage size" name="luggage_size">
                                    @error('luggage_size')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPhoneNumber">Smoking Allowed</label>
                                    <input type="text" class="form-control @error('smoking_allowed') is-invalid @enderror" id="exampleInputEmail" placeholder="Smoking allowed" name="smoking_allowed">
                                    @error('smoking_allowed')
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
                                    <label for="exampleInputEmail">Pets Allowed</label>
                                    <input type="text" class="form-control @error('pets_allowed') is-invalid @enderror" id="exampleInputEmail" placeholder="Pets Allowed" name="pets_allowed">
                                    @error('pets_allowed')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputPhoneNumber">Music Preference</label>
                                    <input type="text" class="form-control @error('music_preference') is-invalid @enderror" id="exampleInputEmail" placeholder="Music Preference" name="music_preference">
                                    @error('music_preference')
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
                                    <label for="exampleInputEmail">Description</label>
                                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="exampleInputEmail" placeholder="Description" name="description">
                                    @error('description')
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/23.3.2/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        $("#edit-user").validate({
            rules: {
                name: {
                    required: true,
                    noSpace: true,
                    minlength: 3
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
