window.addEventListener('DOMContentLoaded', function() {
    $('#levels').select2({
        width: '100%',
        placeholder: trans('messages.please-select-level-s')
    });

    if ($('#studentImage').length)
    {
        const studentImage = new Dropzone("div#studentImage", {
            url: uploadImageUrl,
            maxFilesize: 12,
            maxFiles:1,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });

                var uploadedFile = $('#uploadedStudentImage').val();
                if(uploadedFile) {
                    uploadedFile = JSON.parse(uploadedFile);
                    // accepted: true is required for working of maxFiles
                    const mockFile = { name: uploadedFile.name, size: uploadedFile.size, accepted: true, upload: { filename : uploadedFile.upload.filename } };
                    this.files.push(mockFile); // add to files array
                    this.emit("addedfile", mockFile);
                    this.emit("complete", mockFile);
                    this.emit("thumbnail", mockFile, uploadedFile.url);
                }
            },
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                return time+file.name;
            },
            acceptedFiles: ".jpeg,.jpg,.png,.gif",
            addRemoveLinks: true,
            timeout: 50000,
            removedfile: function(file)
            {
                var name = file.upload.filename;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: removeImageUrl,
                    data: { filename: name },
                    success: function (data){
                    },
                    error: function(e) {
                        console.log(e);
                    }});
                var fileRef;
                return (fileRef = file.previewElement) != null ?
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function(file, response)
            {
            },
            error: function(file, response)
            {
                return false;
            }
        });
    }
    $('.toggle-group').click( function() {
        if (!$(this).parent().find('input').prop('checked')) {
            $('#' + $(this).parent().find('input').data('id')).show();
        } else { 
            $('#' + $(this).parent().find('input').data('id')).hide();
        }
    });
});
