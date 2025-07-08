@extends('admin.layouts.app')
@section('title', 'Pending Refunds')

@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Pending Refunds</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pending Refunds</li>
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
            <h4 class="card-title">Pending Refunds Management</h4>
            <!-- <a href="{{route('admin.ride.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="custom-search">
              <form action="{{ route('admin.payout.pending.refund') }}" method="GET" id="searchForm">
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
                    window.location.href = "{{ route('admin.payout.pending.refund') }}";  // Redirect to your completed payouts page
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
							            <th> Payment Method </th>
							            <th> Transaction Id </th>
							            <th> Amount </th>
							            <th> Platform Fee </th>
							            <th> Final Amount </th>
							            <th> Action </th>
							        </tr>
							    </thead>
							    <tbody>
							        @forelse ($payouts as $payout)
							            <tr id="payout-{{ $payout->id }}">
							                <td>{{ $loop->iteration }}</td>
							                <td>{{ $payout->first_name ?? 'N/A' }}</td>
							                <td>{{ $payout->email ?? 'N/A' }}</td>
							                <td>{{ $payout->payment_method ?? 'N/A' }}</td>
							                <td>{{ $payout->transaction_id ?? 'N/A' }}</td>
							                <td>{{ $payout->divided_amount }}</td>
                                            <td>{{ $platform_fee }} %</td>
                                            <!-- Calculate Final Amount as amount - platform_amount -->
                                            <td>${{ $payout->refunded_amount}}</td>

							                <td>
							                    <div class="pay-out-btn">
							                        <!-- Pass the final amount into a data attribute -->
							                        <button type="button" class="btn btn-outline-primary f-12 m-btn" 
							                                id="make-payment-status" 
							                                data-payout-id="{{$payout->payment_id}}" 
                                                             data-amount="{{ $payout->amount }}"
                                                                data-platform-fee="{{ $platform_fee }}"
							                                data-final-amount="{{$payout->refunded_amount}}">
							                            PAYMENT PROOF
							                        </button>
							                    </div>
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
                <form action="{{ route('admin.payout.mark_complete.refund') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <!-- Profile Upload Section -->
                            <div class="row">
                            <input type="hidden" id="payout_id" name="payment_id">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label class="payment_slip">Upload Payment Slip</label>
                                        <input type="file" id="payment_slip" name="payment_slip" class="nme form-control" placeholder="Upload Image" accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label for="payment_method">Payment Method</label>
                                        <select id="payment_method" name="payment_method" class="form-control" style="padding: unset";>
                                            <option value="">Select Payment Method</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="paypal">PayPal</option>
                                            <option value="stripe">Stripe</option>
                                        </select>
                                    </div>
                                   <div class="form-group">
                                        <label class="payment_slip">Amount To Refund</label>
                                        <input type="number" id="refunded_amount" name="refunded_amount" class="nme form-control" placeholder="Refunded Amount" >
                                    </div>
                                    <div class="form-group">
                                        <label class="payment_date">Date</label>
                                        <input type="date" id="payment_date" name="payment_date" class="nme form-control">
                                    </div>
                                    <div class="form-group">
                                        <!-- <label class="payment_status">Payment Status</label> -->
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
         var payout_amount = $(this).data('final-amount');
        
        $("#payout_id").val(payout_id);
          $("#refunded_amount").val(payout_amount);
        $('#paymentModal').modal('show');
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
