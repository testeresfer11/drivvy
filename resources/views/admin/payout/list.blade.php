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
                <form action="{{ route('admin.payout.pending') }}" method="GET" id="searchForm">
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
                    window.location.href = "{{ route('admin.payout.pending') }}";  // Redirect to your pending payouts page
                });
            </script>

          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
                                <thead>
                                    <tr>
                                        <th> Sr.no </th>
                                        <th> User </th>
                                         <th> Email </th>
                                        <th>Price Per Seat </th>
                                        {{--<th> Amount Paid By User </th>--}}
                                        <th> Platform fee </th>
                                        <th> Final Amount </th>
                                        <th> Action </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payouts as $payout)
                                          
                                        <tr id="payout-{{ $payout->id }}">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $payout->driver_name ?? 'N/A' }}</td>
                                             <td>{{ $payout->driver_email ?? 'N/A' }}</td>
                                             <td>${{ $payout->amount }}</td>
                                             {{--<td>${{ $payout->total }}</td>--}}
                                            <td>N/A</td>
                                            <td>${{ $payout->amount }}</td>
                                            <td >
                                            <div class="pay-out-btn">
        
                                            <button type="button" class="btn btn-outline-primary f-12 m-btn" 
                                            id="make-payment-status" 
                                            data-payout-id="{{$payout->id}}" 
                                             data-payout-total="{{$payout->total}}"
                                            data-payout-amount="{{$payout->amount}}" 
                                            data-platform-fee="{{$platform_fee}}">
                                        MAKE PAYMENT
                                    </button>
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
            <form action="{{ route('admin.payout.mark_complete') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <!-- Profile Upload Section -->
                    <div class="row">
                        <input type="hidden" id="payout_id" name="payout_id">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="payment_slip">Upload Payment Slip</label>
                                <input type="file" id="payment_slip" name="payment_slip" class="nme form-control" placeholder="Upload Image" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label for="payment_method">Payment Method</label>
                                <select id="payment_method" name="payment_method" class="form-control" style="padding: unset;">
                                    <option value="">Select Payment Method</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="paypal">PayPal</option>
                                </select>
                            </div>

                            <!-- Add payout_amount input field here -->
                            {{--<div class="form-group">
                                <label for="payout_amount">Total Amount</label>
                                <input type="number" id="payout_amount" name="payout_amount" class="form-control" readonly>
                            </div>--}}

                            {{--<div class="form-group">
                                <label class="platform_fee">Platform Fee (%)</label>
                                <div class="form-group">
                                    
                                    <input type="number" id="platform_fee" name="platform_fee" class="form-control" step="0.01" placeholder="Enter platform fee" value="{{ $platform_fee }}" onchange="updateFinalAmount()" oninput="updateFinalAmount()"readonly>
                                </div>

                            </div>--}}

                            <div class="form-group">
                                <label class="final_amount">Final Amount</label>
                                <input type="number" id="final_amount" name="final_amount" class="form-control" readonly>
                            </div>

                            <div class="form-group">
                                <label class="payment_date">Date</label>
                                <input type="date" id="payment_date" name="payment_date" class="nme form-control">
                            </div>
                            <div class="form-group">
                                <input type="hidden" class="form-control" id="payment_status" name="payment_status" value="completed">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>

    // Function to reset the URL (removes query parameters)
// Function to reset the URL (removes query parameters)
function resetUrl() {
    const url = new URL(window.location.href);
    url.search = '';  // Remove all query parameters
    window.location.href = url.toString();  // Redirect to the modified URL
}

// Function to update the final amount based on platform fee
function updateFinalAmount() {
    var payoutAmountElement = document.getElementById('payout_amount');
    var platformFeeElement = document.getElementById('platform_fee');
    var finalAmountElement = document.getElementById('final_amount');

    // Ensure that elements exist in the DOM
    if (!payoutAmountElement || !platformFeeElement || !finalAmountElement) {
        console.error("Missing elements for payout_amount, platform_fee, or final_amount");
        return;
    }

    var payoutAmount = parseFloat(payoutAmountElement.value);
    var platformFeePercentage = parseFloat(platformFeeElement.value) || 0;

    // Check if payoutAmount and platformFeePercentage are valid
    if (isNaN(payoutAmount) || isNaN(platformFeePercentage)) {
        console.error("Invalid input for payout amount or platform fee.");
        return;
    }

    // Calculate platform fee and final amount
    var platformFee = (payoutAmount * platformFeePercentage) / 100;
    var finalAmount = payoutAmount - platformFee;

    // Update final amount field
    finalAmountElement.value = finalAmount.toFixed(2);  // Display final amount with 2 decimals
}

// When the "MAKE PAYMENT" button is clicked, populate the modal
$(document).on('click', '#make-payment-status', function (e) {
    var payout_id = $(this).data('payout-id');
    var payout_amount = $(this).data('payout-total');
     var final_amount = $(this).data('payout-amount');
    var platform_fee = $(this).data('platform-fee');

    // Set values in the modal
    $("#payout_id").val(payout_id);
    $("#payout_amount").val(payout_amount); 
     $("#final_amount").val(final_amount);   // Set payout amount in the modal
    $("#platform_fee").val(platform_fee);    // Set platform fee in the modal

  
    
    // Show the modal
    $('#paymentModal').modal('show');
});

// Event listener to update the final amount when the platform fee is changed
$(document).on('input', '#platform_fee', function() {
    updateFinalAmount();
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
