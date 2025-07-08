@extends('company.layouts.app')
@section('title', 'Dashboard')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title bold"> Dashboard </h3>
</div>
@endsection
@section('content')
<div class="admin-dashboard">
    <div class="row">
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/earn.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Total Earning</h6>
                                <h3 class="mb-0 f-26 bold">$21333</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/customer.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Total Customers</h6>
                                <h3 class="mb-0 f-26 bold">333</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/drivers.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Total Drivers</h6>
                                <h3 class="mb-0 f-26 bold">133</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/request.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Upcoming Requests</h6>
                                <h3 class="mb-0 f-26 bold">33</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/approve.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Approve Request</h6>
                                <h3 class="mb-0 f-26 bold">33</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/drivers.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Active Drivers</h6>
                                <h3 class="mb-0 f-26 bold">333</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/order.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Total Orders Delivered</h6>
                                <h3 class="mb-0 f-26 bold">133</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-3 col-sm-6  col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center gap-3">
                        <div class="col-5">
                            <div class="earn-dashboard">
                                <img src="{{asset('admin/images/active.png')}}">
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="total-earning">
                                <h6 class="text-muted font-weight-normal f-14">Active Orders</h6>
                                <h3 class="mb-0 f-26 bold">133</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-7 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <div class="order-chart">
                    <div class="d-flex justify-content-between  total-orders">
                        <h4 class="card-title">Total No. of Orders</h4>
                        <select class="form-control select-field w-max form-select">
                            <option>Weekly</option>
                            <option>Monthly</option>
                            <option>Yearly</option>
                        </select>
                    </div>
                    <canvas id="pieChart" style="height:250px"></canvas>
                    
                </div>
            </div>
            </div>
        </div>
        <div class="col-lg-5 grid-margin stretch-card">
            <div class="card">
            <div class="card-body">
                <h4 class="card-title">Line chart</h4>
                <canvas id="lineChart" style="height:250px"></canvas>
            </div>
            </div>
        </div>
    </div>
    <div class="users-table">
        <div class="card">
            <div class="card-body">
            <div class="flex-header d-flex justify-content-between align-items-center">
                <h3 class="f-22 bold">Total No. of Orders</h3>
                <input type="date" class="form-control w-max" name="date-input">
            </div>
            <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> <p>S. No. </p> </th>
                  <th> <p>Date</p>  </th>
                  <th> <p>Time</p>  </th>
                  <th> <p>Sender</p>  </th>
                  <th>  <p>No. of Container </p></th>
                  <th>  <p>Loading Location</p></th>
                  <th> <p> Unloading Location</p></th>
                  <th>  <p>Status</p> </th>
                  <th>  <p>Action</p> </th>
                </tr>
              </thead>
              <tbody>
            
                  <tr>
                    <td class="py-1">
                      1
                    </td>
                    <td>10 Feb 2024 </td>
                    <td>9:00 PM</td>
                    <td> 2 </td>
                    <td> Delhi </td>
                    <td> Delhi </td>
                    <td> Camileo </td>
                    <td>
                        <div class="status-act">Active</div>
                    </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="#" title="View" class="table-icon f-22"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Edit" class="table-icon f-22"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="table-icon f-22 deleteUser"><i class="mdi mdi-delete"></i></a>
                      </span> 
                    </td>
                  </tr>
             
              </tbody>
            </table>
          </div>
            
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="{{asset('admin/js/dashboard.js')}}"></script>
<script src="{{asset('admin/js/chart.js')}}"></script>
@endsection