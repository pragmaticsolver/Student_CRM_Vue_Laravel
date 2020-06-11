export default class FileHelper {
    static previewImage(input)
    {
        const previewImageDiv = $(input).parents('.input-file-wrapper').find('.preview-section');
        const previewImageEl = previewImageDiv.find('img');

        if (input.files && input.files[0]) {
            let reader = new FileReader();

            reader.onload = function(e) {
                previewImageDiv.show();
                previewImageEl.attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        } else {
            previewImageDiv.hide();
        }
    }

    static update(input) {
        let fileUpdate = $(input).parents('.input-file-wrapper').find('.file-update');
        fileUpdate.val('true');
    }

}
