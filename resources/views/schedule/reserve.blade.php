<?php
    $reserved = 0;
    $waitlist = 0;
?>
@if(!empty($users))
@foreach ($users as $user)
    @if(Auth::user()->id == $user->user_id)
        <?php
            $reserved = 1;
            $waitlist = $user->waitlist;
            break;
        ?>
    @endif
@endforeach
@endif
<button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close') }}</button>

@if($schedule->isPastClassCheckPasses(\Auth::user(),$date))
    @if(!$reserved)
        @if(!$full)
            <button id="reserve_now" type="button" class="btn btn-primary">{{ __('messages.reservenow') }}</button>
        @else
            <button id="waitlist_now" type="button" class="btn btn-primary">{{ __('messages.waitlistnow') }}</button>
        @endif
    @else
        @if(!$waitlist)
            <form class="delete" method="GET" action="{{ route('attendance.cancel', 'normal') }}">
                @csrf
                <input type="hidden" name="yoyaku_id" value="{{$user->id}}">
                <input type="hidden" name="cancel_type" value="cancel">
                <button class="btn btn-danger" type="submit">{{ __('messages.cancel') }}</button>
            </form>
        @endif
    @endif
@endif
