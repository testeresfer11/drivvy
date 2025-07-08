@extends('admin.layouts.app')

@section('title', ' Edit Car')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title"> Edit Car</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.vehicle.list') }}"> Edit Car </a></li>
            <li class="breadcrumb-item active" aria-current="page"> Edit Car</li>
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
                    <h3 class="card-title"> Edit Car</h3>
                    <x-alert />

                    <form class="forms-sample" id="edit-vechile" action="{{ route('admin.cars.edit', ['id' => $cars->car_id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">Make/Brand Name</label>
                                    <input type="text" class="form-control @error('make') is-invalid @enderror" name="make" value="{{old('make',$cars->make)}}"> 
                                
                                    @error('make')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName"> Model </label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" name="model" value="{{old('make',$cars->model)}}"> 
                                   
                                    @error('model')
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
                                    <label for="exampleInputFirstName"> Type </label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" name="type" value="{{old('make',$cars->type)}}"> 
                                    
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName"> Color </label>
                                    <input type="text" class="form-control @error('color') is-invalid @enderror" name="color" value="{{old('make',$cars->color)}}"> 
                                    
                                    @error('color')
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
                                    <label for="exampleInputFirstName"> License Plate </label>
                                    <input type="text" class="form-control @error('license_plate') is-invalid @enderror" name="license_plate" value="{{old('make',$cars->license_plate)}}"> 
                                    
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName"> Year </label>
                                    <input type="text" class="form-control @error('year') is-invalid @enderror" name="year" value="{{old('make',$cars->year)}}"> 
                                    
                                    @error('year')
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
                                    <label for="exampleInputFirstName"> License Plate </label>
                                    <input type="text" class="form-control @error('license_plate') is-invalid @enderror" name="license_plate" value="{{old('make',$cars->license_plate)}}"> 
                                    
                                    @error('type')
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
        $("#add-vechile").validate({
            rules: {
                make: {
                    required: true,
                },
                model: {
                    required: true,
                },
                color: {
                    required: true,
                },
                type: {
                    required: true,
                },
            },
            messages: {
                make: {
                    required: "Make field is required",
                },
                model: {
                    required: "Model field is required",
                },
                color: {
                    required: "Color number is required",
                },
                type: {
                    required: "Type is required"
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
                form.submit();
            }
        });

        // Custom method to check for spaces
        $.validator.addMethod("noSpace", function(value, element) {
            return value.trim().length !== 0;
        }, "Spaces are not allowed");

    });


</script>
@endsection
