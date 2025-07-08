@extends('admin.layouts.app')
@section('title', 'Document')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Document</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Document</a></li>
        <li class="breadcrumb-item active" aria-current="page">Document</li>
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
            <h4 class="card-title">Document Management</h4>
            <!-- <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
            <form action="{{ route('admin.document.search') }}" method="GET" id="searchForm">
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
        window.location.href = "{{ route('admin.document.list') }}";
    });
</script>

          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Sr No </th>
                  <th> Name </th>
                  <th> Document </th>
                  <th> Status </th>
                  <th> Verify  </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($documents as $keys => $document)
                
                  <tr>
                  <td class="py-1"> {{$keys+1}}</td>
                  <td> {{$document->first_name ?? "-"}}   </td>
                  <td>
                  @if($document->id_card != "" && str_contains($document->id_card, 'https://dummyimage.com/'))
                        <a href="{{$document->id_card}}" target="_blank"> <img class="img-lg" src="{{$document->id_card}}"
                                alt="User ID Card" width="500" height="500"></a>
                  </td>
                    @else
                    <a href="{{url('/')}}/storage/id_card/{{$document->id_card}}" target="_blank">
                    <img class="img-lg" src="{{url('/')}}/storage/id_card/{{$document->id_card}}"
                                alt="User ID Card" width="400" height="400">
                    </a>
                  </td>
                    @endif   
                    
                    <td>{{$document->verify_id}}</td>
                    <td>
                      <button type="submit" class="btn green-btn btn-md switch" data-id='{{$document->user_id}}' data-value="2">Approve</button>
                      <button type="submit" class="btn red-btn btn-md switch" data-id='{{$document->user_id}}' data-value="3">Decline</button>
                    </td>

                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.user.view',['id' => $document->user_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                     {{--<span class="menu-icon">
                        <a href="{{route('admin.user.edit',['id' => $document->user_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$document->user_id}}"><i class="mdi mdi-delete"></i></a>
                      </span> --}}
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
                url: "/admin/document/changeStatus",
                type: "GET",
                data: { id: id, status: status },
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
