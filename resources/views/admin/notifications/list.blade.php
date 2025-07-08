@extends('admin.layouts.app')
@section('title', 'Notifications')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Notifications</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Notifications</a></li>
        <li class="breadcrumb-item active" aria-current="page">Notifications</li>
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
            <h4 class="card-title">Notifications</h4>
          </div>
          <div class="notification-all-list">
	    			<div class="notification-table card">
	    				<table class="table-notify"> 
	    					<thead>
	    						<th>
	    							<div class="form-check">
									  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
									  <label class="form-check-label" for="flexCheckDefault">
									    
									  </label>
									</div>
								</th>
								<th></th>
								<th></th>
								<!-- <th>
									<div class="buttons-notify">
										<button type="button" class="trash-btn"><i class="mdi mdi-reload"></i></button>
										<button type="button" class="trash-btn"><i class="mdi mdi-trash-can"></i></button>
										<button type="button" class="trash-btn"><i class="mdi mdi-exclamation"></i></button>
									</div>
								</th> -->
	    					</thead>
	    					<tbody>
								@foreach($notifications as $notify)
	    						<tr>
	    							<!-- <td>	
	    								<div class="form-check">
										  <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
										  <label class="form-check-label" for="flexCheckDefault">					    
										  </label>
										</div>
									</td> -->
									<td><i class="mdi mdi-star"></i></td>
									<td>
										<div class="notify-checks">
										
											<div class="notify-trash">
												<h6 class="bold f-16 mb-2">{{$notify->type}}</h6>
												<p class="f-13 mb-2">{{$notify->message}}</p>
												<!-- <p class="f-13"><span>Friday 6:30 PM</span>	 <span>31 March 2023</span></p> -->
											</div>
										</div>
									</td>
									<td> 
									<td> 
										<span class="menu-icon">
											<a href="{{route('admin.user.notification-view',['notify' => $notify->notification_id ,'id' => $notify->user_id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
										</span>&nbsp;&nbsp;&nbsp;
									</td>
	    						</tr>
								@endforeach
	    					</tbody>
	    				</table>
	    			</div>
	    		</div>	    
        </div>
      </div>
    </div>
  </div>
@endsection
@section('scripts')


@stop
