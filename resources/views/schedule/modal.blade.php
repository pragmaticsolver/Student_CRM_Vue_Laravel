<?php
    $reserved = 0;
    $waitlist = 0;
?>
@foreach ($users as $user)
    @if(Auth::user()->id == $user->user_id)
        <?php
            $reserved = 1;
            $waitlist = $user->waitlist;
            break;
        ?>
    @endif
@endforeach
<div class="">
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
    @if($reserved && !$waitlist)
    <div class="alert alert-success">{{ __('messages.reserved')}}</div>
    @endif
    @if($reserved && $waitlist)
    <div class="alert alert-success">{{ __('messages.waitlisted')}}</div>
    @endif
    <table class="table table-striped table-bordered table-hover ">
        <tr>
            <th>{{ __('messages.class')}}</th>
            <td>{{$schedule->class->title}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.date')}}</th>
            <td>{{$date}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.classtime')}}</th>
            <td>{{$schedule->start_time}} - {{$schedule->end_time}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.classteacher')}}</th>
            <td>{{ $schedule->teacher->nickname }}</td>
        </tr>
        @if($schedule->course_schedule)
            <tr>
                <th>{{ __('messages.course')}}</th>
                <td>{{ $schedule->course_schedule->course->title }}</td>
            </tr>
        @endif
    </table>

    @if($reserved && $zoomMeeting)
        <div class="border border-info my-2 p-2">
            <p><b>{{ __('messages.zoom-meeting-details') }}</b></p>
            <p><b>{{ __('messages.join-url') }}: </b><a href="{{ $zoomMeeting->join_url }}" target="_blank">{{ $zoomMeeting->join_url }}</a></p>
            <p><b>{{ __('messages.meeting-id') }}:</b> {{ $zoomMeeting->display_meeting_id }}</p>
            <p><b>{{__('messages.password')}}:</b> {{ $zoomMeeting->password }}</p>
            <a class="btn btn-info btn-sm" href="{{ $zoomMeeting->join_url }}" target="_blank">{{ __('messages.join-zoom-meeting') }}</a>
        </div>
    @endif
    @if($reserved && !$waitlist)
        <div class="border border-info my-2 p-2">
            <a class="btn btn-info btn-sm" href="{{ route('schedule.class.details',['schedule_id' => $id]) }}?date={{$date}}">{{ __('messages.classdetails') }}</a>
        </div>
    
    @endif
    @if(!$reserved && $full == false && $schedule->isPastClassCheckPasses(\Auth::user(),$date))
        <h3 id="reservation_h3">{{ __('messages.reserveq')}}</h3>
    @endif

    <form id="reservation_form" class="waitlisting reserve">
        <input type="hidden" name="schedule_id" value="{{$id}}"/>
        <input type="hidden" name="date" value="{{$date}}"/>
    </form>
</div>
