@extends('admin.layouts.app')
@section('title', 'Fares')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Fares</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Fare</a></li>
        <li class="breadcrumb-item active" aria-current="page">Fares</li>
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
            <h4 class="card-title">Fares Management</h4>
            <a href="{{route('admin.fare.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add Fare</span></button></a>
          </div>
          <div class="custom-search">
              <form action="{{ route('admin.fare.search') }}" method="GET">
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
                  <th> Sr No. </th>
                  <th> City </th>
                  <th> Base fare </th>
                  <th> Cost Per Kilometer </th>
                  <th> Cost per minute </th>
                  <th> Service Type  </th>
                  <th> Actions </th>
                </tr>
              </thead>
              <tbody>
                
                @forelse ($fares as $key => $fare)
                
                  <tr>
                    <td>{{$key+1}}</td>
                    <td> {{$fare->city}} </td>
                    <td>{{$fare->base_fare}}</td>
                    <td>{{$fare->cost_per_kilometer}}</td>
                    <td>{{$fare->cost_per_minute}}</td>
                    <td>{{$fare->service_type }}</td>
                    <td>
                      <span class="menu-icon">
                        <!-- <a href="{{route('admin.fare.view',['id' => $fare->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span> &nbsp;&nbsp;&nbsp; -->
                      <span class="menu-icon">
                        <a href="{{route('admin.fare.edit',['id' => $fare->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deletefare" data-id="{{$fare->id}}"><i class="mdi mdi-delete"></i></a>
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
             {{ $fares->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>




  @endsection
@section('scripts')
<script>
  $('.deletefare').on('click', function() {
    var user_id = $(this).attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the fare?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/fare/delete/" + user_id,
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
