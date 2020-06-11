<button type="button" class="btn btn-light" data-dismiss="modal">{{ __('messages.close')}}</button>

@can('control-attendance')
    @if($schedule->isPastClassCheckPasses(\Auth::user(),$date))
        @if($full)
        <button id="reserve_now" type="button" class="btn btn-primary" style="display: none;">{{ __('messages.reserve')}}</button>
        @else
        <button id="reserve_now" type="button" class="btn btn-primary">{{ __('messages.reserve')}}</button>
        @endif
    @endif
@endcan
