@php $files = $schedule->files()->whereDate('date', '=', $date)->orderBy('updated_at','desc')->get(); @endphp
@if(count($files) > 0)
    <ul class="media-list">
    @foreach ($files as $file)
        <li class="media">
            <div class="media-body">
                <div class="well well-lg">
                    <div class="media-comment pull-left">
                        {!! $file->getAttachment() !!}
                    </div>
                    <h4 class="media-heading reviews"><span>{{ __('messages.uploadby') }} </span>{!! $file->user->name !!} <span>at {!! date('l d F, Y h:i:s A', strtotime($file->getLocalFileUpdatedAt())) !!}</span></h4>
                    <!-- <a class="btn btn-info btn-circle " href="#" id="exit">{!! __('messages.edit') !!}</a>
                    <a class="btn btn-warning btn-circle" href="#" id="delete"> {!! __('messages.delete') !!}</a> -->
                </div>              
            </div>
        </li> 
    @endforeach         
    </ul> 
@endif                  
