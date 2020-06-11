<h3>{{ __('messages.classdetails') }}</h3>
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <tr>
            <th>{{ __('messages.class')}}</th>
            <td>{{$schedule->class->title}}</td>
        </tr>
        <tr>
            <th>{{ __('messages.classtime')}}</th>
            <td>
                {{$schedule->start_time}} - {{$schedule->end_time}}
            </td>
        </tr>
        <tr>
            <th>{{ __('messages.classteacher')}}</th>
            <td>{{ $schedule->teacher->nickname }}</td>
        </tr>
        <tr>
            <th>{{ __('messages.size') }}</th>
            <td>{{ $schedule->class->getSize() }}</td>
        </tr>
    </table>
</div>
