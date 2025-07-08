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
                <th> <p>S. No. </p></th>
                <th> <p>Type </p></th>
                <th> <p>Content </p></th>
                <th> <p>Action </p></th>
              </tr>
            </thead>
             <tbody>
                @forelse ($policy as $keys => $value)
                
                  <tr>
                  <td>{{$keys+1}}</td>
                    <td>{{$value->type}}</td>
                    

                     <td>{{ $value->content ?? 'N/A'}}</td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.policies.edit',['type' => $value->id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                     
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
          {{ $policy->links('pagination::bootstrap-4') }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }

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
          url: "/admin/contentpage/delete/" + user_id,
          type: "GET",
          success: function(response) {
            if (response.status == "success") {
              toastr.success(response.message);
              // $('table.table-striped tr#+'{$user_id}).remove();
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
          url: "/admin/contentpage/changeStatus",
          type: "GET",
          data: {
            id: id,
            status: action
          },
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

