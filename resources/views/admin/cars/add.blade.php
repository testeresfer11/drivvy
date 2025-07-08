@extends('admin.layouts.app')

@section('title', ' Add Vehicle')

@section('breadcrumb')
<div class="page-header">
    <h3 class="page-title"> Add Vehicle</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.vechile.list') }}"> Add Vehicle </a></li>
            <li class="breadcrumb-item active" aria-current="page"> Add Vehicle</li>
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
                    <h4 class="card-title"> Add Vehicle</h4>
                    <x-alert />

                    <form class="forms-sample" id="add-vechile" action="{{ route('admin.vechile.add') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName">Make/Brand Name</label>
                                    <textarea class="form-control @error('make') is-invalid @enderror" name="make" rows="4" cols="50"></textarea>
                                    @error('make')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName"> Model </label>
                                    <textarea class="form-control @error('model') is-invalid @enderror" name="model" rows="4" cols="50"></textarea>
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
                                    <textarea class="form-control @error('type') is-invalid @enderror" name="type" rows="4" cols="50"></textarea>
                                    
                                    @error('type')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="exampleInputFirstName"> Color </label>
                                    <textarea class="form-control @error('color') is-invalid @enderror" name="color" rows="4" cols="50"></textarea>
                                    
                                    @error('color')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Add</button>
                    </form>
                    <br>    
                    <p><b>Note:</b> To add multiple values please add comma(,) as a seprator. e.g value1, value2...etc</p>

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
