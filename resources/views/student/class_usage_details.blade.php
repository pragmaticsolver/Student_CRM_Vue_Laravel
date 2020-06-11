<div class="row">
    <div class="col-sm-4 my-1">
        <button class="btn btn-block btn_fetch_class_usage btn-secondary" data-month_year="{{ $previes_year_date }}"><span class="fa fa-chevron-left"></span> {{ __('messages.previous-year') }}</button>
    </div>
    <div class="col-sm-4 my-1">
        <button class="btn btn-block btn_fetch_class_usage btn-secondary" data-month_year="{{ $current_display_year_date }}">{{ __('messages.refresh') }}</button>
    </div>
    <div class="col-sm-4 my-1">
        <button class="btn  btn-block btn_fetch_class_usage btn-secondary" data-month_year="{{ $next_year_date }}">{{ __('messages.next-year') }} <span class="fa fa-chevron-right"></span></button>
    </div>
</div>
<div class="row my-2">
    @foreach($class_usage_details as $details)
        <div class="col-sm-2 class-usage-info {{ $details['month_year'] == \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->firstOfMonth()->format('Y-m-d')  ? 'active' : ''}}" data-month_year="{{ $details['month_year'] }}">
            <div class="text-center">
                <h5>{{ $details['title'] }}</h6>
            </div>

            <div class="pull-left">{{ __('messages.paid') }}</div>
            <div class="pull-rigth text-right">{{ $details['paid'] }}</div>

            <div class="pull-left">{{ __('messages.unpaid') }}</div>
            <div class="pull-rigth text-right">{{ $details['unpaid'] }}</div>

            <div class="pull-left">{{ __('messages.used') }}</div>
            <div class="pull-rigth text-right">{{ $details['used'] }}</div>

            <div class="pull-left">{{ __('messages.used-left-overs') }}</div>
            <div class="pull-rigth text-right">{{ $details['used_leftovers'] }}</div>

            <div class="pull-left">{{ __('messages.new-left-overs') }}</div>
            <div class="pull-rigth text-right">{{ $details['new_leftovers'] }}</div>

            <div class="pull-left">{{ __('messages.left-overs') }}</div>
            <div class="pull-rigth text-right">{{ $details['leftovers'] }}</div>

            <div class="pull-left">{{ __('messages.expiring') }}</div>
            <div class="pull-rigth text-right">{{ $details['expiring'] }}</div>

            <div class="pull-left">{{ __('messages.reserved') }}</div>
            <div class="pull-rigth text-right">{{ $details['reserved'] }}</div>

            <div class="pull-left">{{ __('messages.cancelled') }}</div>
            <div class="pull-rigth text-right">{{ $details['cancelled'] }}</div>

            <div class="clearfix"></div>
        </div>
    @endforeach
</div>
<div class="row">
    <p>
        <span class="fa fa-info-circle"></span>
        </em>{{ __('messages.it-may-take-some-time-to-get-your-recent-actions-to-be-reflacted-to-the-details-shown-on-this-section') }}</em>
    </p>
</div>
