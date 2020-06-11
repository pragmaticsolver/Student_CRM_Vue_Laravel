<div>
    {{ csrf_field() }}
    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
    <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    <div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    <div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
    <div class="text-center" style="max-height:400px;overflow-y:scroll;overflow-x: hidden;">
        <div class="row mt-1">
            <div class="col-sm-4"></div>
            <div class="col-sm-1 pr-0">
                <input id="cancel_multiple_select_all" type="checkbox" class="form-control my-1" style="width:25px;padding-right:0px;">
            </div>
            <div class="col-sm-3 pl-0">
                <input type="text" class="form-control ml-0 my-1" value="Select All" disabled="">
            </div>
        </div>
        @foreach($dates as $date)
            <div class="row mt-1">
                <div class="col-sm-4"></div>
                <div class="col-sm-1 pr-0">
                    <input type="checkbox" name="dates[]" value="{{ $date }}" class="cancel_multiple_checkbox form-control my-1" style="width:25px;padding-right:0px;">
                </div>
                <div class="col-sm-3 pl-0">
                    <input type="text" class="form-control ml-0 my-1" value="{{ $date }}" disabled="">
                </div>
            </div>
        @endforeach
    </div>
</div>
