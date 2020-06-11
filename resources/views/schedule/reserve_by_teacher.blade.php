@can('cancel-class')
<div class="cancel-class mr-auto">
    <form id="cancel_class_form" method="POST" action="{{ route('cancel.class') }}" style="display:inline;">
        @csrf
        <input type="hidden" name="schedule_id" value="{{$id}}"/>
        <input type="hidden" name="date" value="{{$date}}"/>
        <input type="hidden" name="send_email" value="0"/>
        <button id="cancel_class" type="button" class="btn btn-danger">{{ __('messages.cancel-class') }}</button>
        @if($schedule->type == \App\Schedules::CLASS_REPEATED_TYPE)
            <button id="cancel_multiple" type="button" class="btn btn-danger">{{ __('messages.cancel-multiple') }}</button>
        @endif
    </form>
</div>
@endcan
<button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>

@can('control-attendance')
    @if($schedule->isPastClassCheckPasses(\Auth::user(),$date))
        @if($full)
        <button id="reserve_now" type="button" class="btn btn-primary" style="display: none;">{{ __('messages.reserve')}}</button>
        <button id="waitlist_now" type="button" class="btn btn-primary">{{ __('messages.waitlistnow') }}</button>
        @else
        <button id="reserve_now" type="button" class="btn btn-primary">{{ __('messages.reserve')}}</button>
        <button id="waitlist_now" type="button" class="btn btn-primary" style="display: none;">{{ __('messages.waitlistnow') }}</button>
        @endif
    @endif
@endcan
