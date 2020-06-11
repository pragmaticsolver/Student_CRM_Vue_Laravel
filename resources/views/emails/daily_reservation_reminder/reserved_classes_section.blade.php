@if(count($reservedClasses) > 0)
    <h3>{{ __('messages.reserved-classes') }}</h3>
    @foreach($reservedClasses as $record)
        @php
            $yoyaku = $record['yoyaku'];
            $zoomMeeting = $record['zoomMeeting'];
        @endphp
        <div style="border: 2px dotted black;padding: 10px;margin-bottom:5px;">
            <p>
                <b>
                    {{ @$yoyaku->schedule->class->title }} 
                    <span style="float:right">
                        {{ @$yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time }}</span>
                </b>
            </p>
            @if($zoomMeeting)
                <p><em>{{ __('messages.zoom-meeting-details') }}</em></p>
                <p>{{ __('messages.join-url') }}:  <a href="{{ $zoomMeeting->join_url }}" target="_blank">{{ $zoomMeeting->join_url }}</a></p>
                <p>{{ __('messages.meeting-id') }}: {{ $zoomMeeting->display_meeting_id }}</p>
                <p>{{ __('messages.password') }}: {{ $zoomMeeting->password }}</p>
            @endif
            @if($reminder['class_reminder'])
                <p>
                    @php $id = encrypt($yoyaku->id); @endphp
                    <a href="{{ route('cancel_reservation',$id) }}" class="btn btn-danger" target="_blank">{{ $button_texts['cancel-class-reservation'] }}</a>
                </p>
            @endif
        </div>
    @endforeach
@endif
