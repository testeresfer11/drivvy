@extends('admin.layouts.app')
@section('title', 'Payouts')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Payouts</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Payouts</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payouts</li>
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
            <h4 class="card-title">Payouts Management</h4>
            <!-- <a href="{{route('admin.ride.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
              <form action="{{ route('admin.payout.completed') }}" method="GET" id="searchForm">
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
                    window.location.href = "{{ route('admin.payout.completed') }}";  // Redirect to your completed payouts page
                });
            </script>

          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
                                <thead>
                                    <tr>
                                        <th> Sr.no </th>
                                        <th> User </th>
                                         <th> Email </th>
                                        <th> Amount </th>
                                         <th> Platform fee (%)</th>
                                          <th>final Amount </th>
                                           <th>Payout Amount</th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payouts as $payout)
                                           
                                        <tr id="payout-{{ $payout->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $payout->driver_name ?? 'N/A' }}</td>
                                             <td>{{ $payout->driver_email ?? 'N/A' }}</td>
                                             <td>${{ number_format($payout->total) }}</td>
                                               <td>{{ $payout->platform_fee ?? 'N/A' }}</td>
                                            
                                             <td>${{ number_format($payout->amount) }}</td>
                                             <td>${{ number_format($payout->amount_paid_by_admin) }}</td>
                                            
                                            <td >
                                            <div class="pay-out-btn">
        
                                            <button type="button" class="btn btn-outline-primary f-12 m-btn" id="make-payment-status" data-payout-id="{{$payout->id}}"> PAYMENT PROOF</button>
                                        </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                            {{ $payouts->appends(request()->input())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" id="payout_id" name="payout_id">
                    <div class="col-12">
                        <div class="form-group">
                            <label class="payment_slip">Payment Slip</label>
                            <img id="payment_slip_preview" src="" alt="Payment Slip" class="user-details-icon w-100 rounded">
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method</label>
                            <input type="text" id="payment_method" name="payment_method" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Amount</label>
                            <input type="text" id="total_amount" name="payment_method" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Platform fee(%)</label>
                            <input type="text" id="Platform_fee" name="payment_method" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Final Amount</label>
                            <input type="text" id="final_amount" name="payment_method" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payout Amount</label>
                            <input type="text" id="payout_amount" name="payment_method" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label class="payment_date">Date</label>
                            <input type="date" id="payment_date" name="payment_date" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label class="payment_status">Payment Status</label>
                            <input type="text" class="form-control" id="payment_status" name="payment_status" value="completed" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                
            </div>
        </div>
    </div>
</div>


@endsection
@section('scripts')
<script>

    $(document).on('click', '#make-payment-status', function (e) {
    var payout_id = $(this).data('payout-id');

    // Fetch payout data via AJAX or have the data available
    $.ajax({
        url: '/admin/payout/details/' + payout_id, // Adjust URL as necessary
        method: 'GET',
        success: function(response) {
            // Assuming response contains the payout data
            $("#payout_id").val(response.id);
            $("#payment_slip").val(response.payment_slip);
            $("#payment_method").val(response.payment_method);
            $("#Platform_fee").val(response.platform_fee);
            $("#total_amount").val(response.total);
            $("#payout_amount").val(response.amount_paid_by_admin);
             $("#final_amount").val(response.amount);
            $("#payment_date").val(response.payment_date);
            $("#payment_slip_preview").attr('src', 'https://ride.drivvy.com.au/storage/payment/' + response.payment_slip).show(); // Use '/storage/payment/' to access the symlinked path
            $('#paymentModal').modal('show');
        },
        error: function() {
            alert('Failed to fetch payout details.');
        }
    });
});


    function resetUrl() {
        // Get the current URL
        const url = new URL(window.location.href);
        
        // Remove all query parameters
        url.search = '';

        // Redirect to the modified URL
        window.location.href = url.toString();
    }

    $(document).on('click', '#make-payment-status', function (e) {
        var payout_id = $(this).data('payout-id');
        $("#payout_id").val(payout_id);
        $('#paymentModal').modal('show');
    });


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
