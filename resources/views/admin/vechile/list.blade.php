@extends('admin.layouts.app')
@section('title', 'Vehicle')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Vehicle</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Vehicle</a></li>
        <li class="breadcrumb-item active" aria-current="page">Vehicle</li>
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
            <h4 class="card-title">Vehicle Management</h4>
            <a href="{{route('admin.vehicle.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add Vehicle</span></button></a>
          </div>
         <div class="custom-search">
            <form action="{{ route('admin.vehicle.search') }}" method="GET" id="searchForm">
                <div class="d-flex align-items-center search-gap">
                    <input type="text" name="search" value="{{ request()->search }}" placeholder="Search...">
                    <button type="submit" class="btn default-btn btn-md">Search</button>
                    <button type="button" class="btn default-btn btn-md" id="resetBtn">Reset</button>
                </div>
            </form>

            <script>
                // Reset button click event
                document.getElementById('resetBtn').addEventListener('click', function() {
                    // Reset form inputs
                    document.getElementById('searchForm').reset();
                    
                    // Optionally, you can remove query parameters by redirecting to the base URL
                    window.location.href = "{{ route('admin.vehicle.list') }}";
                });
            </script>
        </div>

          <div class="table-responsive vehicle-table">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Sr No. </th>
                  <th> Make </th>
                  <th> Model </th>
                  <th> Type </th>
                  <th> Colors </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($vechiles as $keys => $vechile)
                
                  <tr id={{$keys+1}}>
                  <td> {{$keys+1}} </td>
                  <td> {{$vechile->make}} </td>
                    <td> <div  style="width: 300px" class="vehicle-modal">{{$vechile->model}}</div> </td>
                    <td> <div  style="width: 200px" class="vehicle-modal">{{$vechile->type}}</div></td>
                    <td> <div  style="width: 200px" class="vehicle-modal">{{$vechile->color}}</div></td>
                    <td> 
                      <span class="menu-icon">
                      <span class="menu-icon">
                        <a href="{{route('admin.vehicle.edit',['id' => $vechile->vechile_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteVechile" data-id="{{$vechile->vechile_id}}"><i class="mdi mdi-delete"></i></a>
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
             {{ $vechiles->links('pagination::bootstrap-4') }}
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
  $('.deleteVechile').on('click', function() {
    var user_id = $(this).attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Vehicle?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/vehicle/delete/" + user_id,
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

</script>

@stop
