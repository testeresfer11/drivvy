@extends('company.layouts.app')
@section('title', 'Question Edit')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Edit Question</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.questionnaire.list')}}">Questionnaire</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.question.list',['id' => $question->questionnaire_id])}}">Question</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <x-alert />
                <div class="flash-message"></div>
                @switch($question->type)
                    @case('Text')
                        <div class="card border-0 number_question">
                            <form class="edit-question-form" id="edit-question" action="{{route('admin.question.edit',['id' => request()->id])}}" method="POST">
                                @csrf
                                <h2 class="f-18 mb-4 semi-bold">Input</h2>
                                <div class="row top_question_block">
                                    <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question</label>
                                            <textarea  class="form-control" name="question">{{$question->question}}</textarea>
                                            @error('question')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Required</label>
                                            <div class="toggle-user dark-toggle">
                                                <input type="checkbox" name="is_required" id="switch" @if($question->is_required == 1) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                  <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question Category</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="optional" name="Questioncategory" value="Text" @if($question->type == 'Text') checked @endif>
                                                    <label for="text">Text</label>
                                                    @error('Questioncategory')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="f-14">Category</label>
                                    <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%"  tabindex="-1" aria-hidden="true">
                                        <option value="">--Select Category--</option>
                                        @foreach ($categories as $item)
                                            <option value="{{$item->id}}" {{$item->id == $question->category_id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror  
                                </div>
                            </div>
                                <button type="submit" class="btn btn-primary mr-2">Edit</button>
                            </form>
                        </div>
                    @break
                    @case('Optional')    
                        <div class="card border-0 mcq_question">
                            <form class="edit-question-form" id="edit-question" action="{{route('admin.question.edit',['id' => request()->id])}}" method="POST">
                                @csrf
                                <h2 class="f-18 mb-4 semi-bold">Multiple Choice-Single Answer</h2>
                                <div class="col-12 col-md-12 mb-3 top_question_block">
                                    <div class="form-group">
                                        <label class="f-14">Question</label>
                                        <textarea class="form-control " name="question" required>{{ $question->question }}</textarea>
                                        @error('question')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                     <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question Category</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="optional" name="Questioncategory" value="Optional" required @if($question->type == 'Optional') checked @endif>
                                                    <label for="optional">Option</label>
                                                    @error('Questioncategory')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Option Type</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="single_check" name="choice_ques_type" value="single_check" required @if($question->choice_ques_type == 'single_check') checked @endif>
                                                    <label for="optional">Single check</label>
                                                    <input type="radio" id="multi_check" name="choice_ques_type" value="multi_check" required @if($question->choice_ques_type == 'multi_check') checked @endif>
                                                    <label for="optional">Multi check</label>
                                                </div>
                                                @error('choice_ques_type')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Required</label>
                                            <div class="toggle-user dark-toggle">
                                                <input type="checkbox" name="is_required" id="switch" @if($question->is_required == 1) checked @endif>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="f-14">Category</label>
                                        <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%"  tabindex="-1" aria-hidden="true">
                                            <option value="">--Select Category--</option>
                                            @foreach ($categories as $item)
                                                <option value="{{$item->id}}" {{$item->id == $question->category_id ? 'selected' : ''}}>{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                    </div>
                                    </div>
                                    
                                   
                                    <div class="row option_row">
                                        <div style="width:100%" class="mb-3">
                                            <div class="form-group">
                                                <div class="options row">
                                                    @foreach($question->getAnswer as $Ops)
                                                        <div class="col-12 col-md-6 mb-3">
                                                            <div class="form-group">
                                                                <label class="f-14">Option</label>
                                                                <input type="text" name="opt[]" class="form-control" value="{{ $Ops['answer'] }}">
                                                            </div>
                                                            <input type="hidden" name="next_question_id[]" class="next-question-id" value="{{ $Ops['next_question_id'] }}">
                                                            @if($Ops['is_text_answer'] == 1)
                                                                <div class="input-group-append">
                                                                    <button type="button" class="removeOpts" onclick="removeOpts(this)">Remove</button>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 mb-3 ">
                                        <button class="addOption" type="button" onclick="addOption()">+ Add More Options</button>
                                    </div>
                                    
                                <button type="submit" class="btn btn-primary mr-2">Edit Question</button>                            
                            </form>
                        </div>
                    @break
                    @default
                    <div class="card border-0 number_question">
                        <form class="edit-question-form" id="edit-question" action="{{route('admin.question.edit',['id' => request()->id])}}" method="POST">
                            @csrf
                            <div class="top-titlebar d-flex justify-content-between align-items-center pb-3">
                                @if(isset($question->type))
                                    <h2 class="f-18 mb-4 semi-bold">Number</h2>
                                @endif
                            </div>
                            
                            <div class="row top_question_block">
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Question</label>
                                        <textarea name="question" id="question" class="form-control" rows="3">{{ $question->question }}</textarea>
                                        @error('question')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                    </div>
                                </div>
                            </div>
                            <div class="row top_question_block">
                                <div class="col-12 col-md-12 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Type</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="number" id="typeNumber" name="Questioncategory" @if($question->type == 'number') checked @endif>
                                            <label class="form-check-label" for="typeNumber">Number</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="currency" id="typeCurrency" name="Questioncategory" @if($question->type == 'currency') checked @endif>
                                            <label class="form-check-label" for="typeCurrency">Currency</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" value="range" id="typeRange" name="Questioncategory" @if($question->type == 'range') checked @endif>
                                            <label class="form-check-label" for="typeRange">Range</label>
                                        </div>
                                        @error('Questioncategory')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                    </div>
                                </div>
                            </div>
                            
                            
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Minimum Value</label>
                                        <input type="number" value="{{$question->min_value}}" name="min" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Maximum Value</label>
                                        <input type="number" name="max" value="{{$question->max_value}}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Allow Decimal Numbers</label>
                                        <div class="toggle-user dark-toggle">
                                            <input type="checkbox" name="allow_decimal_num" id="switch" @if($question->allow_decimal_num == 1) checked @endif>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <div class="form-group">
                                        <label class="f-14">Required</label>
                                        <div class="toggle-user dark-toggle">
                                            <input type="checkbox" name="is_required" id="switch" @if($question->is_required == 1) checked @endif>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 mb-3">
                                    <label class="f-14">Category</label>
                                    <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%"  tabindex="-1" aria-hidden="true">
                                        <option value="">--Select Category--</option>
                                        @foreach ($categories as $item)
                                            <option value="{{$item->id}}" {{$item->id == $question->category_id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror  
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mr-2">Edit</button> 
                        </form>
                    </div>
                @endswitch
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
    // edit question 
    $("#edit-question").submit(function(e){
        e.preventDefault();
    }).validate({
        rules: {
            question: {
                required: true
            },
            Questioncategory: {
                required: true
            },
            choice_ques_type: {
                required: true
            },
            category_id: {
                required: true
            }
        },
        messages: {
            question: {
                required: "Please enter the question"
            },
            Questioncategory: {
                required: "Please select a question category"
            },
            choice_ques_type: {
                required: "Please select an option type"
            },
            category_id: {
                required: "Please select a category"
            }
        },
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            if (element.prop('type') === 'radio' || element.prop('type') === 'checkbox') {
                error.appendTo(element.closest('.form-group'));
            } else {
                error.insertAfter(element);
            }
        },
        submitHandler: function(form) {
            var cate = $("input[name='Questioncategory']:checked").val();
            
            var flag = 0;
            if (cate == "Optional") {
                $("input[name='opt[]']").each(function(index, value){
                    var value = $(value).val();
                    if(value != ''){
                        flag++;
                    }
                });
                if (flag <= 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: "Please fill up the options",
                        showConfirmButton: true,
                    });
                    return false; 
                }
            }

            if(cate == "Text"){
                $("input[name='opt[]']").val('');
            }
            form.submit();
        }
    });

</script>

@stop
