window.addEventListener('DOMContentLoaded', function(){
    $('#paper_test_id').change(function(){
        $('#total_score').val($("#paper_test_id option:selected").data('total_score'));
    });

    if ($('#pdf_file').length) {
        const pdfDropzone = new Dropzone("div#pdf_file", {
            url: upload_temp_file_url,
            maxFiles:1,
            paramName: "file",
            addRemoveLinks: true,
            timeout: 50000,
            acceptedFiles: ".pdf",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });

                var uploadedPdfFile = $('#uploaded_pdf_file').val();
                if(uploadedPdfFile) {
                    uploadedPdfFile = JSON.parse(uploadedPdfFile);
                    // accepted: true is required for working of maxFiles
                    const mockFile = { name: uploadedPdfFile.name, size: uploadedPdfFile.size, accepted: true };
                    this.files.push(mockFile); // add to files array
                    this.emit("addedfile", mockFile);
                    this.emit("complete", mockFile);
                }
            },
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                return time+file.name;
            },
            removedfile: function(file)
            {
                $('#remove_old_file').val(1);
                $('#temp_file_path').val('');
                var fileRef;
                return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function(file, response)
            {
                $('#temp_file_path').val(response.file_path);
                $('#remove_old_file').val(1);
            }
        });
    }
});
