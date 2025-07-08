@extends('company.layouts.app')
@section('title', 'Question')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Question</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.questionnaire.list')}}">Questionnaire</a></li>
        <li class="breadcrumb-item active" aria-current="page">Question</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <h4 class="response-title d-flex justify-content-between align-items-center">
      <div>
        <h6 class="f-14 mb-1"><span class="semi-bold qury">Questionnaire :</span> <span class="text-muted"> {{$questionnaire->name}} </span></h6>
      </div>
    </h4>
    <div class="col-lg-12 grid-margin stretch-card">
      <div class="card">
        <x-alert />
        <div class="flash-message"></div>
        <div class="card-body">
          <div class="d-flex justify-content-between"> 
            <h4 class="card-title">Question Management</h4>
            <button type="button" class="btn default-btn btn-md" data-toggle="modal" data-target="#addQuestionModal">
              <span class="menu-icon">+ Add Question</span></button>
      
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Question </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($questions as $key => $data)
                  <tr>
                    <td>{{ $data->unique_ques_id }}</td>
                    <td>{{ $data->question }} </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.question.edit',['id' => $data->id])}}" title="Edit" class="text-success "><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteQuestion" data-id="{{$data->id}}"><i class="mdi mdi-delete"></i></a>
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
          </div>
          <div class="custom_pagination">
            {{ $questions->links('pagination::bootstrap-4') }}
          </div>
        </div>
      </div>
    </div>
</div>


{{-- Add  Questionnaire--}}
<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body pt-2">
        <div class="view-survey pt-3">
          <h5 class="modal-title bold mb-4">Question Types</h5>
           <div class="select-survey-ques">
             <div class="accordion accordion-flush" id="accordionFlushExample">
         <div class="accordion-item">
             <h2 class="accordion-header" id="flush-headingOne">
               <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                 Pick & Choose
               </button>
             </h2>
             <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
               <div class="accordion-body">
                 <div class="pick-qustions">
                   <div class="question-pick">
                     <a href="{{ route('admin.question.type', [request()->id,'mcq']) }}"><i class="mdi mdi-chart-gantt"></i> Multiple Choice</a>
                   </div>
                   <div class="question-pick">
                      <a href="{{ route('admin.question.type',[request()->id,'number']) }}"><i class="mdi mdi-decimal-decrease"></i> Number </a>
                   </div>
                   <div class="question-pick">
                     <a href="{{ route('admin.question.type', [request()->id,'input']) }}"><i class="mdi mdi-keyboard"></i> Input</a>
                   </div>						      							      
                 </div>
               </div>
             </div>
           </div>
            </div> 
           </div>     
      </div>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function () {
    // edit the questionnaire
    $('.deleteQuestion').on('click', function() {
      var id = $('.deleteQuestion').attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Question?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "/admin/question/delete/" + id,
                  type: "GET", 
                  success: function(response) {
                    if (response.status == "success") {
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                      } else {
                        $('.flash-message').html('<div class="alert alert-danger" role="alert">'+response.message+'</div>');
                      }
                  }
              });
          }
      });
    });
});
</script>

@stop
