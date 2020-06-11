@php $comments = $schedule->comments()->whereDate('date', '=', $date)->orderBy('updated_at','desc')->get(); @endphp
@if(count($comments) > 0)
    <ul class="media-list">
    @foreach ($comments as $comment)
        <li class="media">
            <div class="media-body">
                <div class="well well-lg">
                    <h4 class="media-heading reviews">{!! $comment->user->name !!} <span>at {!! date('l d F, Y h:i:s A', strtotime($comment->getLocalCommentUpdatedAt())) !!}</span></h4>
                    <p class="media-comment">
                        {!! $comment->comment !!}
                    </p>
                    <!-- <a class="btn btn-info btn-circle " href="#" id="exit">{!! __('messages.edit') !!}</a>
                    <a class="btn btn-warning btn-circle" href="#" id="delete"> {!! __('messages.delete') !!}</a> -->
                </div>              
            </div>
        </li> 
    @endforeach         
    </ul> 
@endif
