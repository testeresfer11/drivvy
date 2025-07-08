@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title"> Dashboard </h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">dashboard</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row dash-card">
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgyellow">
                <a href="/admin/user/list" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="dash-card-lft">
                                    <h2 class="mb-0 text-black">{{$responseData['user'] ?? 0}}</h2>
                                    <h6 class="text-muted font-weight-normal">Total Users</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/user.png')}}"
                                    alt="Banner Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgpink">
                <a href="/admin/ride/list" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="dash-card-lft">
                                    <h2 class="mb-0 text-black">{{$responseData['rides'] ?? 0}}</h2>
                                    <h6 class="text-muted font-weight-normal">Total Rides</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/car-sharing.png')}}"
                                    alt="Banner Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgblue">
                <a href="{{ route('admin.ride.list', ['status' => 1]) }}" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="dash-card-lft">
                                    <h2 class="mb-0 text-black">{{ $responseData['active_bookings'] ?? 0 }}</h2>
                                    <h6 class="text-muted font-weight-normal">Active Rides</h6>
                                </div>
                            </div>
                            <div class="col-4 text-right">
                                <div class="dashboard-icons">
                                    <img src="{{ asset('admin/images/share-ride.png') }}" alt="Active Rides Icon">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgpurple">
                <a href="/admin/payments/list" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="dash-card-lft">
                                    <h2 class="mb-0 text-black">{{$responseData['payments'] ?? 0}} </h2>
                                    <h6 class="text-muted font-weight-normal">Total Payments</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/payment-method.png')}}"
                                    alt="Banner Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
       {{-- <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bglight">
                <a href="/admin/review/list" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="dash-card-lft">
                                    <h2 class="mb-0 text-black">{{$responseData['reviews'] ?? 0}} </h2>
                                    <h6 class="text-muted font-weight-normal">Total Reviews</h6>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/review.png')}}"
                                    alt="Banner Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>--}}
        {{--<div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
        <div class="card m-0 bggray">
            <a href="/admin/ride/list" class="text-black text-decoration-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                        <div class="dash-card-lft">
                            <h2 class="mb-0 text-black">{{$responseData['active_rides'] ?? 0}}</h2>
                            <h6 class="text-muted font-weight-normal">Active Rides</h6>
                        </div>
                        </div>
                        <div class="col-4">
                            <div class="dashboard-icons ">
                                <img src="{{asset('admin/images/ridding.png')}}"
                                    alt="Banner Image">
                            </div>
                        </div>
                    </div>
               
                </div>
            </a>
        </div>
        </div>--}}
        <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bggreen">
                <a href="{{ route('admin.ride.list', ['status' => 2]) }}" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                            <div class="dash-card-lft">
                                <h2 class="mb-0 text-black">{{$responseData['completed_bookings'] ?? 0}}</h2>
                                <h6 class="text-muted font-weight-normal">Completed Bookings</h6>
                            </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/book.png')}}"   alt="Banner Image">
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
       {{-- <div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgreview">
                <a href="/admin/review/list" class="text-black text-decoration-none">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                            <div class="dash-card-lft">
                                <h2 class="mb-0 text-black">{{$responseData['Recent_Reviews'] ?? 0}}</h2>
                                <h6 class="text-muted font-weight-normal">Recent Reviews</h6>
                            </div>
                            </div>
                            <div class="col-4">
                                <div class="dashboard-icons ">
                                    <img src="{{asset('admin/images/online.png')}}"   alt="Banner Image">
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </a>
            </div>
        </div>--}}
        {{--<div class="col-12 col-lg-4 col-xl-3 col-sm-6 grid-margin stretch-card">
            <div class="card m-0 bgmsg">
                <a href="/admin/messages/list" class="text-black text-decoration-none">
                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                        <div class="dash-card-lft">
                            <h2 class="mb-0 text-black">{{$responseData['Messages'] ?? 0}}</h2>
                            <h6 class="text-muted font-weight-normal">Messages</h6>
                        </div>
                        </div>
                        <div class="col-4">
                            <div class="dashboard-icons ">
                                <img src="{{asset('admin/images/revenue.png')}}"   alt="Banner Image">
                            </div>
                        </div>
                    </div>
                  
                </div>
            </a>
            </div>
        </div>
        
    </div>---}}
   <div class="row">
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Rides</h4>
                <canvas id="pieChart" style="height:250px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Monthly Revenue</h4>
                <canvas id="lineCharts" style="height:250px"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
@section('scripts')
<script src="{{asset('admin/js/dashboard.js')}}"></script>
<script src="{{asset('admin/js/chart.js')}}"></script>

<script>

    const rideChartData = @json($responseData['rideChartData']);
    const monthlyRevenueData = @json($responseData['monthlyRevenueData']);

    // Render Pie Chart for Rides
function renderPieChart(data) {
    const ctx = document.getElementById('pieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.map(item => item.label),
            datasets: [{
                label: 'Rides',
                data: data.map(item => item.value),
                backgroundColor: [
                    'rgba(162, 194, 58, 0.2)',  // Active Rides color (semi-transparent)
                    'rgba(54, 162, 235, 0.2)',  // Completed Rides color (semi-transparent)
                    'rgba(255, 159, 64, 0.2)',  // Cancelled Rides color (semi-transparent red)
                    'rgba(255, 99, 132, 0.2)'   // Additional color (semi-transparent)
                ],
                borderColor: [
                    'rgba(162, 194, 58, 1)',    // Active Rides border color (opaque)
                    'rgba(54, 162, 235, 1)',    // Completed Rides border color (opaque)
                    'rgba(255, 159, 64, 1)',    // Cancelled Rides border color (opaque red)
                    'rgba(255, 99, 132, 1)'     // Additional border color (opaque)
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
}


    // Render Line Chart for Monthly Revenue
    function renderLineChart(data) {
        console.log(data);
        const ctx = document.getElementById('lineCharts').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: data.datasets,
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                    },
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    }

    // Use the data from the response to render the charts
   
    renderPieChart(rideChartData);
    renderLineChart(monthlyRevenueData);
</script>
@endsection
