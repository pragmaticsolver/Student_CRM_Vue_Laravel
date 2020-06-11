<tr>
    <th class="{{  $yoyaku->taiken == 1 ? 'bg-danger text-white' : '' }} p-2">
        <div class="pull-left" style="width:50%">
            <span class="align-middle d-inline-block">{{  $yoyaku->student->getFullNameAttribute() }}</span>
            @can('view-student-tags')
                @php $enable_edit = \Auth::user()->hasPermissionTo('edit-student-tags') ? 'true' : 'false'; @endphp
                <app-student-tags class="attach-vue align-middle d-inline-block"
                    :student_id="{{ $yoyaku->student->id }}"
                    :student_tags="{{ json_encode($yoyaku->student->getTags()) }}"
                    :enable_edit="{{ $enable_edit }}"
                ></app-student-tags>
            @endcan
        </div>
        @can('control-attendance')
            <div class="pull-left" style="width:50%">
                <form id="waitlist-delete" class="mb-0 d-inline-block">
                    <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                    <button class="btn btn-sm btn-danger" type="button">{{ __('messages.delete') }}</button>
                </form>
                @if($yoyaku->schedule->isPastClassCheckPasses(\Auth::user(),$yoyaku->date))
                    <form id="reservation_form" method="GET" class="mb-0 d-inline-block">
                        <input type="hidden" name="yoyaku_id" value="{{ $yoyaku->id }}">
                        <input type="hidden" name="customer_id" value="{{ $yoyaku->customer_id }}">
                        <input type="hidden" name="schedule_id" value="{{ $yoyaku->schedule_id }}">
                        <input type="hidden" name="date" value="{{ $yoyaku->date }}">
                        <input type="hidden" name="taiken" value="{{ $yoyaku->taiken }}">
                        <button id="reserve_now" type="button" class="btn btn-sm btn-primary">{{ __('messages.reservenow') }}</button>
                    </form>
                @endif
            </div>
        @endcan
        <div class="clearfix"></div>
    </th>
</tr>