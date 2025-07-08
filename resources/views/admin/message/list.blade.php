@extends('admin.layouts.app')
@section('title', 'Messages')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Messages</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Messages</a></li>
        <li class="breadcrumb-item" aria-current="page">Passengers List</li>
        <li class="breadcrumb-item active" aria-current="page">Conversation</li>
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
            <h4 class="card-title">Rides Messages</h4>
            <!-- <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Driver Name </th>
                  <th> Sender Name </th>
                  <th> Receiver Name </th>
                  <th> Message </th>
                  <th> Date/Time </th>
                </tr>
              </thead>
              <tbody>
                
                @forelse ($messages as $key => $message)
                
                  <tr>
                    <td>{{$key+1}}</td>
                    <td> {{$message->sender_name}} </td>
                    <td> {{$message->receiver_name}} </td>
                    <td>{{$message->content}}</td>
                    <td>{{convertDate($message->timestamp)}}</td>
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
             {{ $messages->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
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

@stop
