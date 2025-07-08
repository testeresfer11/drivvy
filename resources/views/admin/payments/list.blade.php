@extends('admin.layouts.app')
@section('title', 'Payment')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Payment Management</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Payment</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payment</li>
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
            <h4 class="card-title">Payment Management</h4>
            <!-- <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
           <form action="{{ route('admin.payments.list') }}" method="GET" id="searchForm">
              <div class="d-flex align-items-center search-gap">
                 
                  
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
                                </script>                  <select name="payment_method" class="form-control">
                      <option value="">All</option>
                      <option value="stripe" {{ request()->get('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                      <option value="paypal" {{ request()->get('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                  </select>
                  
                  <input type="text" name="search" placeholder="Search..." value="{{ request()->get('search') }}" class="form-control">

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
                  window.location.href = "{{ route('admin.payments.list') }}";  
              });
          </script>

          </div>
          <div class="table-responsive">
           <table class="table table-striped" id="filterData">
                <thead>
                    <tr>
                        <th> Sr No. </th>
                        <th> Passenger Name</th>
                        <th> Ride ID </th>
                        <th> Booking ID </th>
                        <th> Amount </th>
                        <th> Payment Date</th>
                        <th> Payment Method</th>
                        <th> Status</th>
                        <th> Seat Request Status</th>
                        <th> Automatic Refund Processed</th>
                        <th> Passenger Cancel > 24hrs</th>
                        <th> Passenger Cancel < 24hrs</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $key => $payment)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $payment->first_name ?? 'N/A' }}</td>
                            <td><a href="{{ route('admin.ride.view', $payment->ride_id) }}">{{ $payment->ride_id }}</a></td>
                            <td>{{ $payment->booking_id }}</td>
                            <td>${{ number_format($payment->amount, 2) }}</td>
                            <td>{{ convertDate($payment->payment_date) }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>
                                @if ($payment->status === 'COMPLETED')
                                    Succeeded
                                @else
                                    {{ $payment->status }}
                                @endif
                            </td>
                            <td>{{ $payment->booking_status }}</td>
                            <td>
                                @if (in_array($payment->booking_status, ['confirm', 'completed', 'pending']))
                                    NA
                                @else
                                    {{ $payment->is_automatic_refunded == 1 ? 'Yes' : 'No' }}
                                @endif
                            </td>
                            <td>{{ $payment->cancel_before_24 == 1 ? 'Yes' : 'No' }}</td>
                            <td>{{ $payment->cancel_after_24 == 1 ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="no-record"> <center>No record found </center></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

          </div>
          <div class="custom_pagination">
             {{ $payments->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
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
