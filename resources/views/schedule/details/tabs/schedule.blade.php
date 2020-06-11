<h3>{{ __('messages.scheduledetails') }}</h3>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover ">
        <tbody>
            <tr>
                <th>{{ __('messages.type')}}</th>
                <td>{{ $schedule->get_type_label() }}</td>
            </tr>
            @if($schedule->is_repeat_class())
                <tr>
                    <th>{{ __('messages.date')}}</th>
                    <td>{{ $schedule->get_date() }}</td>
                </tr>
                <tr>
                    <th>{{ __('messages.openevery') }}</th>
                    <td>{{ $schedule->day_of_week  }}</td>
                </tr>
            @else
                <tr>
                    <th>{{ __('messages.date')}}</th>
                    <td>{{ $schedule->date }}</td>
                </tr>
            @endif
            <tr>
                <th>{{ __('messages.registeredstudents') }}</th>
                <td>{{ $schedule->yoyaku()->where('waitlist', 0)->distinct('customer_id')->count('customer_id') }}</td>
            </tr>
            <tr>
                <th>{{ __('messages.waitliststudents') }}</th>
                <td>{{ $schedule->yoyaku()->where('waitlist', 1)->distinct('customer_id')->count('customer_id') }}</td>
            </tr>
            @if($schedule->course_schedule)
                <tr>
                    <th>{{ __('messages.course')}}</th>
                    <td>{{ $schedule->course_schedule->course->title }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<div class="row">
    <div class="col-12">
        <div class="ibox collapsed">
            <div class="ibox-title">
                <h5>{{ __('messages.future-reseravtions') }}</h5>
                <div class="ibox-tools">
                    <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                </div>
            </div>
            <div class="ibox-content" style="display: none;">
                <div class="row">
                    <div class="col-lg-12">
                        @foreach($schedule->get_list_dates(1) as $date)
                        <div class="unit-progress">
                            <div class="row">
                                <div class="col-12">
                                    <a class="btn btn-secondary btn-block text-left unit_title_btn" data-toggle="collapse" href="#waitlist_{{ $date }}" role="button" aria-expanded="false" aria-controls="waitlist_{{ $date }}">
                                        {{ __('messages.dates') }}: {{ $date }}
                                    </a>
                                </div>
                            </div>
                            <div class="collapse " id="waitlist_{{ $date }}">
                            @if($schedule->yoyaku()->whereDate('date', '=', $date)->count() > 0)
                                <h3>{{ __('messages.students') }}</h3>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{ __('messages.firstname') }}</th>
                                            <th>{{ __('messages.lastname') }}</th>
                                            <th>{{ __('messages.namekanji') }}</th>
                                            <th>{{ __('messages.email') }}</th>
                                            <th>{{ __('messages.homephone') }}</th>
                                            <th>{{ __('messages.mobilephone') }}</th>
                                            <th>{{ __('messages.type') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($schedule->yoyaku()->whereDate('date', '=', $date)->get() as $yoyaku)
                                            @php($student = $yoyaku->student)
                                            <tr>
                                                <td>{{ $student->firstname }}</td>
                                                <td>{{ $student->lastname }}</td>
                                                <td>{{ $student->get_kanji_name() }}</td>
                                                <td>{{ $student->getEmailAddress() }}</td>
                                                <td>{{ $student->home_phone }}</td>
                                                <td>{{ $student->mobile_phone }}</td>
                                                <td>{{ $yoyaku->get_status() }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card card-body course-progress">
@foreach($schedule->get_list_dates() as $date)  
    <div class="unit-progress">
        <div class="row">
            <div class="col-12">
                <a class="btn btn-secondary btn-block text-left unit_title_btn" data-toggle="collapse" href="#date_{{ $date }}" role="button" aria-expanded="false" aria-controls="date_{{ $date }}">
                    {{ __('messages.dates') }}: {{ $date }}
                </a>
            </div>
        </div>
        <div class="collapse " id="date_{{ $date }}">
            <div class="card-body">
                @if($schedule->yoyaku()->whereDate('date', '=', $date)->count() > 0)
                    <div class="clearfix mb-2 mt-2">
                        <h3>{{ __('messages.students') }}
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>{{ __('messages.firstname') }}</th>
                                <th>{{ __('messages.lastname') }}</th>
                                <th>{{ __('messages.namekanji') }}</th>
                                <th>{{ __('messages.email') }}</th>
                                <th>{{ __('messages.homephone') }}</th>
                                <th>{{ __('messages.mobilephone') }}</th>
                                <th>{{ __('messages.type') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($schedule->yoyaku()->whereDate('date', '=', $date)->get() as $yoyaku)
                                @php($student = $yoyaku->student)
                                <tr>
                                    <td>{{ $student->firstname }}</td>
                                    <td>{{ $student->lastname }}</td>
                                    <td>{{ $student->get_kanji_name() }}</td>
                                    <td>{{ $student->getEmailAddress() }}</td>
                                    <td>{{ $student->home_phone }}</td>
                                    <td>{{ $student->mobile_phone }}</td>
                                    <td>{{ $yoyaku->get_status() }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('schedule.details.tabs.comments')
                @endif
            </div>
        </div>
    </div>
@endforeach
</div>