window.addEventListener('DOMContentLoaded', function() {
    $('.option-info').hide();
    $('.toggle-group').click( function() {
        if (!$(this).parent().find('input').prop('checked')) {
            $('#' + $(this).parent().find('input').data('id')).show();
        } else { 
            $('#' + $(this).parent().find('input').data('id')).hide();
        }
    });
});
