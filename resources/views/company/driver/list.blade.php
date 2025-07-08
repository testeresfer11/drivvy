@extends('company.layouts.app')
@section('title', 'Driver')
@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Drivers</h3>
  <a href="{{route('company.driver.add')}}"><button type="button" class="btn default-btn btn-md">
      <span class="menu-icon">+ Add Driver</span></button></a>
</div>
@endsection
@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <x-alert />
      <div class="flash-message"></div>
      <div class="card-body">
        <form>
          <div class="search-box d-flex justify-content-between">
            <div class="search-input relative">
              <input type="search" placeholder="Search...">
              <button type="submit"><img src="{{asset('admin/images/search-normal.png')}}"></button>
            </div>
            <div class="right-side-input d-flex align-items-center gap-2">
              <input type="date" class="form-control" name="date-input">
              <button class="sort-btn ml-2" type="button">Sort <img src="{{asset('admin/images/sort.png')}}"></button>
            </div>
          </div>
        </form>
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th> <p>S. No. </p></th>
                <th> <p>Image </p></th>
                <th> <p>Date </p></th>
                <th> <p> Driver Name </p></th>
                <th> <p>Email </p></th>
                <th> <p>Contact No.</p> </th>
                <th> <p>Address </p> </th>
                <th> <p>Status </p> </th>
                <th> <p>Action </p></th>
              </tr>
            </thead>
             <tbody>
                @forelse ($users as $user)
                
                  <tr id={{$user->id}}>
                    <td>{{$loop->iteration}}</td>
                    <td class="py-1">
                        <img src="{{ asset('storage/images/' . ($user->driverDetail->profile ?? 'N/A')) }}" alt="User profile picture">
                    </td>


                    <td> {{$user->created_at}} </td>
                    <td>{{$user->first_name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{ optional($user->driverDetail)->phone_number ?? 'N/A' }}</td>

                     <td>{{$user->driverDetail->address?? 'N/A'}}</td>
                     <td>{{$user->status}}</td>
                    <td> <div class="toggle-user dark-toggle">
                      <input type="checkbox" name="is_active" data-id="{{$user->id}}" class="switch" @if ($user->status == 1) checked @endif data-value="{{$user->status}}">

                    </div> </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('company.user.view',['id' => $user->id])}}" title="View" class="table-icon f-22"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="{{route('company.user.edit',['id' => $user->id])}}" title="Edit" class="table-icon f-22"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="table-icon f-22 deleteUser" data-id="{{$user->id}}"><i class="mdi mdi-delete"></i></a>
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
          {{ $users->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $('.deleteUser').on('click', function() {

    var user_id = $('.deleteUser').attr('data-id');
    Swal.fire({
      title: "Are you sure?",
      text: "You want to delete the User?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#2ea57c",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/company/driver/delete/" + user_id,
          type: "GET",
          success: function(response) {
            if (response.status == "success") {
              $('.flash-message').html('<div class="alert alert-success" role="alert">' + response.message + '</div>');
              // $('table.table-striped tr#+'{$user_id}).remove();
              setTimeout(function() {
                location.reload();
              }, 2000);
            } else {
              $('.flash-message').html('<div class="alert alert-danger" role="alert">' + response.message + '</div>');
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
      text: "Do you want to change the status of the user?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#2ea57c",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, mark as status"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "/admin/user/changeStatus",
          type: "GET",
          data: {
            id: id,
            status: action
          },
          success: function(response) {
            if (response.status == "success") {
              $('.flash-message').html('<div class="alert alert-success" role="alert">' + response.message + '</div>');
              setTimeout(function() {
                location.reload();
              }, 2000);
            } else {
              $('.flash-message').html('<div class="alert alert-danger" role="alert">' + response.message + '</div>');
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