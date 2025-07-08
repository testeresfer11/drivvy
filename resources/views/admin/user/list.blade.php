@extends('admin.layouts.app')
@section('title', 'Users')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Users</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Users</li>
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
          <div class="d-flex justify-content-between" style="padding-bottom: 16px;">
            <h4 class="card-title">User Management</h4>
            <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md" >
              <span class="menu-icon">+ Add User</span></button></a>
          </div>
          <div class="custom-search">
            <div class="custom-search">
               <form action="{{ route('admin.user.list') }}" method="GET" id="searchForm">
                      <div class="d-flex align-items-center search-gap">
                          <!-- Search Input -->
                         

                          <!-- Date Range Filter -->
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
                              <option value="" {{ request()->get('status') == '' ? 'selected' : '' }}>All Status</option>
                              <option value="1" {{ request()->get('status') == '1' ? 'selected' : '' }}>Active</option>
                              <option value="0" {{ request()->get('status') == '0' ? 'selected' : '' }}>Inactive</option>
                          </select>

                           <input type="text" name="search" placeholder="Search..." value="{{ request()->get('search') }}">

                          <!-- Submit and Reset Buttons -->
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
                          window.location.href = "{{ route('admin.user.list') }}";  // Redirect to the same page to clear the query parameters
                      });
                  </script>

            </div>

          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Profile </th>
                  <th> Name </th>
                  <th> Email </th>
                   <th> Registered At </th>
                  <th> Status </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($users as $user)
                
                  <tr id={{$user->user_id}}>
                  <td class="py-1">
                  <img 
                            class=" img-lg  rounded-circle"
                            @if($user->profile_picture != "")
                                src="{{url('/')}}/storage/users/{{$user->profile_picture}}"
                            @else
                                 src="{{ asset('/admin/images/user-image.webp') }}" 
                            @endif
                            
                            alt="User profile picture">
                    </td>
                    <td style="width: 300px" class="vehicle-modal"> {{$user->first_name ?? '-'}} {{$user->last_name}}</td>
                    <td style="width: 300px" class="vehicle-modal">{{$user->email}}</td>
                    <td style="width: 300px" class="vehicle-modal">{{$user->created_at}}</td>
                    <td> <div class="toggle-user dark-toggle">
                      <input type="checkbox" name="is_active" data-id="{{$user->user_id}}" class="switch" @if ($user->status == 1) checked @endif data-value="{{$user->status}}">

                    </div> </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.user.view',['id' => $user->user_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="{{route('admin.user.edit',['id' => $user->user_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$user->user_id}}"><i class="mdi mdi-delete"></i></a>
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
            <div class="custom_pagination">
            {{ $users->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>

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

<script>
    // Reset search input and reload the page
    document.getElementById('resetBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.user.list') }}"; // Redirect to the original list page
    });
</script>


@stop
