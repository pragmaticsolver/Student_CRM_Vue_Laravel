import ToggleHelper from './components/ToggleHelper';
import FileHelper from './components/FileHelper';
import DropzoneHelper from './components/DropzoneHelper';
import RowHelper from './components/RowHelper';
import ColorPicker from './components/ColorPicker';

$(function () {
    // Navbar search functionality
    $('#nav-search-btn').click(function(){
        $('#main-menus').hide();
        $('#search_section').show();
        $('#nav-search-field').focus();
    });

    $('#nav-search-close').click(function(){
        $('#main-menus').show();
        $('#search_section').hide();
    });

    if(typeof is_search != 'undefined' && is_search == 1)
    {
        $('#nav-search-field').val(search);
        $('#nav-search-btn').click();
    }

    // Add Payment - student view page
    $('#length, #default_class_length').datetimepicker({
        format: 'HH:mm',
        useCurrent: false,
        defaultDate: false
    });

    // For Terminal page
    if(typeof rfid_valid !== 'undefined' && rfid_valid == 1)
    {
        $('#rfid-section').hide();
        $('#rfid-section .required').removeAttr('required');

        $('#barcode-section').show();
        $('#barcode-section .required').attr('required', true);
    }
    else
    {
        $('#rfid-section').show();
        $('#rfid-section .required').attr('required', true);

        $('#barcode-section').hide();
        $('#barcode-section .required').removeAttr('required');
    }

    $('.custom-file input').each(function(){
        update_label_text(this);
    });
    $('.custom-file input').change(function (e){
        update_label_text(this);

    });

    $('#btn_logout').click(function(e){
        e.preventDefault();
        localStorage.removeItem('homepage_selected_teachers');
        localStorage.removeItem('homepage_signed_in_status');
        localStorage.removeItem('calendar-page-defaultView');
        localStorage.removeItem('calendar-page-defaultDate');
        localStorage.removeItem('calendar-page-selected_levels');
        localStorage.removeItem('calendar-page-selected_teachers');
        document.getElementById('logout-form').submit();
    });

    $('#btn_stop_impersonate').click(function(e) {
        e.preventDefault();
        localStorage.removeItem('calendar-page-defaultView');
        localStorage.removeItem('calendar-page-defaultDate');
        localStorage.removeItem('calendar-page-selected_levels');
        localStorage.removeItem('calendar-page-selected_teachers');
        document.getElementById('stop_imporsonate-form').submit();
    });

    $('.char-sensitive-field').on('keyup', function(){
        const field = $(this);
        inputTextLengthChanged(field)
    })
    // For first time load
    $('.char-sensitive-field').each(function(i, element){
        const field = $(element);
        inputTextLengthChanged(field)
    })
});

function inputTextLengthChanged(field) {
    const placeHoler = field.next('.char-length-indicator');

    const currentLength = field.val().length;
    const maxLength = field.data('max-length');

    if(currentLength > maxLength)
    {
        placeHoler.html('<span class="text-danger">' + currentLength  + '</span> / ' + maxLength)
    } 
    else 
    {
        placeHoler.html(currentLength + ' / ' + maxLength)
    }
}

function update_label_text(element){
    var label = "";
    if($(element)[0].files.length > 1)
    {
        label = $(element)[0].files.length + ' files';
    }
    else if($(element)[0].files.length == 1)
    {
        label = $(element)[0].files[0].name;
    }
    else
    {
        label = $(element).data('default_placeholder');
    }
    $(element).next('.custom-file-label').html(label);
}

window.getCalendarStorageData = function(key){
    return localStorage.getItem(calName + '-' + key);
}

window.setCalendarStorageData = function(key,value){
    localStorage.setItem(calName + '-' + key,value);
}

window.constructHiddenDays = function(visibleDays){
    if(!visibleDays)
    {
        return [];
    }
    var _hidden_days = [];
    var visible_days = visibleDays.split(",");
    var days = ['sun','mon','tue','wed','thu','fri','sat'];
    $.each(days,(function(i, day){
        if(!visible_days.includes(day))
        {
            _hidden_days.push(i)
        }
    }));
    return _hidden_days;
}

window.constructFirstDay = function(weekStartDay){
    var days = ['sun','mon','tue','wed','thu','fri','sat'];
    var index = days.indexOf(weekStartDay);
    if(index < 0)
    {
        index = 0;
    }
    return index;
}

window.update_custom_toggle_button_ui = function(element){
    var btn = $(element).parents('.btn');
    var color = btn.data('color');
    if($(element).is(':checked'))
    {
        btn.css('background',color);
        btn.css('color','white');
    }
    else
    {
        btn.css('background','white');
        btn.css('color',color);
    }
    btn.css('border','2px solid');
    btn.css('border-color',color);
    btn.css('border-radius','5px');

    if($(element).hasClass('hidden'))
    {
        btn.hide();
    }
    else
    {
        btn.show();
    }
}

window.refetchEvent = function(){
    $.ajax({
        url: window.eventDataUrl ,
        dataType: 'json',
        data: {
            schedule_id: lastClickedEvent.extendedProps.ID,
            date: moment.utc(lastClickedEvent.start.getTime()).format('YYYY-MM-DD')
        },
        success: function success(response){
            var extendedPropsToUpdate = ['fullDates', 'isEvent', 'ID', 'class_id', 'teacher_id', 'backgroundColor', 'classLevel', 'isVisible', 'isReserved', 'isEmpty', 'isWaitlisted', 'greyedPastClass', 'hideFull' , 'isStudentRegistered'];

            calendarInstance.batchRendering(function() {
                $(extendedPropsToUpdate).each(function(i, key){
                    if(response.event.hasOwnProperty(key))
                    {
                        lastClickedEvent.setExtendedProp(key, response.event[key]);
                    }
                });
            });
        }
    });
}

$(document).delegate("form#cancel_class_form button[type='submit'], form#cancel_class_form input[type='submit']", "click", function () {
  var button = $(this);
  Swal.fire({
    title: trans('messages.are-you-sure'),
    text: trans('messages.you-wont-be-able-to-revert-this'),
    confirmButtonText: trans('messages.yes-i-sure'),
    cancelButtonText: trans('messages.cancel'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  }).then(function (result) {
    if (result.value) {
      console.log(button.parents('form#cancel_class_form'));
      button.parents('form#cancel_class_form').submit();
    }
  });
  return false; // return confirm("Are you sure?");
});

$(document).delegate("form.delete button[type='submit'], form.delete a.dropdown-item, form.delete input[type='submit']", "click", function () {
  var button = $(this);
  Swal.fire({
    title: trans('messages.are-you-sure'),
    text: trans('messages.you-wont-be-able-to-revert-this'),
    confirmButtonText: trans('messages.yes-i-sure'),
    cancelButtonText: trans('messages.cancel'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
  }).then(function (result) {
    if (result.value) {
      button.parents('form.delete').submit();
    }
  });
  return false; // return confirm("Are you sure?");
});

$('#all_day').click(function() {
    const timeinputs = $('input[type="time"]');
    if($(this).is(':checked')) {
        timeinputs.removeAttr('required');
        timeinputs.parents('.form-group').hide();
    } else {
        timeinputs.attr('required', true);
        timeinputs.parents('.form-group').show();
    }
});

// Collapse ibox function
$('.collapse-link').on('click', function () {
    var ibox = $(this).closest('div.ibox');
    var button = $(this).find('i');
    var content = ibox.children('.ibox-content');
    content.slideToggle(200);
    button.toggleClass('fa-chevron-down').toggleClass('fa-chevron-up');
    ibox.toggleClass('').toggleClass('border-bottom');
    setTimeout(function () {
        ibox.resize();
        ibox.find('[id^=map-]').resize();
    }, 50);
});

$("form.delete a.submit").on('click', function() {
	$(this).parent().submit();
});

$("input[name='keyname']").onkeyup = function(e) {
    if(e.keyCode === 13) {
        $(this).parent().submit();
    }
    return true;
}

$("#change-password").change(function(){
	$('.password').toggle();
});

$('input#change-password').click(function() {
    if($(this).is(":checked")) {
        $('#password').show();
        $("#password input").attr("required", true);
        $('#newpassword').show();
        $("#newpassword input").attr("required", true);
    } else {
        $('#password').hide();
        $('#password input').removeAttr('required');
        $('#newpassword').hide();
        $('#newpassword input').removeAttr('required');
    }
});

Dropzone.autoDiscover = false;

$(document).ready(function() {
    const courseSelect = $('select[name="course_id"]');
    const unitSelect = $('select[name="unit_id"]');
    const testSelect = $('select[name="test_id"]');
    const assessmentTypeSelect = $('select[name="assessment_type"]');
    const testType = $('input[name="test_type"]');

    ToggleHelper.unit(courseSelect.val(), true);
    ToggleHelper.lesson(courseSelect.val(), unitSelect.val(), true);
    ToggleHelper.question(testSelect.val(), true);

    ToggleHelper.fields('.assessment-type', assessmentTypeSelect.val());
    ToggleHelper.fields('.test-type', testType.val());
    // ToggleHelper.paperTestPrefillTotalScore();

    $(document).on('change','select[name="assessment_question_type"]', function(){
        ToggleHelper.fields('.assessment-question-type', $(this).val());
    });

    ColorPicker.create('#color_picker', $('input[name="color_coding"]'));
    ColorPicker.create('#email_header_footer_color_picker', $('input[name="email_header_footer_color"]'));

    assessmentTypeSelect.change(function() {
        ToggleHelper.fields('.assessment-type', $(this).val());
    });

    testType.change(function() {
        ToggleHelper.fields('.test-type', $(this).val());
    });

    courseSelect.change(function() {
        ToggleHelper.unit($(this).val());
        ToggleHelper.lesson($(this).val(), unitSelect.val());
    });

    unitSelect.change(function() {
        ToggleHelper.lesson(courseSelect.val(), $(this).val());
    });

    testSelect.change(function() {
        ToggleHelper.question($(this).val());
    });

    $(document).on('change','.insert-image',function() {
        FileHelper.previewImage(this);
        FileHelper.update(this);
    });

    $('.insert-file').change(function() {
        FileHelper.update(this);
    });

    if($('.data-table').length > 0) $('.data-table').DataTable();

    // $('form.submit-test').submit(function() {
    //     return confirm("Are you sure?");
    // });
    $('form.submit-test button[type="submit"], form.submit-test input[type="submit"]').click(function () {
        var button = $(this);
        var form = button.parents('form.submit-test');
        if(form[0].checkValidity())
        {
            Swal.fire({
                title: trans('messages.are-you-sure'),
                text: trans('messages.you-wont-be-able-to-revert-this'),
                confirmButtonText: trans('messages.yes-i-sure'),
                cancelButtonText: trans('messages.cancel'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then(function (result) {
                if (result.value) {
                    form.submit();
                }
            });
            return false; // return confirm("Are you sure?");
        }
    });
})

$(document).delegate("form.cancel-reservation button", "click", function(e) {
    var _this2 = this;

    e.preventDefault();
    Swal.fire({
        title: trans('messages.are-you-sure'),
        text: trans('messages.you-wont-be-able-to-revert-this'),
        confirmButtonText: trans('messages.yes-i-sure'),
        cancelButtonText: trans('messages.cancel'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }).then(function (result) {
        if (result.value) {
          // if(confirm("Are you sure?")) {
          $('#reservation_alert').hide();
          $('#reservation_alert_danger').hide();
          $('#reservation_alert_warning').hide();
          var form = $(_this2).parent();
          var formData = form.serialize();
          var row = $(_this2).parent().parent().parent();
          var isSimpleCancel = form.hasClass('simple-cancel');
          var url = isSimpleCancel ? window.reservationSimpleCancelUrl : window.reservationCancelUrl;
          $.ajax({
            url: url,
            data: formData,
            beforeSend: function beforeSend() {
              $('.overload-content .preload').css({
                "display": "flex"
              });
            },
            success: function success(data) {
              if (data.success) {
                $('#reservation_alert').text(data.message);
                $('#reservation_alert').show();
                $('#reservation_h3').hide();
                row.remove();
                $(".fc-event.last-click-event").removeClass("full-class");
                $(".modal-footer.monthly-calendar #reserve_now").show();
                $(".modal-footer.monthly-calendar #waitlist_now").hide();

                if (!$('table.registered-students tr').length) {
                  $('h3#registered-students-title').hide();
                }
                refetchEvent();
              } else {
                $('#reservation_alert_danger').text(data.error);
                $('#reservation_alert_danger').show();
              }

              $('.overload-content .preload').css({
                "display": "none"
              });
            },
            error: function error(e) {
              // alert(e);
              Swal.fire(e);
            }
          }); // }
        }
    });
});

$('.level-selectize').selectize({
    plugins: ['remove_button'],
    delimiter: ',',
    persist: false,
    create: function(input) {
        return {
            value: input,
            text: input
        }
    }
});

window.capitalizeFirstLetter = function(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

window.refreshDropzonesForLessonForm = function(lessonId, userId){
    DropzoneHelper.downloadableFiles(lessonId, userId);
    DropzoneHelper.pdfFiles(lessonId, userId);
    DropzoneHelper.audioFiles(lessonId, userId);
    DropzoneHelper.extraMaterialFiles(lessonId, userId);
}

window.reInitializeQuestionsForm = function(){
    const assessmentQuestionTypeSelect = $('select[name="assessment_question_type"]');

    ToggleHelper.fields('.assessment-question-type', assessmentQuestionTypeSelect.val());
    RowHelper.bindRemove();
    RowHelper.bindAddOption();
}

window.copyToClipboard = function(str, element)
{
    const el = document.createElement('textarea');  // Create a <textarea> element
    el.value = str;                                 // Set its value to the string that you want copied
    el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
    el.style.position = 'absolute';
    el.style.left = '-9999px';                      // Move outside the screen to make it invisible
    document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
    const selected =
        document.getSelection().rangeCount > 0        // Check if there is any content selected previously
        ? document.getSelection().getRangeAt(0)     // Store selection if found
        : false;                                    // Mark as false to know no selection existed before
    el.select();                                    // Select the <textarea> content
    document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
    document.body.removeChild(el);                  // Remove the <textarea> element
    if (selected) {                                 // If a selection existed before copying
        document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
        document.getSelection().addRange(selected);   // Restore the original selection
    }

    if(element)
    {
        var original_text = $(element).text();
        var original_width = $(element).css('width');
        
        $(element).attr('disabled',true);
        $(element).css('width', original_width);
        $(element).text(trans('messages.copied'));

        setTimeout(() => {
            $(element).text(original_text);
            $(element).attr('disabled',false);
        }, 1000);
    }
}

$('#changeLanguage').change(function(){
   var url = $(this).data('url');
   var language = $(this).val();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        url: url,
        dataType: 'json',
        type:'POST',
        data: {
            language: language
        },
        success: function success(){
            window.location.reload();
        }
    });
});
