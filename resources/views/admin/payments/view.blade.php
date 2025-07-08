@extends('admin.layouts.app')
@section('title', 'View Ride')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Ride</h3>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.ride.list') }}">Ride</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Ride</li>
            </ol>
        </nav>
    </div>
@endsection
@section('content')
    <div>
        <h4 class="user-title">View Ride</h4>
        <div class="card">
            <div class="card-body">
                <form class="forms-sample">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-12">
                                <div class="response-data ml-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Driver Name :</span> <span
                                            class="text-muted" id="userName">{{ $ride->first_name }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Origin :</span> <span
                                    class="text-muted" id="lastName">{{ $ride->departure_city }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Destination :</span> <span
                                            class="text-muted" id="userName">{{ $ride->arrival_city ?? '' }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Departure Time :</span> <span
                                    class="text-muted" id="userName">{{ convertDate($ride->departure_time) ?? '' }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Arrival Time :</span> <span
                                    class="text-muted" id="userName">{{ convertDate($ride->arrival_time) ?? '' }}</span></h6> 
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Seats Available :</span> <span
                                    class="text-muted" id="userName">{{ $ride->available_seats ?? '' }}</span></h6>  
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                {{-- <div class="text-end">
                    <a href="{{ route('admin.user.list') }}"><button
                            class="btn default-btn btn-md mr-2">Cancel</button></a>
                </div> --}}
            </div>
        </div>
       
@endsection
