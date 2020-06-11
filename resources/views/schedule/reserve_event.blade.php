<?php
    $reserved = 0;
?>
@if(!empty($users))
@foreach ($users as $user)
    @if(Auth::user()->id == $user->user_id)
        <?php
            $reserved = 1;
            break;
        ?>
    @endif
@endforeach
@endif
<button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close') }}</button>

@if($schedule->isPastClassCheckPasses(\Auth::user(),$date))
    @if(!$reserved && !$full)
        <button id="reserve_now" type="button" class="btn btn-primary">{{ __('messages.reservenow') }}</button>
    @else
        <form class="cancel-reservation mb-0 simple-cancel">
            @csrf
            <input type="hidden" name="yoyaku_id" value="{{$user->id}}">
            <button class="btn btn-danger" type="submit">{{ __('messages.cancel') }}</button>
        </form>
    @endif
@endif
