<div class="unit-progress">
    <div class="row">
        <div class="col-12">
        <h3>{{ __('messages.attachments') }}</h3>
        </div>
    </div>
    <div class="card-body">
        <form id="schedule_file_form" enctype="multipart/form-data" action="{{ route('files.upload') }}">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
            <input type="hidden" name="date" value="{{ $date }}">
            <div class="form-group row">
                <label for="email" class="col-sm-2 control-label">{!! __('messages.file') !!}</label>
                <div class="col-sm-8">  
                    <div class="file-upload-wrapper" data-text="Select your file!">
                        <input type="file" name="file" class="file-upload-field" value="">
                    </div>
                </div>
                <div class="col-sm-2">  
                    <button class="btn_save_class_files btn btn-primary my-1 pull-left">
                        {{ __('messages.save') }} &nbsp
                        <i class="prelaoder fa fa-spinner fa-spin" style="display:none;"></i>
                    </button>
                </div>
            </div>            
        </form>
        <div class="row">
            <div class="col-sm-12">  
                <span class="commentfile-details">
                    @include('schedule.details.tabs.commentfiles-list')
                </span>
                
            </div>
        </div>  
    </div>           
</div>                    
<div class="accordion" id="accordioncomments_{{ $date }}">
    <div class="card">
        <div class="card-header" id="comments_{{ $date }}">
            <h2 class="mb-0">
                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse_comments_{{ $date }}" aria-expanded="false" aria-controls="collapse_comments_{{ $date }}">
                {{ __('messages.comments') }}
                </button>
            </h2>
        </div>
        <div id="collapse_comments_{{ $date }}" class="collapse" aria-labelledby="comments_{{ $date }}" data-parent="#accordioncomments_{{ $date }}" style="">
            <div class="card-body">
                <form id="comment_form" enctype="multipart/form-data" action="{{ route('comments.add') }}">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                    <input type="hidden" name="date" value="{{ $date }}">
                    <div class="form-group">
                        <label for="email" class="col-sm-2 control-label">{!! __('messages.comment') !!}</label>
                        <div class="col-sm-12">
                            <textarea class="form-control" name="comment" id="comment" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">  
                            <button class="btn_save_class_comments btn btn-primary my-1 pull-left">
                                {{ __('messages.save') }} &nbsp
                                <i class="prelaoder fa fa-spinner fa-spin" style="display:none;"></i>
                            </button>
                        </div>
                    </div>            
                </form>
                        
                <span class="comments-details">
                    @include('schedule.details.tabs.comments-list')
                </span>
            </div>
        </div>
    </div>
</div>
