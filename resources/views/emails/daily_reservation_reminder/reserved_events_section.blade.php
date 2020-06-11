@if(count($reserverdEvents) > 0)
    <h3>{{ __('messages.reserved-events') }}</h3>
    <table class="email-inner-body" align="center" width="570" cellpadding="0" cellspacing="0">
        <tr>
            <td class="re-email-content-cell" style="width:205px;">{{ __('messages.event') }}</td>
            <td class="re-email-content-cell" style="width:253px;">{{ __('messages.time') }}</td>
            @if(\App\Settings::get_value('event_reminder'))
                <td class="re-email-content-cell">{{ __('messages.action') }}</td>
            @endif
        </tr>
        @foreach($reserverdEvents as $yoyaku)
            @php $is_all_day_event = $yoyaku->schedule->type == \App\Schedules::EVENT_ALLDAY_TYPE; @endphp
            <tr>
                <td class="re-email-content-cell">{{ @$yoyaku->schedule->class->title }}</td>
                <td class="re-email-content-cell">
                    @if($is_all_day_event)
                        {{ __('messages.allday') }}
                    @else
                        {{ @$yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time }}
                    @endif
                </td>
                @if($reminder['event_reminder'])
                <td class="re-email-content-cell">
                    {{-- No Cancellation for all day events --}}
                    @if(!$is_all_day_event)
                        @php $id = encrypt($yoyaku->id); @endphp
                        <a href="{{ route('cancel_reservation',$id) }}" class="btn btn-danger" target="_blank">{{ $button_texts['cancel-event-reservation'] }}</a>
                    @endif
                </td>
                @endif
            </tr>
        @endforeach
    </table>
@endif
