@extends('admin.layouts.app')
@section('title', 'Reports')
<style>
    .table tbody tr td.mw {
    min-width: 450px;
    overflow-wrap: break-word;
    white-space: normal;
}
select.form-control {
    width: fit-content !important;
}

</style>
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Reports Management</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Reports</a></li>
        <li class="breadcrumb-item active" aria-current="page">Reports</li>
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
            <h4 class="card-title">Reports Management</h4>
          </div>
          <div class="custom-search">
            <form action="{{ route('admin.reports.users') }}" method="GET" id="searchForm">
                <div class="d-flex align-items-center search-gap">
                  

                    <!-- Date Filter -->
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

                            <input type="text" name="search" value="{{ request()->search }}" placeholder="Search..." class="form-control">
                    <!-- Status Filter -->
                         <select name="status" class="form-control" style="width: fit-content;">
                                <option value="" {{ request()->status === "" ? 'selected' : '' }}>All</option>
                                <option value="0" {{ request()->status == 0 ? 'selected' : '' }}>Unresolved</option>
                                <option value="1" {{ request()->status == 1 ? 'selected' : '' }}>Resolved</option>
                                <option value="2" {{ request()->status == 2 ? 'selected' : '' }}>False Complaint</option>
                            </select>

                    
                    <button type="submit" class="btn default-btn btn-md">Search</button>
                 <button type="button" class="btn default-btn btn-md" id="resetBtn">Reset</button>
              </div>
          </form>

          <script>
              // Reset button click event
              document.getElementById('resetBtn').addEventListener('click', function() {
                  // Reset form inputs
                  document.getElementById('searchForm').reset();

                  // Redirect to the same page to clear query parameters
                  window.location.href = "{{ route('admin.reports.users') }}";  
              });
          </script>

          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
                    <thead>
                        <tr>
                            <th>Ride ID</th>
                            <th>Ride Location</th>
                            <th>User</th>
                            <th>Driver</th>
                            <th>Report About</th>
                            <th>Description</th>
                            <th>Reported at</th>
                            <th style="width: 200px;">Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $data)
                            <tr>
                             <td>
                                <a href="{{ route('admin.ride.view', ['id' => $data->ride->ride_id]) }}">
                                    {{ $data->ride->ride_id }}
                                </a>
                            </td>

                                <td class="mw">{{ $data->ride->arrival_city }} - {{ $data->ride->departure_city }}</td>
                                <td>{{ $data->passenger->first_name ?? 'N/A' }}</td>
                                <td>{{ $data->driver->first_name ?? 'N/A' }}</td>
                                <td>{{ $data->report->type }}</td>
                               <td >{{ $data->description }}</td>

                                <td>{{ $data->created_at->diffForHumans() }}</td>
                                
                                <!-- Status Dropdown -->
                               <td>
                                    @if($data->status == 0)
                                        <!-- If unresolved, show dropdown -->
                                        <select class="form-control changeStatus" data-id="{{ $data->id }}">
                                            <option value="0" {{ $data->status == 0 ? 'selected' : '' }}>Unresolved</option>
                                            <option value="1">Resolved</option>
                                            <option value="2">Mark As False</option>
                                        </select>
                                    @elseif($data->status == 1)
                                    <span class="badge badge-success">Resolved</span>
                                    
                                       @else 
                                        <span class="badge badge-danger"> False Complaint</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
          </div>
          <div class="custom_pagination">
              {{ $reports->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')

<script>
    
  $(document).on('change', '.changeStatus', function() {
      var report_id = $(this).data('id');
      var new_status = $(this).val();

      Swal.fire({
          title: "Are you sure?",
          text: "You want to update the report status?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, update it!"
      }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/reports/changeStatus/" + report_id,
                  type: "POST",
                  data: {
                      _token: "{{ csrf_token() }}",  // Add CSRF token
                      status: new_status
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
                      toastr.error('Something went wrong!');
                  }
              });
          } else {
              location.reload();
          }
      });
  });
</script>
@endsection
