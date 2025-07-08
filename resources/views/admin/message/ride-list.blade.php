@extends('admin.layouts.app')
@section('title', 'Rides Messages')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Rides Messages</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Messages</a></li>
        <li class="breadcrumb-item active" aria-current="page">Passengers List</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <x-alert />
       
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <!-- <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
            <form action="{{ route('admin.messages.ride-search') }}" method="GET">
              <div class="d-flex align-items-center search-gap">
                <input type="text" name="search" placeholder="Search...">
                <button type="submit" class="btn default-btn btn-md">Search</button>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Ride ID </th>
                  <th> Driver Name </th>
                  <th> Origin </th>
                  <th> Destination </th>
                  <th> Actions </th>
                </tr>
              </thead>
              <tbody>
                
                @forelse ($rides as $key => $ride)
                
                  <tr>
                    <td> <a href="{{route('admin.ride.view',$ride->ride_id)}}">  #{{$ride->ride_id}} </a> </td>
                    <td> <a href="{{route('admin.user.view',['id' => $ride->user_id])}}"> {{$ride->first_name}} {{$ride->last_name}}</a> </td>
                    <td>{{$ride->departure_city}}</td>
                    <td>{{$ride->arrival_city}}</td>
                    <td>
                      <span class="menu-icon">
                        <a href="{{route('admin.messages.passengers',['id' => $ride->ride_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      <!-- <span class="menu-icon">
                        <a href="{{route('admin.ride.edit',['id' => $ride->ride_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp; -->
                      <!-- <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$ride->ride_id}}"><i class="mdi mdi-delete"></i></a>
                      </span>  -->
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
             {{ $rides->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<!-- <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#filterData').DataTable({
              layout: {
                    bottomEnd: null,
                    topStart: null
                }
        });
    });
</script> -->
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
                  url: "/admin/user/delete/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                      toastr.success(response.message);
                        //$('table.table-striped tr#+'{$user_id}).remove();
                         setTimeout(function() {
                            location.reload();
                         }, 2000);
                      } else {
                        toastr.error(response.message);
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
                data: { id: id, status: action },
                success: function(response) {
                    if (response.status == "success") {
                      toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                      toastr.error(response.message);
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
