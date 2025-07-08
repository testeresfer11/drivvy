@extends('admin.layouts.app')

@section('title', 'Edit Fare')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title">Edit Fare</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.fare.list') }}">Edit Fare</a></li>
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
                    <h4 class="card-title">Edit Fare</h4>
                    <x-alert />

                    <form class="forms-sample" id="edit-user" action="{{ route('admin.fare.edit',['id' => $fare->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" id="exampleInputAddress" placeholder="City" name="city" value="{{ old('city', $fare->city) }}" >
                                    @error('city')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Base Fare</label>
                                    <input type="text" class="form-control @error('base_fare') is-invalid @enderror" id="exampleInputAddress" placeholder="Base Fare" name="base_fare" value="{{ old('city', $fare->base_fare) }}">
                                    @error('base_fare')
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
                                    <label for="exampleInputAddress"> Cost per Kilometer</label>
                                    <input type="text" class="form-control @error('cost_per_kilometer') is-invalid @enderror" id="exampleInputAddress" placeholder="Cost per Kilometer" name="cost_per_kilometer" value="{{ old('city', $fare->cost_per_kilometer) }}">
                                    @error('cost_per_kilometer')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputAddress">Cost per minute</label>
                                    <input type="text" class="form-control @error('cost_per_minute') is-invalid @enderror" id="exampleInputAddress" placeholder="Cost per minute" name="cost_per_minute" value="{{ old('city', $fare->cost_per_minute) }}">
                                    @error('cost_per_minute')
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
                                        <label for="exampleInputEmail">Service Type </label>
                                        <input type="text" class="form-control @error('service_type') is-invalid @enderror" id="exampleInputEmail" placeholder="e.g., standard, luxury" name="service_type" value="{{ old('city', $fare->service_type) }}">
                                        @error('service_type')
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
                city: {
                    required: true,
                },
                base_fare: {
                    required: true,
                }
                cost_per_kilometer: {
                    required: true,
                }
                cost_per_minute: {
                    required: true,
                }
                service_type: {
                    required: true,
                }
            },
            messages: {
                city: {
                    required: "City is required",
                },
                base_fare: {
                    required: "Base fare is required",
                },
                cost_per_kilometer: {
                    required: "Cost per kilometer is required",
                },
                cost_per_minute: {
                    required: "Cost per minute is required",
                },
                service_type: {
                    required: "Service type is required",
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
