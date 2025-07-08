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
                                <div class="row user-details-data">
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Driver Name :</span> <span
                                            class="text-muted" id="userName">{{ $ride->first_name }} {{$ride->last_name}}</span></h6>
                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Origin :</span> <span
                                    class="text-muted" id="lastName">{{ $ride->departure_city }}</span></h6>
                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Stopover 1</span> <span
                                            class="text-muted" id="userName">{{ $ride->stopover1 ?? '' }}</span></h6>
                                  </div>
                                   <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Stopover 2 :</span> <span
                                            class="text-muted" id="userName">{{ $ride->stopover2 ?? '' }}</span></h6>
                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Destination :</span> <span
                                            class="text-muted" id="userName">{{ $ride->arrival_city ?? '' }}</span></h6>
                                  </div>
                                   
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Departure Time :</span> <span
                                    class="text-muted" id="userName">{{ convertDate($ride->departure_time) ?? '' }}</span></h6>
                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Arrival Time :</span> <span
                                    class="text-muted" id="userName">{{ convertDate($ride->arrival_time) ?? '' }}</span></h6> 
                                  </div>
                                 
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Smoking Allowed :</span> <span
                                    class="text-muted" id="userName">{{ $ride->smoking_allowed  }}</span></h6>  
                                  </div>
                                  <div class="col-12 col-md-3 mb-4"> 
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Pets Allowed :</span> <span class="text-muted" id="userName">
                                            {{ $ride->pets_allowed === 'Avoid pets at home' ? 'Not allowed' : 'Allowed' }}
                                        </span></h6>

                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Music Preference :</span> <span
                                    class="text-muted" id="userName">{{ $ride->music_preference ?? 'N/A' }}</span></h6> 
                                  </div>
                                  <div class="col-12 col-md-3 mb-4">
                                   
                                  </div>
                                  <div class="col-12 col-md-12 mb-4"> 
                                   
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
    </div>


    <div>
        <h4 class="user-title">Car details</h4>
        <div class="card">
            <div class="card-body">
                <form class="forms-sample">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-12">
                                <div class="response-data car-details ml-4">
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury"> Make:</span> <span
                                    class="text-muted" id="userName">{{ $ride->make }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Model :</span> <span
                                    class="text-muted" id="lastName">{{ $ride->model }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">Year :</span> <span
                                    class="text-muted" id="userName">{{ $ride->year ?? '' }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">License Plate:</span> <span
                                    class="text-muted" id="userName">{{ $ride->license_plate ?? '' }}</span></h6>
                                    <h6 class="f-14 mb-1"><span class="semi-bold qury">color :</span> <span
                                    class="text-muted" id="userName">{{ $ride->color ?? '' }}</span></h6> 
                                  
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>  
    </div>      

    <div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <x-alert />
       
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h4 class="card-title">Passengers List</h4>
            <!-- <a href="{{route('admin.user.add')}}"><button type="button" class="btn default-btn btn-md">
              <span class="menu-icon">+ Add User</span></button></a> -->
          </div>
          <div class="table-responsive">
            <table class="table table-striped" id="filterData">
              <thead>
                <tr>
                  <th> Passenger ID </th>
                  <th> Passenger Name </th>
                  <th> Seats Booked </th>
                  <th> Booking Date </th>
                
                  <th> Rating </th>
                  <th> Comment </th>
                 
                </tr>
              </thead>
              <tbody>
                
                @forelse ($passengers as $key => $passenger)
                
                  <tr>
                    <td>{{$passenger->passenger_id}}</td>
                    <td>{{$passenger->passenger_name}}</td>
                    <td>{{$passenger->seat_count}}</td>
                    <td>{{$passenger->booking_date}}</td>
                   
                    <td>@if($passenger->rating != "")   {{$passenger->rating}}/5 @else Not added yet @endif</td>
                    <td>@if($passenger->comment != "")   {{$passenger->comment}}/5 @else Not added yet @endif</td>
                    
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
             {{ $passengers->links('pagination::bootstrap-4') }}
            </div>
        </div>
      </div>
    </div>
  </div>
       
@endsection
