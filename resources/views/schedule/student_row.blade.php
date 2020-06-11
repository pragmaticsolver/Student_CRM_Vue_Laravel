<tr>
    <th class="p-2 {{  $yoyaku->taiken == 1 ? 'bg-danger text-white' : '' }}" >
        <div class="pull-left" style="width:50%">
            <label class="mt-2 align-middle d-inline-block">{{  $yoyaku->student->getFullNameAttribute() }}</label>
            @can('view-student-tags')
                @php $enable_edit = \Auth::user()->hasPermissionTo('edit-student-tags') ? 'true' : 'false'; @endphp
                <app-student-tags class="attach-vue align-middle d-inline-block"
                    :student_id="{{ $yoyaku->student->id }}"
                    :student_tags="{{ json_encode($yoyaku->student->getTags()) }}"
                    :enable_edit="{{ $enable_edit }}"
                ></app-student-tags>
            @endcan
        </div>
        <div class="pull-left" style="width:50%">
            @if($yoyaku->status === 1)
                <button class="btn btn-sm btn-success" style="margin:1px;" type="submit" disabled>{{ __('messages.signedin') }}</button>
            @endif

            @can('control-attendance')
                @if($yoyaku->status === 0)

                    @can('attendance-add')
                        <form class="delete mb-0" id="signin" method="POST" action="{{ route('attendance.store') }}">
                            @csrf
                            <input type="hidden" name="yoyaku_id" value="{{  $yoyaku->id }}">
                            <input type="hidden" name="customer_id" value="{{ $yoyaku->customer_id }}">
                            <input type="hidden" name="class_id" value="{{  $yoyaku->schedule->class_id }}">
                            <input type="hidden" name="teacher_id" value="{{  $yoyaku->schedule->teacher_id }}">
                            <input type="hidden" name="schedule_id" value="{{  $yoyaku->schedule_id }}">
                            <input type="hidden" name="payment_plan_id" value="{{ $yoyaku->schedule->class->payment_plan_id }}">
                            <input type="hidden" name="taiken" value="{{  $yoyaku->taiken }}">
                            <input type="hidden" name="start_date" value="{{  $yoyaku->start_date }}">
                            <input type="hidden" name="end_date" value="{{  $yoyaku->end_date }}">
                            <button class="btn btn-sm btn-success btn_submit_form" style="margin:1px;" name="signin_btn" type="button">{{ __('messages.signin') }}</button>
                        </form>
                    @endcan

                    @if($yoyaku->schedule->isPastClassCheckPasses(\Auth::user(),$yoyaku->date))
                        <form class="delete mb-0 cancel-reservation-form"  method="GET" action="{{ route('attendance.cancel') }}">
                            <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                            <input type="hidden" name="cancel_type" value="cancel">
                            <input type="hidden" name="cancel_future_reservations" value="0">
                            <input type="hidden" name="send_email" value="0">
                            <div class="btn-group" style="margin:1px;">
                                <button class="btn btn-sm btn-secondary btn_submit_form" type="button">{{ __('messages.cancel') }}</button>
                                <button type="button" class="btn btn-sm btn-secondary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="fa fa-caret-down"></span>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item cancel_future_reservations btn_submit_form" href="JavaScript:void(0);">{{ __('messages.cancel-future-reservations') }}</a>
                                </div>
                            </div>
                        </form>
                        {{-- 
                            Do Not Remove - as need to restore this funcitonality in future 
                            <form class="delete mb-0 cancel-reservation-form" method="GET" action="{{ route('attendance.cancel') }}">
                            <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                            <input type="hidden" name="cancel_type" value="partial-penalty-cancel">
                            <input type="hidden" name="send_email" value="0">
                            <button class="btn btn-sm btn-warning btn_submit_form"  style="margin:1px;" type="button">{{ __('messages.partialcancel') }}</button>
                        </form> --}}
                        <form class="delete mb-0 cancel-reservation-form" method="GET" action="{{ route('attendance.cancel') }}">
                            <input type="hidden" name="yoyaku_id" value="{{$yoyaku->id}}">
                            <input type="hidden" name="cancel_type" value="full-penalty-cancel">
                            <input type="hidden" name="send_email" value="0">
                            <button class="btn btn-sm btn-danger btn_submit_form" style="margin:1px;" type="button">{{ __('messages.fullcancel') }}</button>
                        </form>
                    @endif

                @elseif($yoyaku->status === 1)
                    <form class="delete mb-0" method="POST" action="{{ route('attendance.undo') }}">
                        @csrf
                        <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                        <button class="btn btn-sm btn-light btn_submit_form" style="margin:1px;" type="button">{{ __('messages.undo-signin') }}</button>
                    </form>
                {{-- @elseif($yoyaku->status === 2)
                    <button class="btn btn-secondary" type="button">{{ __('messages.cancelled') }}</button> --}}
                @endif
            @endcan
        </div>
        <div class="clearfix"></div>
    </th>
</tr>
