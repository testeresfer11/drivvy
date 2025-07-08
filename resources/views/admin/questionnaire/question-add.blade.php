@extends('admin.layouts.app')
@section('title', 'Question Add')
@section('breadcrum')
<div class="page-header">
    <h3 class="page-title">Add Question</h3>
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('admin.questionnaire.list')}}">Questionnaire</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="{{route('admin.question.list',['id' => request()->id])}}">Question</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add</li>
    </ol>
    </nav>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                {{-- <h4 class="card-title">Add Question</h4> --}}
                <x-alert />
               
                @switch(request()->que_type)
                    @case('mcq')
                        <div class="card border-0 mcq_question">
                            <form class="add-question-form" id="add-question" action="{{route('admin.question.add')}}" method="POST">
                                @csrf
                                <h2 class="f-18 mb-4 semi-bold">Multiple Choice-Single Answer</h2>
                                <input type="hidden" name="questionnaire_id" value="{{request()->id}}">
                                <div class="col-12 col-md-12 mb-3 top_question_block">
                                    <div class="form-group">
                                        <label class="f-14">Question</label>
                                        <textarea class="form-control @error('question') is-invalid @enderror" name="question" required></textarea>
                                        @error('question')
                                            <span class="invalid-feedback"
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            <div class="row">
                                <div class="row w-100">
                                    
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question Type</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="optional" class="@error('Questioncategory') is-invalid @enderror" name="Questioncategory" value="Optional" required>
                                                    <label for="optional">Option</label>
                                                @error('Questioncategory')
                                                    <span class="invalid-feedback"
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Option Type</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="single_check" name="choice_ques_type" value="single_check" required>
                                                    <label for="single_check">Single check</label>
                                                    <input type="radio" id="multi_check" name="choice_ques_type" value="multi_check" required>
                                                    <label for="multi_check">Multi check</label>
                                                </div>
                                                @error('choice_ques_type')
                                                    <span class="invalid-feedback"
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
                                        <div class="form-group">
                                            <label class="f-14">Required</label>
                                            <div class="toggle-user dark-toggle">
                                                <input type="checkbox" name="is_required" id="switch" checked="">
                                            </div>
                                            @error('is_required')
                                                <span class="invalid-feedback"
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="f-14">Category</label>
                                        <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                            <option value="">--Select Category--</option>
                                            @foreach ($categories as $item)
                                                <option value="{{$item->id}}" data-select2-id="3">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback"
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                </div>
                                </div>
                            
           

                                <div class="row" id="options" style="display:none;">
                                    <div class="options">
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="form-group" id="cloneable">
                                                <label class="f-14">Option</label>
                                                <input type="text" name="opt[]" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="form-group">
                                                <label class="f-14">Option</label>
                                                <input type="text" name="opt[]" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3 ">
                                        <button class="addOption" type="button" onclick="addOption()">+ Add More Options</button>
                                    </div>
                                </div>
                            
                                <button type="submit" class="btn btn-primary mr-2">Add Question</button>
                            </form>
                            
                        </div>
                    @break
                    @case('number')
                        <div class="card border-0 number_question">
                            <form class="add-question-form" id="add-question" action="{{route('admin.question.add')}}" method="POST">
                                @csrf
                                <h2 class="f-18 mb-4 semi-bold">Number</h2>
                                <input type="hidden" name="questionnaire_id" value="{{request()->id}}">
                                <div class="row top_question_block">
                                    <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question</label>
                                            <textarea class="form-control @error('question') is-invalid @enderror" name="question" required></textarea>
                                            @error('question')
                                                <span class="invalid-feedback"
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row top_question_block">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Type</label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="number" id="typeNumber" name="Questioncategory">
                                                <label class="form-check-label" for="typeNumber">Number</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="currency" id="typeCurrency" name="Questioncategory">
                                                <label class="form-check-label" for="typeCurrency">Currency</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" value="range" id="typeRange" name="Questioncategory">
                                                <label class="form-check-label" for="typeRange">Range</label>
                                            </div>
                                            @error('Questioncategory')
                                                <span class="invalid-feedback"
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
                                            <input type="number" name="min" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Maximum Value</label>
                                            <input type="number" name="max" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Allow Decimal Numbers</label>
                                            <div class="toggle-user dark-toggle">
                                                <input type="checkbox" name="allow_decimal_num" id="switch" checked="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Required</label>
                                            <div class="toggle-user dark-toggle">
                                                <input type="checkbox" name="is_required" id="switch" checked="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="f-14">Category</label>
                                        <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                            <option value="">--Select Category--</option>
                                            @foreach ($categories as $item)
                                                <option value="{{$item->id}}" data-select2-id="3">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback"
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                            </form>
                        </div>
                    @break
                    @case('input')
                        <div class="card border-0 number_question">
                            <form class="add-question-form" id="add-question" action="{{route('admin.question.add')}}" method="POST">
                                @csrf
                                <h2 class="f-18 mb-4 semi-bold">Input</h2>
                                <input type="hidden" name="questionnaire_id" value="{{request()->id}}">
                                <div class="row top_question_block">
                                    <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question</label>
                                            <textarea  class="form-control" name="question" required></textarea>
                                            @error('question')
                                                <span class="invalid-feedback"
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
                                                <input type="checkbox" name="is_required" id="switch" checked="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 mb-3">
                                        <div class="form-group">
                                            <label class="f-14">Question Type</label>
                                            <div class="category">
                                                <div class="vehiclecategory">
                                                    <input type="radio" id="optional" name="Questioncategory" value="Text" required>
                                                    <label for="text">Text</label>
                                                    </div>
                                                    @error('Questioncategory')
                                                        <span class="invalid-feedback"
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-3">
                                        <label class="f-14">Category</label>
                                        <select class="js-example-basic-single select2-hidden-accessible @error('category_id') is-invalid @enderror" name="category_id" id="exampleInputCategory" style="width:100%" data-select2-id="1" tabindex="-1" aria-hidden="true">
                                            <option value="">--Select Category--</option>
                                            @foreach ($categories as $item)
                                                <option value="{{$item->id}}" data-select2-id="3">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="invalid-feedback"
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror  
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-2">Add</button>
                            </form>
                        </div>
                    @break
                    @default
                        
                @endswitch
            </div>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
// Add question via mcq
$("#add-question").submit(function(e){
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
