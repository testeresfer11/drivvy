@extends('admin.layouts.app')
@section('title', 'Edit Card')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Cards</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.card.list')}}">Cards</a></li>
        <li class="breadcrumb-item active" aria-current="page">View</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')
<div>
    <div class="row justify-content-center">
      <div class="col-5 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">View Card</h4>
            <x-alert />
           
            <div class="personal-card">
              <div class="row scratch-rw">
                  <div class="scratch-card text-center first">
                    {{-- &&  file_exists(public_path('storage/images/' . $card->path)) --}}
                    @if($card->path && $card->type == 'image' )
                      <img src="{{asset('storage/images/' . $card->path)}}">
                    @elseif($card->path && $card->type == 'video' )
                      <iframe src="{{asset('storage/images/' . $card->path)}}" frameborder="0"></iframe>
                    @endif
                      <h5>{{$card->name ?? ''}}</h5>
                      <p>{{$card->description ?? ''}}</p>
                      <h3>${{$card->amount ?? '0'}}.00</h3>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
@endsection