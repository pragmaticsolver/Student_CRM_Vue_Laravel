@extends('layouts.app')

@php
    $expected_checkin_days = App\Settings::get_value('library_expected_checkin_days');
    if(!$expected_checkin_days)
    {
        $expected_checkin_days = 0;
    }
    $default_checkin_date = now()->addDays($expected_checkin_days)->format('Y-m-d');
    $today = today()->format('Y-m-d');
@endphp

@section('content')
<script type="text/javascript">
window.reservationUrl  =   "{{url('schedule/reservation_by_teacher')}}";
window.reservationCancelUrl  =   "{{url('attendance/cancel')}}";
</script>
<div class="row justify-content-center overload-content">
    <div class="preload">
        <div class="fa fa-spinner fa-spin" style="font-size:100px"></div>
    </div>
    <div class="col-lg-12" id="main_section" style="display:none;">
        <div class="row">
            <div class="col-lg-4">
                @if(isset($date))
                    <h1>{{$date}}</h1>
                @else
                    <h1>{{ __('messages.today') }}</h1>
                @endif
            </div>
            <div class="col-lg-8">
                <?php
                    if(isset($date)) {
                        $todayDate = date('Y-m-d', strtotime($date));
                    } else {
                        $todayDate = date('Y-m-d');
                    }
                    $tomorrowDate = date('Y-m-d', strtotime($todayDate . ' +1 day'));
                    $nextWeekDate = date('Y-m-d', strtotime($todayDate . ' +7 day'));
                ?>
                <div class="btn-group">
                    <a href="{{ url('/date/'.$tomorrowDate) }}" class="btn btn-success">{{ __('messages.tomorrow') }}</a>
                    <a href="{{ url('/date/'.$nextWeekDate) }}" class="btn btn-success">{{ __('messages.nextweek') }}</a>

                    <form class="mb-0" method="POST" action="{{ route('schedule.get.date') }}">
                        <div class="pull-left"></div>
                        <div class="pull-right">
                            @csrf
                            <div class="input-group">
                                <input type="date" name="date" class="form-control" value="<?php if(isset($date)) echo $date; ?>" required>
                                <span class="input-group-btn">
                                    <button class="btn btn-success" type="submit">{{ __('messages.datesearch') }}</button>
                                </span>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
        <div id="vue-app">
            @can('stats')
                <app-daily-stats date="{{ $date }}" v-bind:selected_teachers="selected_teachers"></app-daily-stats>
            @endcan
        </div>

        @if($todoAccessList->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="ibox collapsed">
                        <div class="ibox-title">
                            <h5>{{ __('messages.todos') }} ({{ $todoAccessList->count() }})</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"><i class="fa fa-chevron-down"></i></a>
                            </div>
                        </div>
                        <div class="ibox-content" style="display: none;">
                            <div class="row">
                                <div class="col-lg-12">
                                    @include('todo.list-todo')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        
        <div class="row">
            <div class="col-lg-4">
                <label>{{ __('messages.status') }}</label>
                <br>
                <div class="btn-group-toggle" data-toggle="buttons">
                    <label class="btn active mb-1" data-color="#3cb394">
                        <input type="checkbox" name="signed_in" value="1" checked autocomplete="off">{{ __('messages.signedin') }}
                    </label>
                    <label class="btn active mb-1" data-color="#3cb394">
                        <input type="checkbox" name="signed_in" value="0" checked autocomplete="off">{{ __('messages.not-signedin') }}
                    </label>
                </div>
            </div>
            <div class="col-lg-8">
                <label>{{ __('messages.teachers') }}</label>
                <br>
                <div class="btn-group-toggle" data-toggle="buttons">
                    @foreach($teachers as $teacher)
                        <label class="btn active mb-1" data-color="{{ $teacher->get_color_coding() }}">
                            <input type="checkbox" name="teachers_filter" value="{{ $teacher->id }}" checked autocomplete="off">{{ $teacher->nickname }}
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-12">
                <label>{{ __('messages.options') }}</label>
                <br>
                <div class="btn-group-toggle" data-toggle="buttons">
                    <label class="btn active mb-1" data-color="#3cb394">
                        <input type="checkbox" id="send_email_for_actions" name="send_email_for_actions" autocomplete="off">{{ __('messages.notify-student-&-teacher-via-appropriate-email-when-action-performed-from-this-page') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-success" id="reservation_alert" style="display:none;"></div>
                <div class="alert alert-danger" id="reservation_alert_danger" style="display:none;"></div>
                <div class="alert alert-warning" id="reservation_alert_warning" style="display:none;"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-hover" id="main_table">
                    <tr>
                        <th><h2>{{ __('messages.classname') }}</h2></th>
                        <th><h2>{{ __('messages.classtime') }}</h2></th>
                        <th><h2>{{ __('messages.classteacher') }}</h2></th>
                    </tr>
                    @if(count($schedules) > 0)
                        @foreach($schedules as $time => $schedules_array)
                            <tr class="time-row" data-time="{{ $time }}">
                                <td colspan="3"><h2>{{ $time }}</h2></td>
                            </tr>
                            @foreach($schedules_array as $schedule)
                                <tr class="schedule-row" data-teacher_id="{{ @$schedule->teacher->id }}" data-time="{{ $time }}" data-schedule_id="{{ $schedule->id }}">
                                    <td><a href="{{ route('schedule.show', $schedule->id) }}">{{$schedule->class->title}}</a></td>
                                    <td>{{$schedule->start_time}}-{{$schedule->end_time}}</td>
                                    <td>{{ @$schedule->teacher->nickname}}</td>
                                </tr>
                                @foreach($schedule->yoyaku()->where('date',$date)->get() as $yoyaku)
                                    @include('student-row')
                                @endforeach
                            @endforeach
                        @endforeach
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
    <div class="modal inmodal" id="AddContactModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.addcontact') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="add_contact_form" method="POST" action="{{ route('contact.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.name') }}</label>
                                    <div class="col-lg-10">
                                        <label class="col-lg-12 col-form-label" id="cm_student_name">Student Name</label>
                                        <input type="hidden" name="cm_customer_id" id="cm_customer_id" value="">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.contacttype') }}</label>
                                    <div class="col-lg-10">
                                        <label class="radio-inline"><input type="radio" name="type" value="denwa" checked=""> {{ __('messages.telephone') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="line"> {{ __('messages.line') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="direct"> {{ __('messages.direct') }}</label>
                                        <label class="radio-inline"><input type="radio" name="type" value="mail"> {{ __('messages.mail') }}</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-2 col-form-label">{{ __('messages.contents') }}</label>
                                    <div class="col-lg-10">
                                        <textarea name="message" id="message" rows="5" placeholder="{{ __('messages.pleasewritecontentshere') }}" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}" required>{{old('message')}}</textarea>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="submit_contact_btn">
                                    <span class="fa fa-pencil"></span> {{ __('messages.record') }}
                                    &nbsp<span id="form_spinner" class="fa fa-spinner fa-spin"></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#AddContactModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="CheckinModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.checkin') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="checkin_form" method="POST" class="modal_form"  action="{{ route('book.checkin.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.barcode') }}</label>
                                    <div class="col-lg-9">
                                        <input name="barcode" type="text" id="ckm_barcode" class="form-control" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.student') }}</label>
                                    <div class="col-lg-9">
                                        <label class="col-lg-12 col-form-label" id="ckm_student_name">Student Name</label>
                                        <input type="hidden" name="student_id" id="ckm_student_id" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.checkindate') }}</label>
                                    <div class="col-lg-9">
                                        <input name="checkin_date" type="date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1 submit_form_btn">
                                    </span> {{ __('messages.checkin') }}
                                    <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#CheckinModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="CheckoutModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.checkout') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form method="POST" id="checkout_form" class="modal_form" action="{{ route('book.checkout.store') }}">
                                @csrf
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.barcode') }}</label>
                                    <div class="col-lg-9">
                                        <input name="barcode" type="text" id="ckom_barcode" class="form-control" value="" required autofocus>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.student') }}</label>
                                    <div class="col-lg-9">
                                        <label class="col-lg-12 col-form-label" id="ckom_student_name">Student Name</label>
                                        <input type="hidden" name="student_id" id="ckom_student_id" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.checkoutdate') }}</label>
                                    <div class="col-lg-9">
                                    <input name="checkout_date" type="date" class="form-control" required value="{{ $today }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-lg-3 col-form-label">{{ __('messages.expectedcheckindate') }}</label>
                                    <div class="col-lg-9">
                                    <input name="expected_checkin_date" type="date" class="form-control" value="{{ $default_checkin_date }}" required>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1 submit_form_btn">
                                    </span> {{ __('messages.checkout') }}
                                    <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#CheckoutModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script src="{{ asset('public'.mix('js/page/home.js')) }}"></script>
@endpush
