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
          <div class="d-flex justify-content-between">
            <h4 class="card-title">Deleted Users</h4>
            <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a>
          </div>
          <div class="custom-search">
            <form action="{{ route('admin.user.deleted') }}" method="GET">
              <div class="d-flex align-items-center search-gap">
                <input type="text" name="search" placeholder="Search...">
                <button type="submit" class="btn default-btn btn-md">Search</button>
                 <button type="button" class="btn default-btn btn-md" id="resetBtn">Reset</button>
              </div>
            </form>
          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Profile </th>
                  <th> Name </th>
                  <th> Email </th>
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
                    
                    <td> 
                   
                      
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                       <a href="#" title="Restore" class="text-success restoreUser" data-id="{{ $user->user_id }}">
				    <i class="mdi mdi-restore"></i>
				</a>
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
$(document).on('click', '.restoreUser', function(e) {
    e.preventDefault();
    const userId = $(this).data('id');

    // Show SweetAlert confirmation dialog
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to restore this user?",
        icon: 'warning',
        showCancelButton: true,
         confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
        confirmButtonText: 'Yes, restore it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get CSRF token from the meta tag
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // AJAX request to restore the user
            $.ajax({
                url: '/admin/user/restore/' + userId, // Adjust the URL as needed
                type: 'POST', // Use POST for restoring
                data: {
                    _token: csrfToken // Include CSRF token
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success('User restored successfully!');

                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                          toastr.error('Unable to restore the user.');

                    }
                },
                error: function(xhr) {
                    // Handle error (e.g., show an error message)
                        toastr.error('An error occurred while restoring the user. Please try again.');

                    console.error('Error restoring user', xhr);
                }
            });
        }
    });
});
</script>

<script>
    // Reset search input and reload the page
    document.getElementById('resetBtn').addEventListener('click', function() {
        window.location.href = "{{ route('admin.user.deleted') }}"; // Redirect to the original list page
    });
</script>



@stop
