@extends('company.layouts.app')
@section('title', 'Edit Category')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Category</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.category.list')}}">Category</a></li>
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
            <h4 class="card-title">Edit Category</h4>
            <x-alert />
            <div class="flash-message"></div>
            <form class="forms-sample" id="Edit-Category" action="{{route('admin.category.edit',['id' => $category->id])}}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <div class="form-group">
                <div class="row">
                    <div class="col-6">
                        <label for="exampleInputName">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputName" placeholder=" Name" name="name" value="{{$category->name ?? ''}}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label for="exampleCardLimit">Card Limit</label>
                        <input type="number" class="form-control  @error('card_limit') is-invalid @enderror" id="exampleCardLimit" placeholder="Card Limit" name="card_limit" value="{{$category->card_limit ?? ''}}">
                        @error('card_limit')
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
    $("#Edit-Category").submit(function(e){
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
            first_name: {
                required: "Name is required",
                minlength: "Name must consist of at least 3 characters"
            },
            
           
            card_limit: {
                required: 'Card limit is required',
                number: 'Only numeric value is acceptable',
            },
        },
        submitHandler: function(form) {
          form.submit();
      }

    });
  });
  </script>
@stop