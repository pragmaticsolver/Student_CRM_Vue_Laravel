<?php
$reserved = 0;
?>
@foreach ($users as $user)
    @if(Auth::user()->id == $user->user_id)
        <?php
        $reserved = 1;
        break;
        ?>
    @endif
@endforeach

<div class="">
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
    @if($reserved)
        <div class="alert alert-success">{{ __('messages.reserved')}}</div>
    @endif
    <table class="table table-striped table-bordered table-hover ">
        <tr>
            <th>{{ __('messages.event')}}</th>
            <td>{{$schedule->class->title}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.date')}}</th>
            <td>{{$date}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.description')}}</th>
            <td>{{ $schedule->class->description }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.eventtime')}}</th>
            <td>
                @if($schedule->type != '2')
                    {{$schedule->start_time}} - {{$schedule->end_time}}
                @else
                    {{ __('All Day') }}
                @endif
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.cost') }}</th>
            <td>{{ $schedule->event()->first()->cost }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.size') }}</th>
            <td>{{ $schedule->event()->first()->size }}</td>
        </tr>
    </table>
    @if(!$reserved && $full == false && $schedule->isPastClassCheckPasses(\Auth::user(),$date))
        <h3 id="reservation_h3">{{ __('messages.reserveq')}}</h3>
    @endif

    <form id="reservation_form" class="reserve">
        <input type="hidden" name="schedule_id" value="{{$id}}"/>
        <input type="hidden" name="date" value="{{$date}}"/>
        <input type="hidden" name="customer_id" value="{{ $student_id }}">
    </form>
</div>
