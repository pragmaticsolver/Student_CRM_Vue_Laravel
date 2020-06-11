@php $_is_past_class_check_passes = $yoyaku->schedule->isPastClassCheckPasses(\Auth::user(),$yoyaku->date); @endphp

<tr class="{{ $yoyaku->taiken == 1 &&  $yoyaku->waitlist == 0 ? 'bg-danger text-white' : ''}} {{$yoyaku->waitlist == 1 ? 'bg-warning' : ''}}"
    data-teacher_id="{{ $yoyaku->schedule->teacher_id }}"
    data-sigend_id="{{ $yoyaku->status === 0 ? 0 : 1}}"
    data-time="{{ substr($yoyaku->schedule->start_time,0,5).' - '.substr($yoyaku->schedule->end_time,0,5) }}" data-schedule_id="{{ $yoyaku->schedule->id }}"
    data-yoyaku_id="{{ $yoyaku->id }}"
    >
    <td>
        <a
            class="align-middle d-inline-block"
            style="{{ $yoyaku->taiken == 1 &&  $yoyaku->waitlist == 0 ? 'color:white' : ''}}"
            href="{{ url('/student/'.$yoyaku->customer_id) }}"
            data-toggle="popover" data-placement="right"
            data-img="{{ $yoyaku->student->image ? $yoyaku->student->getImageUrl() : '' }}">
            {{ $yoyaku->student->getFullNameAttribute() }}
        </a>
        
        @can('view-student-tags')
            @php $enable_edit = \Auth::user()->hasPermissionTo('edit-student-tags') ? 'true' : 'false'; @endphp
           <div class="attach-vue align-middle d-inline-block">
                <app-student-tags
                    :student_id="{{ $yoyaku->student->id }}"
                    :student_tags="{{ json_encode($yoyaku->student->getTags()) }}"
                    :enable_edit="{{ $enable_edit }}"
                ></app-student-tags>
           </div>
        @endcan
    </td>
    <td>
        @if($yoyaku->status === 1)
            <button class="btn btn-success btn-sm" type="submit" disabled>{{ __('messages.signedin') }}</button>
        @elseif($yoyaku->status === 2)
            <button class="btn btn-secondary btn-sm" type="button">{{ __('messages.cancelled') }}</button>
        @endif

        @can('control-attendance')
            @if($yoyaku->waitlist == 1)
                @if($_is_past_class_check_passes)
                    <form class="delete mb-0" id="reservation_form" method="GET" action="{{url('schedule/reservation_by_teacher')}}">
                        @csrf
                        <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                        <input type="hidden" name="customer_id" value="{{$yoyaku->customer_id}}">
                        <input type="hidden" name="schedule_id" value="{{$yoyaku->schedule_id}}">
                        <input type="hidden" name="date" value="{{$yoyaku->date}}">
                        <button type="button" class="btn btn-primary btn-sm btn_submit_form">{{ __('messages.reservenow') }}
                            <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                        </button>
                    </form>
                @endif
                <form class="delete mb-0" id="cancel_waitlist_form" method="GET" action="{{ url('schedule/waitlist_delete') }}">
                    <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                    <button type="button" class="btn btn-danger btn-sm btn_submit_form">{{ __('messages.delete') }}
                        <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                    </button>
                </form>
            @else
                @if($yoyaku->status === 0)
                    @can('attendance-add')
                        <form class="delete mb-0" method="POST" action="{{ route('attendance.store') }}">
                            @csrf
                            <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                            <input type="hidden" name="customer_id" value="{{$yoyaku->customer_id }}">
                            <input type="hidden" name="class_id" value="{{ $yoyaku->schedule->class_id }}">
                            <input type="hidden" name="teacher_id" value="{{ $yoyaku->schedule->teacher_id }}">
                            <input type="hidden" name="schedule_id" value="{{ $yoyaku->schedule_id }}">
                            <input type="hidden" name="payment_plan_id" value="{{ $yoyaku->schedule->class->payment_plan_id }}">
                            <input type="hidden" name="taiken" value="{{ $yoyaku->taiken }}">
                            <input type="hidden" name="start_date" value="{{ $yoyaku->start_date }}">
                            <input type="hidden" name="end_date" value="{{ $yoyaku->end_date }}">
                            <button class="btn btn-success btn-sm btn_submit_form" name="signin_btn" type="button">{{ __('messages.signin') }}
                                <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                        </form>
                    @endif
                    @if($_is_past_class_check_passes)
                        <form class="delete mb-0 cancel-reservation-form" method="GET" action="{{ route('attendance.cancel','home_page') }}">
                            @csrf
                            <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                            <input type="hidden" name="cancel_type" value="cancel">
                            <input type="hidden" name="cancel_future_reservations" value="0">
                            <div class="btn-group btn-sm">
                                <button class="btn btn-secondary btn-sm btn_submit_form btn_cancel_main" type="button">{{ __('messages.cancel') }}
                                    <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fa fa-caret-down"></span>
                                    <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item cancel_future_reservations btn_submit_form" href="JavaScript:void(0);">{{ __('messages.cancel-future-reservations') }}</a>
                                </div>
                            </div>
                        </form>
                        {{--
                            Do Not Remove - as need to restore this funcitonality in future 
                            <form class="delete mb-0" method="GET" action="{{ route('attendance.cancel','home_page') }}">
                            <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                            <input type="hidden" name="cancel_type" value="partial-penalty-cancel">
                            <button class="btn btn-warning btn-sm btn_submit_form" type="button">{{ __('messages.partialcancel') }}
                                <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                        </form> --}}
                        <form class="delete mb-0" method="GET" action="{{ route('attendance.cancel','home_page') }}">
                            <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                            <input type="hidden" name="cancel_type" value="full-penalty-cancel">
                            <button class="btn btn-danger btn-sm btn_submit_form" type="button">{{ __('messages.fullcancel') }}
                                <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                        </form>
                    @endif
                @elseif($yoyaku->status === 1)
                    <form class="delete mb-0" method="POST" action="{{ route('attendance.undo') }}">
                        @csrf
                        <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                        <input type="hidden" name="home_page" value="1">
                        <button class="btn btn-light btn-sm btn_submit_form" type="button">{{ __('messages.undo-signin') }}
                            <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                        </button>
                    </form>
                @elseif($yoyaku->status === 2)
                    <form class="delete mb-0" method="POST" action="{{ route('attendance.undo') }}">
                        @csrf
                        <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                        <input type="hidden" name="home_page" value="1">
                        <button class="btn btn-light btn-sm btn_submit_form" type="button">{{ __('messages.undo-cancel') }}
                            <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                        </button>
                    </form>

                    <form class="delete mb-0 delete-reservation-form" method="POST" action="{{ route('delete_reservation') }}">
                        @csrf
                        <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                        <input type="hidden" name="home_page" value="1">
                        <input type="hidden" name="delete_future_reservations" value="0">
                        <div class="btn-group btn-sm">
                            <button class="btn btn-danger btn-sm btn_submit_form btn_delete_main" type="button">{{ __('messages.delete') }}
                                <span class="preloader" style="display:none;">&nbsp<i class="fa fa-spinner fa-spin"></i></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="fa fa-caret-down"></span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item delete_future_reservations btn_submit_form" href="JavaScript:void(0);">{{ __('messages.delete-future-reservations') }}</a>
                            </div>
                        </div>
                    </form>
                @endif
            @endif
        @endcan
    </td>
    <td>
        @can('library')
            <button class="btn btn-primary btn-sm checkin_btn"
            data-student_name="{{ $yoyaku->student->getFullNameAttribute()}}"
            data-student_id="{{ $yoyaku->student->id }}"
            >{{ __('messages.checkin') }}</button>

            <button class="btn btn-primary btn-sm checkout_btn"
            data-student_name="{{ $yoyaku->student->getFullNameAttribute()}}"
            data-student_id="{{ $yoyaku->student->id }}"
            >{{ __('messages.checkout') }}</button>
        @endcan

        @can('contact-list')
            <button class="btn btn-secondary add_contact_btn btn-sm"
            data-student_name="{{ $yoyaku->student->getFullNameAttribute()}}"
            data-customer_id="{{ $yoyaku->student->id }}"
            >{{ __('messages.addcontact') }}</button>
        @endcan
    </td>
</tr>
