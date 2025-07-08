@extends('admin.layouts.app')
@section('title', 'Cars')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Cars</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Cars</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cars</li>
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
            <h4 class="card-title">Cars Management</h4>
            <!-- <a href="{{route('admin.cars.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add Car</span></button></a> -->
          </div>
          <div class="custom-search">
            <form action="{{ route('admin.cars.search') }}" method="GET" id="searchForm">
                <div class="d-flex align-items-center search-gap">
                    <input type="text" name="search" value="{{ request()->search }}" placeholder="Search...">
                    <button type="submit" class="btn default-btn btn-md">Search</button>
                    <button type="button" class="btn default-btn btn-md" id="resetBtn">Reset</button>
                </div>
            </form>
        </div>

        <script>
            // Reset button click event
            document.getElementById('resetBtn').addEventListener('click', function() {
                // Reset form inputs
                document.getElementById('searchForm').reset();
                
                // Optionally, you can remove query parameters by redirecting to the base URL
                window.location.href = "{{ route('admin.cars.list') }}";  // Redirect to your base cars list page
            });
        </script>

          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> User Name </th>
                  <th> Make </th>
                  <th> Model </th>
                  <th> Type </th>
                  <th> Colors </th>
                  <th> License Plate </th>
                  <th> Year</th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($cars as $keys => $car)
                
                  <tr id={{$keys+1}}>
                  <td> <a href="{{route('admin.user.view',['id' => $car->user_id])}}"> {{$car->first_name}} {{$car->last_name}}</a> </td>
                  <td> {{$car->make}} </td>
                    <td> {{$car->model}} </td>
                    <td>{{$car->type}}</td>
                    <td>{{$car->color}}</td>
                    <td>{{$car->license_plate}}</td>
                    <td>{{$car->year}}</td>
                    
                    <td> 
                      <span class="menu-icon">
                      <span class="menu-icon">
                        <a href="{{route('admin.cars.edit',['id' => $car->car_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteCar" data-id="{{$car->car_id}}"><i class="mdi mdi-delete"></i></a>
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
             {{ $cars->links('pagination::bootstrap-4') }}
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
  $('.deleteCar').on('click', function() {
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
                  url: "/admin/cars/delete/" + user_id,
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
