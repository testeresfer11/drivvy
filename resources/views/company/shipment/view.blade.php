@extends('company.layouts.app')
@section('title', 'View Shipment')
@section('breadcrum')
    <div class="page-header">
        <h3 class="page-title">Shipment Details</h3>
    </div>
@endsection
@section('content')
    <div class="view-shipment">
        <form class="forms-sample">
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="card mb-4">
                            <div class="card-body shipment-view-card">
                                <h3 class="f-22 bold text-black text-center mb-1">John Due</h3>
                                <p class="f-16 dark text-center">ID2334</p>
                                <div class="shipment-viewer relative d-flex justify-content-between">
                                    <div class="shpmnt-left">
                                        <div class=""><img src="{{asset('admin/images/circle.png')}}"></div>
                                        <h6 class="semi-bold f-14 mb-0">Sep 09, 2021 Tue</h6>
                                        <p class="f-13 text-muted">Chicago Avenue</p>
                                    </div>
                                    <div class="shpmnt-left">
                                        <div class="text-end"><img src="{{asset('admin/images/circle1.png')}}"></div>
                                        <h6 class="semi-bold f-14 mb-0">Sep 09, 2021 Tue</h6>
                                        <p class="f-13 text-muted">Chicago Avenue</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body shipment-view-card">
                                <p class="f-14 d-flex justify-content-between">
                                    <span class="semi-bold">Total No. of Container</span>
                                    <span class="text-muted">24</span>
                                </p>
                                <p class="f-14 d-flex justify-content-between">
                                    <span class="semi-bold">Truck Type</span>
                                    <span class="text-muted">2P, 10P</span>
                                </p>
                                <p class="f-14 d-flex justify-content-between">
                                    <span class="semi-bold"> Type of Goods</span>
                                    <span class="text-muted">Containerized Goods</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-7">
                        <div class="card mb-4">
                            <div class="card-body shipment-view-card">
                                <h3 class="semi-bold f-22 text-black">User Info </h3>
                                <p class="f-16">Shipment Name</p>
                                <div class="ship-flex-data">
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Order Placed ID :</span> 
                                        <span class="text-muted rgt" id="">ID345344</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Date :</span> 
                                        <span class="text-muted rgt" id="">12 Aug 2022</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Delivery Type :</span> 
                                        <span class="text-muted rgt" id="">Same Day</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Status :</span> 
                                        <span class="text-muted rgt" id="">Pending</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Delivery Note :</span> 
                                        <span class="text-muted rgt" id="">Pending</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Recipient  Name :</span> 
                                        <span class="text-muted rgt" id="">Shanaya</span>
                                    </h6>
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury">Recipient  Phone Number :</span> 
                                        <span class="text-muted rgt" id="">9836736678</span>
                                    </h6>
                                </div>
                            </div>
                            <div class="card-footer bg-white">
                                <div class="ship-flex-data">
                                    <h6 class="f-14 mb-1">
                                        <span class="semi-bold qury text-black">Total Price :</span> 
                                        <span class="bold text-black f-22 rgt" id="">$400.00</span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>    
    </div>
       
@endsection
