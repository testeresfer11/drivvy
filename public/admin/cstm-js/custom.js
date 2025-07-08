jQuery.validator.addMethod("noSpace", function(value, element) { 
    return value.trim() !== "" && value.trim()[0] !== " "; 
}, "Spaces are not allowed");

$(document).ready(function() {
    $(document).ajaxStart(function() {
        $('.preloader').show(); 
    });

    $(document).ajaxStop(function() {
        $('.preloader').hide();
    });
    
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 2000);

    var scroll=$('.chat-box');
    scroll.animate({scrollTop: scroll.prop("scrollHeight")});


     $('#description').summernote({
            height: 200, // Set the height of the editor
            minHeight: null, // Set minimum height of the editor
            maxHeight: null, // Set maximum height of the editor
            focus: true // Set focus to editable area after initializing Summernote
        });
    
});

/*** Questions management  */
$("input[name = 'Questioncategory']").on("click",function(){
    var cate = $(this).val();
    if(cate == "Optional"){
        $('#options').slideDown(500);
    }else{
        $('#options').slideUp(500);
    }
});

$(document).on('click', '.remove-option', function() {
    $(this).closest('.col-12').remove();
});

function addOption() {
    var lngth = $('input[name="opt[]"]').length + 1;
    var newOptionHTML = `
        <div class="col-12 col-md-6 mb-3">
            <div class="form-group">
                <label class="f-14">Option</label>
                <input type="text" name="opt[]" class="form-control">
                <div class="droppable" data-next-question-id=""></div> <!-- Add data-next-question-id attribute -->
                <div class="input-group-append">
                    <button type="button" class="remove-option">Remove</button>
                </div>
            </div>
        </div>
    `;
    $('.options').append(newOptionHTML);
    $('.options').children().last().find('.droppable').droppable({
        accept: ".draggable",
        drop: function(event, ui) {
            var questionText = ui.helper.find('.card-title').text();
            $(this).text(questionText);
            
    
            var nextQuestionId = ui.helper.data('next-question-id');
            
            var nextQuestionIds = $('#next_question_id').val().split(',');
            
            
            nextQuestionIds.push(nextQuestionId);
            
            $('#next_question_id').val(nextQuestionIds.join(','));
        }
    });
}

// Add the custom filesize method to jQuery Validator
$.validator.addMethod("filesize", function (value, element, param) {
    // Check if the element is a file input and if a file is selected
    if (element.files && element.files.length > 0) {
        // Get the file size (in bytes)
        var fileSize = element.files[0].size;
        // Convert the parameter to bytes (from KB)
        var maxSize = param * 1024;
        // Check if the file size is less than or equal to the maximum size
        return this.optional(element) || fileSize <= maxSize;
    }
    // No file selected, validation passes
    return this.optional(element);
}, "File size must be less than {0} KB.");

document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        // Toggle the type attribute
        const type = passwordInput.type === 'password' ? 'text' : 'password';
        passwordInput.type = type;

        // Toggle the eye icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
});
