<div id="video-lightbox" class="lightbox" onclick="hideVideo('video-lightbox','youtube')">
    <div class="lightbox-container">  
        <div class="lightbox-content">
        
        <button onclick="hideVideo('video-lightbox','youtube')" class="lightbox-close">
            {{__('lightbox-close')}}
        </button>
        <div class="video-container">
            <iframe id="youtube" width="960" height="540" src="" frameborder="0" allowfullscreen></iframe>
        </div>      
        
        </div>
    </div>
</div>

<div class="modal inmodal" id="EditFileModal" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="edit_filename_form" enctype="multipart/form-data" action="{{ url('/update_file_name') }}">
            @csrf
            <input type="hidden" name="file_id" value="" id="file_id">
            <input type="hidden" name="type" value="" id="type">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('messages.editFileName') }}</h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow-y: scroll;">
                <div class="row mr-1">
                    <div class="col-12 form-fields">
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">{{ __('messages.file-name') }}</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="file_name" name="file_name" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col">
                    <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="edit_filename_sumbit_btn">
                        {{ __('messages.edit') }}
                        <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                    </button>
                    <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#EditFileModal').modal('hide');">{{ __('messages.cancel') }}</button>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
