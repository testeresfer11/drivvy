@extends('company.layouts.app')
@section('title', 'Add Card')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Cards</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.card.list')}}">Cards</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add card</li>
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
            <h4 class="card-title">Add Card</h4>
            <x-alert />
            <div class="flash-message"></div>
            <form class="forms-sample" id="add-card" action="{{route('admin.card.add')}}" method="POST" enctype="multipart/form-data">
              @csrf
                
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputName">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputName" placeholder="Name" name="name">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputCardLimit">Amount</label>
                        <input type="number" class="form-control  @error('amount') is-invalid @enderror" id="exampleInputCardLimit" placeholder="Amount" name="amount">
                        @error('amount')
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
                        <label for="card_type">Card Type</label>
                        <select class="js-example-basic-single select2-hidden-accessible @error('type') is-invalid @enderror" name="type" style="width:100%" data-select2-id="1" tabindex="-1" aria-hidden="true" id="card_type">
                            <option value="">--Select Card Type--</option>
                            <option value="image">Image</option>
                            <option value="text">Text</option>
                            <option value="video">Video</option>
                            <option value="audio">Audio</option>
                        </select>
                        @error('type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $type }}</strong>
                            </span>
                        @enderror    
                    </div>
                </div>
               
                <div class="form-group">
                    <div class="row">
                        <label for="exampleInputCategory">Category</label>
                        <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%" data-select2-id="1" tabindex="-1" aria-hidden="true">
                            <option value="">--Select Category--</option>
                            @foreach ($category as $item)
                                <option value="{{$item->id}}" data-select2-id="3">{{$item->name}}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror    
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
    $("#add-card").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            category_id:{
                required: true,
            },
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
            amount: {
                required: true,
                number: true,
            },
            type: {
                required: true,
            },
            file: {
                required: function(element) {
                    return $("#card_type").val() != "text";
                },
            },
            description: {
                required: function(element) {
                    return $("#card_type").val() === "text";
                }
            }

        },
        messages: {
            category_id:{
                required: "Category id is required."
            },
            name: {
                required: "Name is required.",
                minlength: "Name must consist of at least 3 characters."
            },
            amount: {
                required: "Amount is required.",
                number: 'Only numeric value is acceptable'
            },
            type:{
                required: "Please select card type."
            },
            file:{
                required: "Please select file."
            },
            description:{
                required: "Description is required."
            }
        },
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            if (element.prop('type') === 'file') {
                error.appendTo(element.closest('.row'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            form.submit();
        }

    });

});
$('#card_type').on('change',function(){
    $type = $('#card_type').val();
    $('.type').remove();
    if($type == 'image'){
        $(this).closest('.form-group').after( `<div class="form-group type">
            <div class="row">
                <label>Image</label>
                <div class="input-group col-xs-12">
                <input type="file" name="file" class="form-control file-upload-info" placeholder="Upload Image" accept="image/*">
                </div>
            </div>
        </div>`);
    }else if($type == 'video'){
        $(this).closest('.form-group').after( `<div class="form-group type">
            <div class="row">
                <label>Video</label>
                <div class="input-group col-xs-12">
                <input type="file" name="file" class="form-control file-upload-info" placeholder="Upload Image" accept="video/*">
                </div>
            </div>
        </div>`);
    }else if($type == 'audio'){
        $(this).closest('.form-group').after( `<div class="form-group type">
            <div class="row">
                <label>Audio</label>
                <div class="input-group col-xs-12">
                <input type="file" name="file" class="form-control file-upload-info" placeholder="Upload Audio" accept="audio/*">
                </div>
            </div>
        </div>`);
    }
});


</script>
@stop