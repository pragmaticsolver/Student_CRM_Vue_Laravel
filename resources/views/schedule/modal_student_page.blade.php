<div class="">
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
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
            <td>{{$schedule->teacher->nickname}}</td>
        </tr>
        @if($schedule->course_schedule)
            <tr>
                <th>{{ __('messages.course')}}</th>
                <td>{{ $schedule->course_schedule->course->title }}</td>
            </tr>
        @endif
        <tr>
            <th>{{ __('messages.attendance-status')}}</th>
            <td>
                {{ $yoyaku->attendance_status }}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.class-usage')}}</th>
            <td>
                @php
                    $msg = '';
                    if($yoyaku->classUsage)
                    {
                        if($yoyaku->classUsage->month_year != $yoyaku->classUsage->used_month_year )
                        {
                            $msg .= "Left over class of ".substr($yoyaku->classUsage->month_year,0,7);
                        }
                        else
                        {
                            $msg .= "Class of ".substr($yoyaku->classUsage->month_year,0,7);
                        }
                        $msg .= $yoyaku->classUsage->is_paid == 0 ? ' (Unpaid)' : '';
                    }
                    if(!$msg){
                        $msg = '-';
                    }
                @endphp
                {{ $msg }}
            </td>
        </tr>
    </table>
</div>
