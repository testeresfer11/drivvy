@extends('admin.layouts.app')
@section('title', 'View User')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Users</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.user.list') }}">Users</a></li>
                <li class="breadcrumb-item active" aria-current="page">View User</li>
            </ol>
        </nav>
    </div>
@endsection
@section('content')
    <div>
        <h4 class="user-title">View User</h4>
        <div class="card">
            <div class="card-body">
                <form class="forms-sample">
                    <div class="form-group">
                        <div class="row align-items-center">
                            
                            <div class="col-12 col-md-8">
                                <div class="response-data ml-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Name :</span> <span
                                            class="text-muted" id="userName">{{ $user->first_name }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Email :</span> <span
                                            class="text-muted" id="userEmail">{{ $user->email ?? '' }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Phone Number :</span> <span
                                            class="text-muted" class="userPhone">{{ $user->driverdetail ? $user->driverDetail->phone_number ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Address :</span> <span
                                            class="text-muted" id="userAddress">{{ $user->driverDetail ? $user->driverDetail->address ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Pin Code :</span> <span
                                            class="text-muted" id="userPinCode">{{ $user->driverDetail ? $user->driverDetail->zip_code ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Date &amp; time :</span> <span
                                            class="text-muted" id="userDateTime">{{ $user->created_at->format('Y-m-d H:i:s') }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Truck Number :</span> <span
                                            class="text-muted" id="truckNumber">{{ $user->driverDetail ? $user->driverDetail->truck_number ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Total Number of Trucks :</span> <span
                                            class="text-muted" id="totalTrucks">{{ $user->driverDetail ? $user->driverDetail->total_no_of_truck ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Truck Type :</span> <span
                                            class="text-muted" id="truckType">{{ $user->driverDetail ? $user->driverDetail->truck_type ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Registration Date :</span> <span
                                            class="text-muted" id="registrationDate">{{ $user->driverDetail ? $user->driverDetail->registration_date ?? 'N/A' : 'N/A' }}</span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-12 col-md-4 text-center">
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">License Card :</span></h6>
                                @if ($user->driverDetail && $user->driverDetail->license_card)
                                    <img src="{{ asset('storage/license_card/' . $user->driverDetail->license_card) }}" alt="License Card" class="img-fluid img-thumbnail"
                                        data-toggle="modal" data-target="#licenseCardModal">
                                @else
                                    <p class="text-muted">N/A</p>
                                @endif
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Vehicle Insurance :</span></h6>
                                @if ($user->driverDetail && $user->driverDetail->vehicle_insurance)
                                    <img src="{{ asset('storage/vehicle_insurance/' . $user->driverDetail->vehicle_insurance) }}" alt="Vehicle Insurance" class="img-fluid img-thumbnail"
                                        data-toggle="modal" data-target="#vehicleInsuranceModal">
                                @else
                                    <p class="text-muted">N/A</p>
                                @endif
                            </div>
                            <div class="col-12 col-md-4 text-center">
                                <h6 class="f-14 mb-1"><span class="semi-bold qury">Passport :</span></h6>
                                @if ($user->driverDetail && $user->driverDetail->passport)
                                    <img src="{{ asset('storage/passport/' . $user->driverDetail->passport) }}" alt="Passport" class="img-fluid img-thumbnail"
                                        data-toggle="modal" data-target="#passportModal">
                                @else
                                    <p class="text-muted">N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modals for full size images -->
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <img class="img-fluid" 
                        @if (isset($user->driverDetail) && !is_null($user->driverDetail->profile)) 
                            src="{{ asset('storage/images/' . $user->driverDetail->profile) }}"
                        @else
                            src="{{ asset('admin/images/faces/face15.jpg') }}" 
                        @endif
                        onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                        alt="User profile picture">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="licenseCardModal" tabindex="-1" role="dialog" aria-labelledby="licenseCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="licenseCardModalLabel">License Card Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($user->driverDetail && $user->driverDetail->license_card)
                    <img src="{{ asset('storage/license_card/' . $user->driverDetail->license_card) }}" alt="License Card" class="img-fluid">
                @else
                    <p class="text-muted">N/A</p>
                @endif
            </div>
        </div>
    </div>
</div>


    <div class="modal fade" id="vehicleInsuranceModal" tabindex="-1" role="dialog" aria-labelledby="vehicleInsuranceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleInsuranceModalLabel">Vehicle Insurance Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($user->driverDetail && $user->driverDetail->vehicle_insurance)
                    <img src="{{ asset('storage/vehicle_insurance/' . $user->driverDetail->vehicle_insurance) }}" alt="Vehicle Insurance" class="img-fluid">
                @else
                    <p class="text-muted">N/A</p>
                @endif
            </div>
        </div>
    </div>
</div>

    <div class="modal fade" id="passportModal" tabindex="-1" role="dialog" aria-labelledby="passportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passportModalLabel">Passport Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if ($user->driverDetail && $user->driverDetail->passport)
                    <img src="{{ asset('storage/passport/' . $user->driverDetail->passport) }}" alt="Passport" class="img-fluid">
                @else
                    <p class="text-muted">N/A</p>
                @endif
            </div>
        </div>
    </div>
</div>

                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $('.modal').on('show.bs.modal', function (e) {
            $('.modal').not($(this)).each(function () {
                $(this).modal('hide');
            });
        });
    </script>
@endsection
