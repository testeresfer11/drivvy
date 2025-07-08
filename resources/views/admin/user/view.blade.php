<style type="text/css">
    /* Adjust text color for Document Files section */
.documents-section h6 span.qury {
    color: #333; /* Change to your desired color */
}

.documents-section p.text-muted {
    color: #6c757d; /* Change to your desired color */
}

.documents-section .img-lg {
    border: 2px solid #ddd; /* Optional: Add border to images */
    border-radius: 8px; /* Optional: Add rounded corners */
}

/* Optional: Style for the tab buttons */
.tablinks {
    color: #007bff; /* Change to your desired color */
}

.tablinks.active {
    background-color: #007bff; /* Change to your desired color */
    color: #fff; /* Change to your desired color */
}


.bank-details {
    background-color: #f9f9f9; /* Light background for better contrast */
    border: 1px solid #e0e0e0; /* Subtle border */
    border-radius: 8px; /* Rounded corners */
    padding: 20px; /* Padding for spacing */
    margin: 20px 0; /* Margin to separate from other content */
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Light shadow for depth */
}

.detail-item {
    margin-bottom: 15px; /* Spacing between items */
}

.detail-item h6 {
    color: #333; /* Darker text for headings */
    font-weight: 600; /* Slightly bolder text */
}

.detail-item .text-muted {
    color: #666; /* Muted color for less emphasis */
    font-size: 14px; /* Consistent font size */
}


</style>
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
        <div class="card">
            <div class="card-body">
                <h3> Personal Details </h3>
                <form class="forms-sample">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col-12 col-md-3">
                                <div class="view-user-details">
                                    <div class="text-center">
                                        <img 
                                            class="user-details-icon w-100 rounded"
                                            @if($user->profile_picture != "")
                                                src="{{url('/')}}/storage/users/{{$user->profile_picture}}"
                                            @else
                                                 src="{{ asset('admin/images/user-image.webp') }}" 
                                            @endif
                                            alt="User profile picture">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="row user-details-data">
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">First Name :</span> <span class="text-muted" id="userName">{{ $user->first_name ?? '-' }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Last Name :</span> <span class="text-muted" id="lastName">{{ $user->last_name ?? '-'  }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Email :</span> <span class="text-muted" id="userName">{{ $user->email ?? '-' }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Phone Number :</span> <span class="text-muted" class="userPhone">{{ $user->country_code}}{{ $user->phone_number ?? '-'  }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Date &amp; Time :</span> <span class="text-muted" id="userDateTime">{{ convertDate($user->join_at) }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">DOB :</span> <span class="text-muted" id="userDateTime">{{ $user->dob ?? '-' }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Chattiness :</span> <span class="text-muted" id="userDateTime">{{ $user->chattiness ?? '-' }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Music :</span> <span class="text-muted" id="userDateTime">{{ $user->music ?? '-'}}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Smoking :</span> <span class="text-muted" id="userDateTime">{{ $user->smoking ?? '-' }}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-4 mb-4">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Pets :</span> <span class="text-muted" id="userDateTime">{{ $user->pets ?? '-'}}</span></h6>
                                    </div>
                                    <div class="col-12 col-md-12 mb-12">
                                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Bio :</span> <span class="text-muted" id="userDateTime">{{ $user->bio ?? '-'}}</span></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-body">
                <h3 class="mb-4">Documents Files : <span class="text-muted">{{ $user->verify_id }}</span></h3>
                <div class="documents-section">
                    @if($user->id_card)
                        <h6 class="f-14 mb-1"><span class="semi-bold qury">Document ID:</span></h6>
                        <a href="{{ str_contains($user->id_card, 'https://dummyimage.com/') ? $user->id_card : url('/storage/id_card/'.$user->id_card) }}" target="_blank">
                            <img class="img-lg" src="{{ str_contains($user->id_card, 'https://dummyimage.com/') ? $user->id_card : url('/storage/id_card/'.$user->id_card) }}" alt="User ID Card" width="400" height="400">
                        </a>
                       
                    @else
                        <p class="text-muted">No documents added yet.</p>
                    @endif
                </div>
            </div>
        </div>


        <div class="card mt-4">
            <div class="card-body">
                <h3 class="mb-4">Bank Details : <span class="text-muted"></span></h3>
                <div class="documents-section">

                    @if($bankDetails)

                        <div class="bank-details">
                       
                        
                        <div class="detail-item">
                            <h6 class="f-14 mb-1">
                                <span class="semi-bold qury">BSB Number</span>
                            </h6>
                            <span class="text-muted">&nbsp;&nbsp;&nbsp;{{ $bankDetails->B5B_number ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <h6 class="f-14 mb-1">
                                <span class="semi-bold qury">Account Number</span>
                            </h6>
                            <span class="text-muted">&nbsp;&nbsp;&nbsp;{{ $bankDetails->account_number ?? '-' }}</span>
                        </div>

                         <div class="detail-item">
                            <h6 class="f-14 mb-1">
                                <span class="semi-bold qury">Full Name</span>
                            </h6>
                            <span class="text-muted">&nbsp;&nbsp;&nbsp;{{ $bankDetails->full_name ?? '-' }}</span>
                        </div>

                        <div class="detail-item">
                            <h6 class="f-14 mb-1">
                                <span class="semi-bold qury">Paypal ID</span>
                            </h6>
                            <span class="text-muted">&nbsp;&nbsp;&nbsp;{{ $bankDetails->paypal_id ?? '-' }}</span>
                        </div>
                    </div>

                    @else
                        <p class="text-muted">No Bank Detail Added Yet.</p>
                    @endif
                </div>
            </div>
        </div>


                <div class="card mt-4">
                    <div class="card-body">
                        <h3 class="mb-4"> Rides Information</h3>
                        <div class="carpool-tabs">
                            <div class="tab border-0 bg-transparent">
                                <button class="tablinks" onclick="openTab(event, 'Cars')">Cars</button>
                                <button class="tablinks" onclick="openTab(event, 'Rides')">Rides</button>
                                <button class="tablinks" onclick="openTab(event, 'Bookings')">Bookings</button>
                                <button class="tablinks" onclick="openTab(event, 'Reviews')">Reviews</button>
                           
                            </div> 
                            <div id="Cars" class="tabcontent border-0">
                                <h3>Cars</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="filterData">
                                    <thead>
                                        <tr>
                                        <th> Make </th>
                                        <th> Model </th>
                                        <th> Year </th>
                                        <th> License Plate </th>
                                        <th> Color </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                        
                                        @forelse ($cars as $key => $car)
                                        
                                        <tr>
                                            <td> {{$car->make}} </td>
                                            <td>{{$car->model}}</td>
                                            <td>{{$car->year}}</td>
                                            <td>{{$car->license_plate}}</td>
                                            <td>{{$car->color}}</td>
                                            <td>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                    @if(count($cars) > 5)
                                    <form action="{{ route('admin.cars.search') }}" method="GET">
                                        <input type="hidden" value="{{$user->user_id}}" name="search" placeholder="Search...">
                                        <div class="text-end"><button type="submit" class="btn gradient-btn btn-md mt-4">View more</button></div>
                                    </form>

                                    @endif
                                </div>
                            </div>

                            <div id="Rides" class="tabcontent border-0">
                                <h3>Rides</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="filterData">
                                    <thead>
                                        <tr>
                                        <th> Profile </th>
                                        <th> Driver Name </th>
                                        <th> Origin </th>
                                        <th> Destination </th>
                                        <th> Departure Time </th>
                                        <th> Arrival Time </th>
                                        <th> Total Seats </th>
                                        <th> Seats Available </th>
                                        <th> Actions </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse ($rides as $key => $ride)
                                        
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td> {{$ride->first_name}} </td>
                                            <td>{{$ride->departure_city}}</td>
                                            <td>{{$ride->arrival_city}}</td>
                                            <td>{{$ride->departure_time}}</td>
                                            <td>{{$ride->arrival_time}}</td>
                                            <td>{{$ride->available_seats}}</td>
                                            <td>{{ $ride->seat_left}}</td>
                                            <td>
                                            <span class="menu-icon">
                                                <a href="{{route('admin.ride.view',['id' => $ride->ride_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                                            </span>&nbsp;&nbsp;&nbsp;
                                            <span class="menu-icon">
                                                <a href="{{route('admin.ride.edit',['id' => $ride->ride_id])}}" title="Edit" class="text-success"><i class="mdi mdi-pencil"></i></a>
                                            </span>&nbsp;&nbsp;
                                            <span class="menu-icon">
                                                <a href="#" title="Delete" class="text-danger deleteUser" data-id="{{$ride->ride_id}}"><i class="mdi mdi-delete"></i></a>
                                            </span> 
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                    @if(count($rides) > 5)
                                    <form action="{{ route('admin.ride.search') }}" method="GET">
                                        <input type="hidden" value="{{$user->user_id}}" name="search" placeholder="Search...">
                                        <div class="text-end"><button type="submit" class="btn gradient-btn btn-md mt-4">View more</button></div>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <div id="Bookings" class="tabcontent border-0">
                                <h3>Bookings</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="filterData">
                                    <thead>
                                        <tr>
                                        <th> Sr No. </th>
                                        <th> Passenger Name </th>
                                        <th> Ride ID </th>
                                        <th> Origin </th>
                                        <th> Destination </th>
                                        <th> Requested Seats </th>
                                        <th> Status </th>
                                        <th> Request Date </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse ($requests as $key => $request)
                                        
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td> {{$request->first_name}} </td>
                                            <td>{{$request->ride_id}}</td>
                                            <td>{{$request->departure_location}}</td>
                                            <td>{{$request->arrival_location}}</td>
                                            <td>{{$request->seat_count}}</td>
                                            <td>{{$request->status}}</td>
                                            <td>{{convertDate($request->booking_date)}}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                    @if(count($requests) > 5)
                                    <form action="{{ route('admin.requests.search') }}" method="GET">
                                        <input type="hidden" value="{{$user->user_id}}" name="search" placeholder="Search...">
                                        <div class="text-end"><button type="submit" class="btn gradient-btn btn-md mt-4">View more</button></div>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            
                            <div id="Reviews" class="tabcontent border-0">
                                <h3>Reviews</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="filterData">
                                    <thead>
                                        <tr>
                                        <th> Sr No. </th>
                                        <th> Reviewer Name</th>
                                        <th> Rating </th>
                                        <th> Comment </th>
                                        <th> Review Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse ($reviews as $key => $review)
                                        
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td> {{$review->reviewer_first_name}}  {{$review->reviewer_last_name}}</td>
                                            <td>{{$review->rating}}</td>
                                            <td>{{$review->comment}}</td>
                                            <td>{{convertDate($review->review_date)}}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                    @if(count($reviews) > 5)
                                    <form action="{{ route('admin.review.search') }}" method="GET">
                                        <input type="hidden" value="{{$user->user_id}}" name="search" placeholder="Search...">
                                        <div class="text-end"> <button type="submit" class="btn gradient-btn btn-md mt-4">View more</button></div>
                                    </form>
                                    @endif
                                </div>
                            </div>

                            <div id="Messages" class="tabcontent border-0">
                                <h3>Messages</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="filterData">
                                    <thead>
                                        <tr>
                                        <th> Driver Name </th>
                                        <th> Sender Name </th>
                                        <th> Receiver Name </th>
                                        <th> Message </th>
                                        <th> Date/Time </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse ($messages as $key => $message)
                                        
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td> {{$message->sender_name}} </td>
                                            <td> {{$message->receiver_name}} </td>
                                            <td>{{$message->content}}</td>
                                            <td>{{convertDate($message->timestamp)}}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="no-record"> <center>No record found </center></td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                    @if(count($messages) > 5)
                                    <form action="{{ route('admin.messages.ride-search') }}" method="GET">
                                        <input type="hidden" value="{{$user->user_id}}" name="search" placeholder="Search...">
                                       <div class="text-end"> <button type="submit" class="btn gradient-btn btn-md mt-4">View more</button></div>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
          
<script>
    function openTab(evt, tabName) {
        // Declare all variables
        var i, tabcontent, tablinks;

        // Get all elements with class="tabcontent" and hide them
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }

        // Get all elements with class="tablinks" and remove the class "active"
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }

        // Show the current tab, and add an "active" class to the button that opened the tab
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    // Get the element with id="defaultOpen" and click on it
    document.getElementsByClassName("tablinks")[0].click();
</script>
      
@endsection
