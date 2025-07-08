@extends('admin.layouts.app')
@section('title', 'Rides')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Rides</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Rides</a></li>
        <li class="breadcrumb-item active" aria-current="page">Rides</li>
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
            <h4 class="card-title">Rides Management</h4>
            <!-- <a href="{{route('admin.ride.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
              <form action="{{ route('admin.ride.list') }}" method="GET" id="searchForm">
    <div class="d-flex align-items-center search-gap">
        <!-- Search Input -->
     

              <div class="form-group">
                            <label for="start_date">From Date</label>
                                    <input type="date" id="start_date" name="start_date" value="{{ request()->get('start_date') }}" class="form-control" placeholder="Start Date">
                                </div>

                                <div class="form-group">
                                    <label for="end_date">To Date</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ request()->get('end_date') }}" class="form-control" placeholder="End Date">
                                </div>

                                <script>
                                    // Automatically open the calendar when clicking on the input field
                                    document.getElementById('start_date').addEventListener('focus', function() {
                                        this.showPicker();
                                    });
                                    
                                    document.getElementById('end_date').addEventListener('focus', function() {
                                        this.showPicker();
                                    });
                                </script>

        <!-- Status Filter -->
        <select name="status" class="form-control">
            <option value="">All</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
           <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}">
        <button type="submit" class="btn default-btn btn-md">Search</button>
        <button type="button" class="btn default-btn btn-md" id="resetBtn">Reset</button>
    </div>
</form>

<script>
    // Reset button click event
    document.getElementById('resetBtn').addEventListener('click', function() {
        // Reset form inputs
        document.getElementById('searchForm').reset();

        // Reset the form to initial state after clearing inputs
        window.location.href = "{{ route('admin.ride.list') }}";  // Redirect to the same page to clear the query parameters
    });
</script>


          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Ride ID </th>
                  <th> Driver Name </th>
                  <th> Origin </th>
                  <th> Destination </th>
                  <th> Total Seats </th>
                  <th> Seats Available </th>
                   <th> departure Date </th>
                  <th> Ride status </th>
                  <th> Actions </th>
                </tr>
              </thead>
              <tbody>
                
                @forelse ($rides as $key => $ride)
                
                  <tr>
                    <td>{{$ride->ride_id}}</td>
                    <td> <a href="{{route('admin.user.view',['id' => $ride->user_id])}}" > {{$ride->first_name}} {{$ride->last_name}} </a> </td>
                    <td>{{$ride->departure_city}}</td>
                    <td>{{$ride->arrival_city}}</td>
                    <td>{{$ride->available_seats}}</td>

                    <td>{{ $ride->seat_left <= 0 ? 'Full' : $ride->seat_left}}</td>
                      <td>{{ $ride->departure_time}}</td>

                    <td>{{ $ride->getStatusTextnew() }}</td>

                    <td>
                      <span class="menu-icon">
                        <a href="{{route('admin.ride.view',['id' => $ride->ride_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span> &nbsp;&nbsp;&nbsp;
                      {{--<span class="menu-icon">
                        <a href="{{route('admin.ride.edit',['id' => $ride->ride_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;--}}
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$ride->ride_id}}"><i class="mdi mdi-delete"></i></a>
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
              {{ $rides->appends(request()->query())->links('pagination::bootstrap-4') }}
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
    var user_id = $(this).attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Ride?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/ride/delete/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    console.log(response); // Check the server response here
                    if (response.status == "success") {
                        toastr.success(response.message);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        toastr.error(response.message); // This should show the error message
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
