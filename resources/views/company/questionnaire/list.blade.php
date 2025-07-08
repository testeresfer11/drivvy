@extends('company.layouts.app')
@section('title', 'Questionnaire')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Questionnaire</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Questionnaire</a></li>
        <li class="breadcrumb-item active" aria-current="page">Questionnaire</li>
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
            <h4 class="card-title">Questionnaire Management</h4>
            <button type="button" class="btn default-btn btn-md" data-toggle="modal" data-target="#addQuestionnaireModal">
              <span class="menu-icon">+ Add Questionnaire</span></button>
      
          </div>
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th> Sr. No. </th>
                  <th> Question </th>
                  <th> Created At </th>
                  <th> Action </th>
                </tr>
              </thead>
              <tbody>
                @forelse ($questions as $key => $data)
                  <tr>
                    <td>{{ $data->unique_questionnaire_id }}</td>
                    <td>{{ $data->name }} </td>
                    <td>{{ convertDate($data->created_at,'d M,Y') }} </td>
                    <td> 
                      <span class="menu-icon">
                        <a href="{{route('admin.question.list',['id' => $data->id])}}" title="Question" class="text-primary" ><i class="mdi mdi-menu"></i></a>&nbsp;&nbsp;

                        <a href="#" title="Edit" class="text-success editQuestionnaire" data-id="{{$data->id}}" data-toggle="modal" data-target="#editQuestionnaireModal"><i class="mdi mdi-pencil"></i></a>
                      </span>&nbsp;&nbsp;
                      <span class="menu-icon">
                        <a href="#" title="Delete" class="text-danger deleteQuestionnaire" data-id="{{$data->id}}"><i class="mdi mdi-delete"></i></a>
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
<div class="modal fade" id="addQuestionnaireModal" tabindex="-1" aria-labelledby="addQuestionnaireModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body pt-2">
        <form class="forms-sample pt-3" id="add-questionnaire" action="{{route('admin.questionnaire.add')}}" method="POST">
          @csrf
          <h3>Create Questionnaire</h3>
          <div class="form-group pt-3">
            <div class="row">
              <div class="col-12 ps-col position-relative">
                <input type="text" class="form-control nmerr @error('name') is-invalid @enderror" placeholder="Name" name="name">
                <span class="invalid-feedback errnm" role="alert">
                  @error('name')
                    <strong>{{ $message }}</strong>
                  @enderror
                </span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mr-2 add">Add</button>
        </form>
      </div>
    </div>
  </div>
</div>

{{-- Edit  Questionnaire--}}
<div class="modal fade" id="editQuestionnaireModal" tabindex="-1" aria-labelledby="editQuestionnaireModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body pt-2">
        <form class="forms-sample pt-3" id="edit-questionnaire" action="#" method="POST">
          @csrf
          <input type="hidden" name="id" id="edit-questionnaire-id">
          <h3>Edit Questionnaire</h3>
          <div class="form-group pt-3">
            <div class="row">
              <div class="col-12 ps-col position-relative">
                <input type="text" class="form-control nmerr @error('name') is-invalid @enderror" placeholder="Name" name="name" id="edit-questionnaire-name">
                <span class="invalid-feedback errnm" role="alert">
                  @error('name')
                    <strong>{{ $message }}</strong>
                  @enderror
                </span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary mr-2">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

@endsection
@section('scripts')
<script>
$(document).ready(function () {
    // Add the questionnaire
    $("#add-questionnaire").validate({
        rules: {
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
        },
        messages: {
            name: {
                required: "Questionnaire name is required.",
                minlength: "Questionnaire name must consist of at least 3 characters."
            },
        },

        submitHandler: function (form) {
            let formdata = new FormData(form);
            $.ajax({
                url: form.action,
                method: "POST",
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status === "success") {
                        $('#addQuestionnaireModal').modal('hide');
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            let errorElement = $('[name="' + key + '"]').siblings('.invalid-feedback');
                            errorElement.html('<strong>' + value[0] + '</strong>');
                            $('[name="' + key + '"]').addClass('is-invalid');
                        });

                    }
                }
            });
        }
    });

    // get the questionnaire
    $('.editQuestionnaire').on('click', function(){
      var id = $(this).data('id');
        $.ajax({
          url: "edit/"+id,
          type: "GET",
          success: function(response) {
            if (response.status == "success") {
              $('#edit-questionnaire-name').val(response.data.name);
              $('#edit-questionnaire-id').val(response.data.id);
            } 
          }
        });
    });

    // edit the questionnaire
    $("#edit-questionnaire").validate({
        rules: {
            name: {
                required: true,
                noSpace: true,
                minlength: 3,
            },
        },
        messages: {
            name: {
                required: "Questionnaire name is required.",
                minlength: "Questionnaire name must consist of at least 3 characters."
            },
        },

        submitHandler: function (form) {
            let formdata = new FormData(form);
            const id = $('#edit-questionnaire-id').val();
            $.ajax({
                url: 'edit/'+id,
                method: "POST",
                data: formdata,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status === "success") {
                        $('#editQuestionnaireModal').modal('hide');
                        $('.flash-message').html('<div class="alert alert-success" role="alert">'+response.message+'</div>');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        $.each(errors, function (key, value) {
                            let errorElement = $('[name="' + key + '"]').siblings('.invalid-feedback');
                            errorElement.html('<strong>' + value[0] + '</strong>');
                            $('[name="' + key + '"]').addClass('is-invalid');
                        });

                    }
                }
            });
        }
    });

    // edit the questionnaire
    $('.deleteQuestionnaire').on('click', function() {
      var id = $('.deleteQuestionnaire').attr('data-id');
      Swal.fire({
          title: "Are you sure?",
          text: "You want to delete the Questionnaire?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#2ea57c",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then((result) => {
          if (result.isConfirmed) {
              $.ajax({
                  url: "delete/" + id,
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
