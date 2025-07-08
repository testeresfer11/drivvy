@extends('company.layouts.app')
@section('title', 'Card')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Cards</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Cards</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cards</li>
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
            <h4 class="card-title">Cards Management</h4>
            <a href="{{route('admin.card.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add Card</span></button></a>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Card Name </th>
                  <th> Category Name </th>
                  <th> Amount </th>
                  <th> Status </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($cards as $key => $data)
                  <tr>
                    <td>{{++$key}}</td>
                    <td> {{$data->name}} </td>
                    <td>{{$data->Category ? ($data->Category->name ? $data->Category->name : 'N/A'): 'N/A'}}</td>
                    <td>{{$data->amount}}</td>
                    <td> 
                        <div class="toggle-user dark-toggle">
                        <input type="checkbox" name="is_active" data-id="{{$data->id}}" class="switch" @if ($data->status == 1) checked @endif data-value="{{$data->status}}">
                        </div> 
                    </td>
                    <td> 
                      {{-- <span class="menu-icon">
                        <a href="{{route('admin.card.view',['id' => $data->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp; --}}
                      <span class="menu-icon">
                        <a href="{{route('admin.card.edit',['id' => $data->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteCard" data-id="{{$data->id}}"><i class="mdi mdi-delete"></i></a>
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
            {{ $cards->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script>
  $('.deleteCard').on('click', function() {
    var category_id = $('.deleteCard').attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Card?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/card/delete/" + category_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
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
        text: "Do you want to change the status of the card?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#2ea57c",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, mark as status"
    }).then((result) => {
        if (result.isConfirmed) {
          $('.preloader').show();
            $.ajax({
                url: "/admin/card/changeStatus",
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
