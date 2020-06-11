<table class="table table-hover">
    <tbody>
        @if(!$activityLogs->isEmpty())
            <tr>
                <th  data-collumn_name="id" class="collumn_sort" style="width:152px">{{ __('messages.activity-time') }}</th>
                <th style="width:200px">{{ __('messages.activity-by') }}</th>
                <th>{{ __('messages.activity') }}</th>
            </tr>
            @foreach($activityLogs as $activityLog)
                <tr>
                    <td>{{ $activityLog->get_datetime($school_timezone) }}</td>
                    <td>{{ $activityLog->get_creted_by_user_name() }}</td>
                    <td>{{ $activityLog->get_disaplay_text() }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
@if($activityLogs->isEmpty())
<div class="text-center">
    <div>{{ __('messages.no-activities-found') }}</div>
</div>
@endif
