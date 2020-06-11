export default class DropzoneHelper {

    static extraMaterialFiles(lessonId, userId) {
        // Only initialize Dropzone if div is exist
        if ($('#extra_material_files').length) {
            const extramaterialFiles = new Dropzone("div#extra_material_files", {
                url: uploadLessonExtraMaterialFileUrl + '/' + lessonId,
                paramName: "file",
                addRemoveLinks: true,
                maxFilesize: 16,
                timeout: 50000,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    // Load exist file
                    const existExtraMaterialFiles = $('input[name="exist_extra_material_files"]').val();
                    if(existExtraMaterialFiles) {
                        const extraMaterialFiles = JSON.parse(existExtraMaterialFiles);
                        const Dropzone = this;

                        extraMaterialFiles.forEach(function(File) {
                            const mockFile = { name: File.name, size: 0, id: File.id };
                            Dropzone.options.addedfile.call(Dropzone, mockFile);
                            mockFile.previewElement.classList.add('dz-success');
                            mockFile.previewElement.classList.add('dz-complete');
                        });
                    }
                },
                // Rename file before upload to server
                renameFile: function(file) {
                    return file.name;
                },
                success: function(file, response){
                    file.id = response.id;
                },
                removedfile: function(file){
                    if(file.id)
                    {
                        // Send request to remove file on server
                        $.ajax({
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: deleteLessonFileUrl + '/' + file.id,
                            success: function(){
                                // Remove element dom node
                                const previewElement = file.previewElement;
                                if(previewElement !== null) previewElement.parentNode.removeChild(previewElement);
                            }
                        });
                    }
                }
            });
        }
    }

    static downloadableFiles(lessonId, userId) {
        // Only initialize Dropzone if div is exist
        if ($('#downloadable_files').length) {
            const downloadableFiles = new Dropzone("div#downloadable_files", {
                url: uploadLessonDownloadableFileUrl + '/' + lessonId,
                paramName: "file",
                addRemoveLinks: true,
                maxFilesize: 16,
                timeout: 50000,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function() {
                    // Load exist file
                    const existDownloadableFiles = $('input[name="exist_downloadable_files"]').val();
                    if(existDownloadableFiles) {
                        const downloadableFiles = JSON.parse(existDownloadableFiles);
                        const Dropzone = this;

                        downloadableFiles.forEach(function(downloadableFile) {
                            const mockFile = { name: downloadableFile.name, size: 0, id: downloadableFile.id };
                            Dropzone.options.addedfile.call(Dropzone, mockFile);
                            mockFile.previewElement.classList.add('dz-success');
                            mockFile.previewElement.classList.add('dz-complete');
                        });
                    }
                },
                // Rename file before upload to server
                renameFile: function(file) {
                    return file.name;
                },
                success: function(file, response){
                    file.id = response.id;
                },
                removedfile: function(file){
                    if(file.id)
                    {
                        // Send request to remove file on server
                        $.ajax({
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: deleteLessonFileUrl + '/' + file.id,
                            success: function(){
                                // Remove element dom node
                                const previewElement = file.previewElement;
                                if(previewElement !== null) previewElement.parentNode.removeChild(previewElement);
                            }
                        });
                    }
                }
            });
        }
    }

    static pdfFiles(lessonId, userId) {
        // Only initialize Dropzone if div is exist
        if ($('#pdf_files').length) {
            const pdfFiles = new Dropzone("div#pdf_files", {
                url: uploadLessonPdfFileUrl + '/' + lessonId,
                maxFilesize: 16,
                paramName: "file",
                addRemoveLinks: true,
                timeout: 50000,
                acceptedFiles: ".pdf",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function () {
                    // Load exist file
                    const existPdfFiles = $('input[name="exist_pdf_files"]').val();
                    if(existPdfFiles) {
                        const pdfFiles = JSON.parse(existPdfFiles);
                        const Dropzone = this;

                        pdfFiles.forEach(function(pdfFile) {
                            const mockFile = { name: pdfFile.name, size: 0, id: pdfFile.id };
                            Dropzone.options.addedfile.call(Dropzone, mockFile);
                            mockFile.previewElement.classList.add('dz-success');
                            mockFile.previewElement.classList.add('dz-complete');
                        });
                    }
                },
                // Rename file before upload to server
                renameFile: function (file) {
                   
                    return file.name;
                },
                success: function(file, response){
                    file.id = response.id;
                },
                removedfile: function (file) {
                    if(file.id)
                    {
                        // Send request to remove file on server
                        $.ajax({
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: deleteLessonFileUrl + '/' + file.id,
                            success: function(){
                                // Remove element dom node
                                const previewElement = file.previewElement;
                                if (previewElement !== null) previewElement.parentNode.removeChild(previewElement);
                            }
                        });
                    }
                }
            });
        }
    }

    static audioFiles(lessonId, userId) {
        // Only initialize Dropzone if div is exist
        if ($('#audio_files').length) {
            const audioFiles = new Dropzone("div#audio_files", {
                url: uploadLessonAudioFileUrl + '/' + lessonId,
                paramName: "file",
                addRemoveLinks: true,
                timeout: 50000,
                acceptedFiles: ".mp3, .mpga",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                init: function () {
                    // Load exist file
                    const existAudioFiles = $('input[name="exist_audio_files"]').val();
                    if(existAudioFiles) {
                        const audioFiles = JSON.parse(existAudioFiles);
                        const Dropzone = this;

                        audioFiles.forEach(function(audioFile) {
                            const mockFile = { name: audioFile.name, size: 0, id: audioFile.id };
                            Dropzone.options.addedfile.call(Dropzone, mockFile);
                            mockFile.previewElement.classList.add('dz-success');
                            mockFile.previewElement.classList.add('dz-complete');
                        });
                    }
                },
                // Rename file before upload to server
                renameFile: function (file) {
                    return file.name;
                },
                success: function(file, response){
                    file.id = response.id;
                },
                removedfile: function (file){
                    if(file.id)
                    {
                        // Send request to remove file on server
                        $.ajax({
                            method: "POST",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            url: deleteLessonFileUrl + '/' + file.id,
                            success: function(){
                                // Remove element dom node
                                const previewElement = file.previewElement;
                                if (previewElement !== null) previewElement.parentNode.removeChild(previewElement);
                            }
                        });
                    }
                }
            });
        }
    }
}
