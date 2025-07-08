@extends('company.layouts.app')
@section('title', 'Shipment')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Shipment</h3>
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
                  <th> <p>S. No. </p> </th>
                  <th> <p>Date</p>  </th>
                  <th> <p>From</p>  </th>
                  <th> <p>Sender</p>  </th>
                  <th>  <p>Delivery Type </p></th>
                  <th>  <p>No. of Container</p></th>
                  <th>  <p>Status</p> </th>
                  <th>  <p>Action</p> </th>
                </tr>
              </thead>
              <tbody>
            
                  <tr>
                    <td class="py-1">
                      1
                    </td>
                    <td>10 Feb 2024 </td>
                    <td>Chicago </td>
                    <td>Chicago </td>
                    <td>Same Day</td>
                    <td> 2 </td>
                    <td>
                        <div class="status-act" data-bs-toggle="modal" data-bs-target="#exampleModal">Active</div>
                    </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="#" title="View" class="table-icon f-22"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      <!-- <span class="menu-icon">
                        <a href="#" title="Edit" class="table-icon f-22"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp; -->
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="table-icon f-22 deleteUser"><i class="mdi mdi-delete"></i></a>
                      </span> 
                    </td>
                  </tr>
              </tbody>
            </table>
          </div>
           
        </div>
      </div>
    </div>
  </div>
  <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0">
      <div class="modal-header border-0 justify-content-end">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
      </div>
      <div class="modal-body text-center">
        <h3 class="shipment-pop f-22">Do you want to accept the booking request ? </h3>
      </div>
      <div class="modal-footer border-0 justify-content-center gap-4 mb-4">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn default-btn">Accecpt</button>
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
                  url: "/admin/user/delete/" + user_id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        $('table.table-striped tr#+'{$user_id}).remove();
                        // setTimeout(function() {
                        //     location.reload();
                        // }, 2000);
                      } else {
                        $('.flash-message').html('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                      }
                  }
              });
          }
      });
  });

</script>

@stop
