window.addEventListener('DOMContentLoaded', function() {

    $('.form-submits-via-ajax').submit(function(e) {
        e.preventDefault()

        const form = $(this)
        const submitButton = form.find('input[type="submit"]')
        submitButton.attr('disabled', true)

        data = form.serialize()
        axios.post(form.attr('action'),data) .then(res => {
            let data = res.data;
            if(data.status == 1) {
                toastr.success(data.message)
            } else {
                toastr.error(data.message || trans('messages.something-went-wrong'))
            }
            submitButton.removeAttr('disabled')
        })
        .catch(error => {
            if(error.response.status == 422)
            {
                const data = error.response.data;
                var errorMessage = '';
                const form_errors = data.errors;

                $.each(form_errors, function(key ,errors) {
                    errors.forEach((fieldError) => {
                        errorMessage += fieldError + '<br>';
                    })
                })
                
                Swal.fire({
                    title: data.message,
                    html: errorMessage,
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
            }
            else
            {
                toastr.error(error.response.data.message || trans('messages.something-went-wrong'))
            }
            submitButton.removeAttr('disabled')
        });
    })
});