@extends('admin.layouts.app')
@section('title', 'Edit Content')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Edit Content</h3>
</div>
@endsection
@section('content')
<div class="add-driver-form">
    <div class="row">
        <div class="col-12 grid-margin stretch-card">
            <div class="card rounded">
                <div class="card-body">
                    <form class="forms-sample" id="Edit-User" action="{{ route('admin.contentpage.edit', ['id' => $user->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <label for="exampleInputFirstName">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="exampleInputFirstName" placeholder="Name" name="name" value="{{ $user->name }}">
                                    @error('name')
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
                                    <textarea class="form-control" id="description" name="description">{{ $user->description }}</textarea>
                                    @if ($errors->has('description'))
                                        <span class="text-danger">{{ $errors->first('description') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="driver-btns d-flex justify-content-end">
                            <button type="submit" class="btn default-btn mr-2">Update</button>
                           
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

@stop
