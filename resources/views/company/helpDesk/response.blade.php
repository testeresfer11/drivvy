@extends('company.layouts.app')
@section('title', 'Query Response')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Help Desk</h3>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.helpDesk.list',['type' => 'open'])}}">Help Desk</a></li>
        <li class="breadcrumb-item active" aria-current="page">Response</li>
      </ol>
    </nav>
</div>
@endsection
@section('content')

<div class="help-response">
  <h4 class="response-title d-flex justify-content-between align-items-center">
    <div>
      <h6 class="f-14 mb-1"><span class="semi-bold qury">Title :</span> <span class="text-muted"> {{$response->title}} </span></h6>
      <h6 class="f-14 mb-1"><span class="semi-bold qury ">Description :</span> <span class="text-muted">{{$response->description}}</span></h6>
    </div>
    <div>
      <h6 class="f-14 mb-1"><span class="semi-bold qury help-id"># {{$response->ticket_id}} </span></h6>
    </div>
  </h4>
  <div class="row justify-content-center">
    <div class="col-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">
          <x-alert />
          <div class="flash-message"></div>
          <div class="card-header d-flex justify-content-between p-3">
            <p class="fw-bold mb-0">Admin</p>
           
          </div>
            <ul class="list-unstyled chat-box">
              @forelse ( $response->response as $data)
               @if ($data->user_id == authId())
               
                  <li class="d-flex mb-4 right-msg">
                    <div class="card w-100">
                      
                      <div class="card-body">
                        <p class="mb-0">
                          {{$data->response}}
                        </p>
                        <p class="text-muted small mb-0 msg_time"> {{replyDiffernceCalculate($data->created_at)}} ago</p>
                      </div>
                    </div>
                    <img src="{{userImageById($data->user_id)}}" alt="avatar"
                      class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
                  </li>
                @else
                  <li class="d-flex  mb-4 left-msg">
                    <img src="{{userImageById($data->user_id)}}" alt="avatar"
                      class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                    <div class="card">
                      <div class="card-body">
                        <p class="mb-0">
                          {{$data->response}}
                        </p>
                        <p class="text-muted small mb-0 msg_time"> {{replyDiffernceCalculate($data->created_at)}} ago</p>
                      </div>
                    </div>
                  </li>
                @endif
               

                
              @empty
                  <center><img class="mt-4" src="{{asset('admin/images/faces/no-record.png')}}" height="300px"></center>
              @endforelse
              
              {{-- <li class="d-flex justify-content-between mb-4">
                <div class="card w-100">
                  <div class="card-header d-flex justify-content-between p-3">
                    <p class="fw-bold mb-0">Lara Croft</p>
                    <p class="text-muted small mb-0"><i class="mdi mdi-clock"></i> 13 mins ago</p>
                  </div>
                  <div class="card-body">
                    <p class="mb-0">
                      Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                      laudantium.
                    </p>
                  </div>
                </div>
                <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-5.webp" alt="avatar"
                  class="rounded-circle d-flex align-self-start ms-3 shadow-1-strong" width="60">
              </li>
              <li class="d-flex justify-content-between mb-4">
                <img src="https://mdbcdn.b-cdn.net/img/Photos/Avatars/avatar-6.webp" alt="avatar"
                class="rounded-circle d-flex align-self-start me-3 shadow-1-strong" width="60">
                <div class="card">
                  <div class="card-header d-flex justify-content-between p-3">
                    <p class="fw-bold mb-0">Brad Pitt</p>
                    <p class="text-muted small mb-0"><i class="mdi mdi-clock"></i> 10 mins ago</p>
                  </div>
                  <div class="card-body">
                    <p class="mb-0">
                      Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                      labore et dolore magna aliqua.
                    </p>
                  </div>
                </div>
              </li> --}}
              
            </ul>
        </div>
        <form id="query-response" action="{{route('admin.helpDesk.response',['id' => $response->id])}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="card messages-card mb-1">
                <div data-mdb-input-init class="form-outline">
                  <textarea type="text" class="form-control @error('description') is-invalid @enderror" id="textAreaExample2" rows="4" placeholder="Type..." name="response"></textarea>
                  @error('description')
                      <span class="invalid-feedback" role="alert">
                          <strong>{{ $message }}</strong>
                      </span>
                  @enderror
                </div>
              </div>
              <div class="send-btn text-end pb-4">
                <button class="btn default-btn btn-md mr-4" type="submit">Send</button>
              </div>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection
@section('scripts')
<script>
  $(document).ready(function() {
    $("#query-response").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            response: {
                required: true,
                noSpace: true,
            },
        },
        messages: {
            response: {
                required: "Response is required.",
            },
        },
        submitHandler: function(form) {
          form.submit();
      }

    });
  });
  </script>
@stop