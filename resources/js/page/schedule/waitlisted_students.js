window.addEventListener('DOMContentLoaded', function() {
    $(document).delegate("#reserve_now","click", function() {
        $('#reservation_alert').hide();
        $('#reservation_alert_danger').hide();
        $('#reservation_alert_warning').hide();
        if($(this).parent().hasClass("facing-calendar")) {
            $(this).attr('disabled',true);
        }
        if($(this).parent().attr('id') == 'reservation_form') {
            var form = $(this).parent().serialize();
            var reserve_from_waitlist = $(this).parent().parent().parent();
        } else {
            var form = $('#reservation_form.reserve').serialize();
        }

        $.ajax({
            url:reservationUrl,
            data:form,
            beforeSend: function(){
                $('.overload-content .preload').css({"display" : "flex"});
            },
            success:function(data){
                if(data.success){

                    $('#reservation_alert').text(data.message);
                    $('#reservation_alert').show();
                    $('#reservation_h3').hide();

                    if(data.warning !== undefined) {
                        $('#reservation_alert_warning').text(data.warning);
                        $('#reservation_alert_warning').show();
                    }

                    if(data.full) {
                        $(".modal-footer.monthly-calendar #reserve_now").hide();
                        $(".modal-footer.monthly-calendar #waitlist_now").show();
                    }

                    if(reserve_from_waitlist !== undefined) {
                        reserve_from_waitlist.remove();
                    }

                    $('h3#registered-students-title').show();

                    if(!$('table.waitlisted-students tr').length) {
                        $('h3#waitlisted-students-title').hide();
                    }
                }else{
                    $('#reservation_alert_danger').text(data.error);
                    $('#reservation_alert_danger').show();
                }

                $('.overload-content .preload').css({"display" : "none"});
            },
            error:function(e){
                //alert(e);
                Swal.fire(e);
            },
        });
    });
});
