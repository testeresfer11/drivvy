@extends('admin.layouts.app')
@section('title', 'Edit Policy')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Edit Policy</h3>
</div>
@endsection
@section('content')
<div class="add-policy-form">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card rounded">
                <div class="card-body">
                  

                    <form class="forms-sample" id="policies" action="{{ route('admin.policies.update', ['id' => $policy->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                <label for="exampleInputFirstName">Name</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" id="exampleInputFirstName" placeholder="Name" name="type" value="{{$policy->type}}">
                                    @error('type')
                                        <span class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                               
                                
                        </div>
                    </div>
                     <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <label for="exampleInputId">Content</label>
                                    <textarea class="form-control" id="description" name="content">{{ $policy->content }}</textarea>
                                    @if ($errors->has('content'))
                                        <span class="text-danger">{{ $errors->first('content') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>


                     
                        <div class="driver-btns d-flex justify-content-end">
                            <button type="submit" class="btn default-btn mr-2">Update</button>
                            <button type="button" class="btn btn-dark" onclick="window.location.href='{{ route('admin.policies.list') }}'">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection
@section('scripts')
<style>
    .img-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 10px;
    }
</style>
<script>
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function() {
        $("#policies").validate({
            rules: {
                name: {
                    required: true,
                },
                content: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: "Name is required",
                },
                content: {
                    required: 'Email is required.',
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>

@stop
