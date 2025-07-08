@extends('admin.layouts.app')
@section('title', 'Edit Port')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Ports</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.port.list') }}">Ports</a></li>
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
            <h4 class="card-title">Edit Port</h4>
            <x-alert />
           
            <form class="forms-sample" id="Edit-User" action="{{ route('admin.port.edit', ['id' => $port->id]) }}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <div class="form-group">

                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Name" name="name" value="{{ $port->name ?? '' }}">
                        @error('name')
                            <span class="invalid-feedback"
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-6">
                        <label for="exampleInputAddress">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" placeholder="Address" name="address" value="{{ $port->address ?? ''}}">
                        @error('address')
                            <span class="invalid-feedback"
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
<script>
$(document).ready(function() {
    $("#Edit-User").validate({
        rules: {
            name: {
                required: true,
                minlength: 3,
                nowhitespace: true
            },
            address: {
                required: false,
                maxlength: 255
            }
           
        },
        messages: {
            name: {
                required: "name is required",
                minlength: "name must be at least 3 characters long",
                nowhitespace: "name cannot contain spaces"
            },
       
            address: {
                maxlength: "Address cannot exceed 255 characters"
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
});

// Function to preview selected image
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
            $('.preview-image').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@stop
