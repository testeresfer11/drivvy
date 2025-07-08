@extends('company.layouts.app')
@section('title', 'Transaction')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Transactions</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('company.dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Transactions</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <x-alert />
        <div class="flash-message"></div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <h4 class="card-title">Transactions</h4>
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Transaction Id</th>
                  <th> User Name</th>
                  <th> Transaction Amount</th>
                  <th> Order Id</th>
                  <th> Transaction Date</th>
                  <th> Card Name</th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($transactions as $key => $data)
                  <tr>
                    <td> {{ ++$key }} </td>
                    <td> {{''}} </td>
                    <td> {{''}} </td>
                    <td> {{''}} </td>
                    <td> {{''}} </td>
                    <td> {{''}} </td>
                    <td> {{''}} </td>       
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('company.transaction.view',['id' => $data->id])}}" title="View" class="text-primary"><i class="mdi mdi-eye"></i></a>
                      </span>&nbsp;&nbsp;&nbsp;
                    </td>
                  </tr>
                @empty
                    <tr>
                      <td colspan="8" class="no-record"> <center>No record found </center></td>
                    </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="custom_pagination">
            {{ $transactions->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

