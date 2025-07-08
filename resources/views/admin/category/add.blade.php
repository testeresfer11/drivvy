@extends('admin.layouts.app')
@section('title', 'Add Category')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Category</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.category.list')}}">Category</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Category</li>
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
            <h4 class="card-title">Add Category</h4>
            <x-alert />
           
            <form class="forms-sample" id="add-category" action="{{route('admin.category.add')}}" method="POST">
              @csrf

              <div class="form-group">
                
                <div class="row">
                  <div class="col-6">
                    <label for="exampleInputName">Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputName" placeholder="Name" name="name">
                    @error('name')
                        <span class="invalid-feedback"
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  </div>
                    <div class="col-6">
                        <label for="exampleInputCardLimit">Card Limit</label>
                        <input type="number" class="form-control  @error('card_limit') is-invalid @enderror" id="exampleInputCardLimit" placeholder="Card Limit" name="card_limit">
                        @error('card_limit')
                            <span class="invalid-feedback"
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
              </div>
              
              <button type="submit" class="btn btn-primary mr-2">Add</button>
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
    $("#add-category").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
            card_limit: {
                required: true,
                number: true,
            },
        },
        messages: {
            name: {
                required: "Name is required.",
                minlength: "Name must consist of at least 3 characters."
            },
            card_limit: {
                required: "Card limit is required.",
                number: 'Only numeric value is acceptable'
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
  });
  </script>
@stop