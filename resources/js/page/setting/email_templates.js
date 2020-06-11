window.addEventListener('DOMContentLoaded', function() {

    // Disable submit on enter key - as smtp details need to be tested before submit.
    $(document).on('keypress', ':input:not(textarea):not([type=submit])', function (e) {
        if (e.which == 13) e.preventDefault();
    });

    $('[data-toggle="tooltip"]').tooltip();

    $('#remove_eh_image_btn').click(function(){
        $('#remove_email_header_image').val(1);
        $('#email_header_image_preview_section').hide();
    });

    $('#test-mail-btn').click(function(){
        $('#label_smtp_host').text($('#smtp_host').val());
        $('#label_smtp_port').text($('#smtp_port').val());
        $('#label_smtp_username').text($('#smtp_username').val());
        $('#label_smtp_password').text($('#smtp_password').val());
        $('#label_smtp_from_address').text($('#smtp_from_address').val());
        $('#label_smtp_from_name').text($('#smtp_from_name').val());
        
        $('#test_smtp_host').val($('#smtp_host').val());
        $('#test_smtp_port').val($('#smtp_port').val());
        $('#test_smtp_username').val($('#smtp_username').val());
        $('#test_smtp_password').val($('#smtp_password').val());
        $('#test_smtp_from_address').val($('#smtp_from_address').val());
        $('#test_smtp_from_name').val($('#smtp_from_name').val());

        $('#test-smtp-modal').modal('show');
    });

    $('#submit_test_smtp').click(function(){
        
        $('#test-smtp-modal .preloader').show();
        $('#submit_test_smtp').attr('disabled', true);

        $.ajax({
            type: 'POST',
            url: route('mail.send-test-email').url(),
            data: $('#test_smtp_form').serialize(),
            success: function(response){
                if(response.status != 1)
                {
                    var message = response.message || trans('messages.something-went-wrong')
                    Swal.fire({
                        text: message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                else
                {
                    Swal.fire({
                        text: response.message,
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                    });
                }
                $('#submit_test_smtp').attr('disabled', false);
                $('#test-smtp-modal .preloader').hide();
                $('#test-smtp-modal').modal('hide');
            },
            error: function(e){
                $('#submit_test_smtp').attr('disabled', false);
                $('#test-smtp-modal .preloader').hide();
                Swal.fire({
                    text: trans('messages.something-went-wrong'),
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                $('#test-smtp-modal').modal('hide');
            }
        });
    });
});