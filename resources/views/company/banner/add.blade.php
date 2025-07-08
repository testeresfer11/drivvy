@extends('company.layouts.app')
@section('title', 'Add Banner')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Banners</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.banner.list')}}">Banners</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Banner</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row justify-content-center">
      <div class="col-5 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Add Banner</h4>
            <x-alert />
            <div class="flash-message"></div>
            <form class="forms-sample" id="add-banner" action="{{route('admin.banner.add')}}" method="POST" enctype="multipart/form-data">
              @csrf
                
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputTitle">Title</label>
                        <input type="text" class="form-control @error('title ') is-invalid @enderror" id="exampleInputTitle" placeholder="Title" name="title">
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputdescription">Description</label>
                        <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="exampleInputdescription" placeholder="Description" name="description"></textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label>Image</label>
                        <div class="input-group col-xs-12">
                        <input type="file" name="file" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
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
    $("#add-banner").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            title: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
            file: {
                required: true,
            },
        },
        messages: {
            title: {
                required: "Title is required.",
                minlength: "Title must consist of at least 3 characters."
            },
            file:{
                required: "Please select banner image."
            },
        },
        errorPlacement: function(error, element) {
            if (element.prop('type') === 'experience_level') {
                error.appendTo(element.closest('.form-group'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }
    });

});

</script>
@stop