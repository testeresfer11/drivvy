@extends('admin.layouts.app')
@section('title', 'Add Port')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Ports</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.port.list')}}">Ports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Ports</li>
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
            <h4 class="card-title">Add Port</h4>
          
           
            <form class="forms-sample" id="add-user" action="{{route('admin.port.add')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputFirstName">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputFirstName" placeholder="First Name" name="name">
                        @error('name')
                            <span class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                     <div class="col-6">
                        <label for="exampleInputAddress">Address</label>
                        <input type="text" class="form-control @error('address') is-invalid @enderror" id="exampleInputAddress" placeholder="Address" name = "address">
                        @error('address')
                            <span class="invalid-feedback">
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
<script>
  $(document).ready(function() {
    $("#add-user").validate({
        rules: {
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
            
            address: {
                required: true,
               
            },
            
        },
        name: {
            required: {
                required: "name is required",
                minlength: "name must consist of at least 3 characters"
            },
            
            address: {
                required: "Address is required"
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
      return value == '' || value.trim().length == value.length; 
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
