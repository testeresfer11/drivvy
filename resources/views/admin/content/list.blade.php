@extends('admin.layouts.app')

@section('title', 'Content')

@section('breadcrum')
<div class="page-header">
  <h3 class="page-title">Content</h3>
</div>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-12 grid-margin stretch-card">
    <div class="card">
      <x-alert />

      <div class="card-body">
        <form>
          <div class="search-box d-flex justify-content-between">
            <div class="search-input relative">
              <input type="search" placeholder="Search...">
              <button type="submit"><img src="{{ asset('admin/images/search-normal.png') }}"></button>
            </div>
            <div class="right-side-input d-flex align-items-center gap-2">
              <input type="date" class="form-control" name="date-input">
              <button class="sort-btn ml-2" type="button">Sort <img src="{{ asset('admin/images/sort.png') }}"></button>
            </div>
          </div>
        </form>

        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th><p>S. No.</p></th>
                <th><p>Title</p></th>
                <th><p>Description</p></th>
                <th><p>Status</p></th>
              
                <th><p>Action</p></th>
              </tr>
            </thead>
            <tbody>
              @forelse ($users as $user)
              <tr id="{{ $user->id }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $user->name }}</td>
                <td>
                  {!! truncate_html($user->description, 100) !!} 
                </td>
                <td>
                  <div class="toggle-user dark-toggle">
                    <input type="checkbox" name="is_active" data-id="{{ $user->id }}" class="switch"
                      {{ $user->status == 1 ? 'checked' : '' }} data-value="{{ $user->status }}">
                  </div>
                </td>
                
                <td>
                  <span class="menu-icon">
                    <a href="{{ route('admin.contentpage.edit', ['id' => $user->id, 'role' => $user->role_id]) }}" title="Edit"
                      class="text-success"><i class="mdi mdi-pencil"></i></a>
                  </span>&nbsp;&nbsp;
                  <span class="menu-icon deleteUser" data-id="{{ $user->id }}" title="Delete">
                    <i class="mdi mdi-delete"></i>
                  </span>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="no-record">
                  <center>No records found</center>
                </td>
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
  $(document).ready(function () {
    // Delete user functionality
    $('.deleteUser').on('click', function () {
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
            url: "/admin/contentpage/delete/" + user_id,
            type: "GET",
            success: function (response) {
              if (response.status == "success") {
                toastr.success(response.message);
                setTimeout(function () {
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

    // Switch status functionality
    $('.switch').on('click', function () {
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
            url: "/admin/contentpage/changeStatus",
            type: "GET",
            data: {
              id: id,
              status: action
            },
            success: function (response) {
              if (response.status == "success") {
                toastr.success(response.message);
                setTimeout(function () {
                  location.reload();
                }, 2000);
              } else {
                toastr.error(response.message);
              }
            },
            error: function (error) {
              console.log('error', error);
            }
          });
        } else {
          $('.switch').prop('checked', !$('.switch').prop('checked'));
        }
      });
    });
  });
</script>
@stop
