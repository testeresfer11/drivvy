@extends('company.layouts.app')
@section('title', 'Catrgory')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Catrgory</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.category.list')}}">Catrgory</a></li>
        <li class="breadcrumb-item active" aria-current="page">Category</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <x-alert />
        <div class="flash-message"></div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h4 class="card-title">Catrgory Management</h4>
            <a href="{{route('admin.category.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add Category</span></button></a>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Name </th>
                  <th> Card Limit </th>
                  <th> Status </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($category as $key => $data)
                  <tr>
                    <td>{{++$key}}</td>
                    <td> {{$data->name}} </td>
                    <td>{{$data->card_limit}}</td>
                    <td> 
                        <div class="toggle-user dark-toggle">
                        <input type="checkbox" name="is_active" data-id="{{$data->id}}" class="switch" @if ($data->status == 1) checked @endif data-value="{{$data->status}}">
                        </div> 
                    </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.category.edit',['id' => $data->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteCategory" data-id="{{$data->id}}"><i class="mdi mdi-delete"></i></a>
                      </span> 
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="6" class="no-record"> <center>No record found </center></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="custom_pagination">
            {{ $category->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteCategory').on('click', function() {
    console.log('here');
    var category_id = $('.deleteCategory').attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Category?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/category/delete/" + category_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                        if(response.count == 0){
                          $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                          setTimeout(function() {
                              location.reload();
                          }, 2000);
                        }else{
                          Swal.fire({
                            title: "OOPs! Unable to delete. ",
                            text: response.message,
                            icon: "info",
                            confirmButtonColor: "#2ea57c",
                            confirmButtonText: "Ok"
                          });
                        }
                    } else {
                      $('.flash-message').html('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                    }
                  }
              });
          }
      });
  });

  $('.switch').on('click', function() {
    var status = $(this).data('value');
    var action = (status == 1) ? 0 : 1;
    var id = $(this).data('id');

    Swal.fire({
        title: "Are you sure?",
        text: "Do you want to change the status of the category?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "/admin/category/changeStatus",
                type: "GET",
                data: { id: id, status: action },
                success: function(response) {
                    if (response.status == "success") {
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        $('.flash-message').html('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                    }
                },
                error: function(error) {
                    console.log('error', error);
                }
            });
        } else {
            $('.switch').prop('checked', !$('.switch').prop('checked'));
        }
    });
  });

</script>

@stop
