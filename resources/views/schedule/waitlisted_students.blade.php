@extends('layouts.app')

@section('content')
	<script type="text/javascript">
    window.reservationUrl  =   "{{url('schedule/reservation_by_teacher')}}";
	</script>
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.waitliststudents') }}</h1>
            <div class="alert alert-success" style="display:none;" id="reservation_alert"></div>
    		<div class="alert alert-danger" style="display:none;" id="reservation_alert_danger"></div>
    		<div class="alert alert-warning" style="display:none;" id="reservation_alert_warning"></div>
            <table class="table table-hover">
            		<tr>
		            	<td>{{ __('messages.name')}}</td>
		            	<td>{{ __('messages.classname')}}</td>
		            	<td>{{ __('messages.date')}}</td>
		            	<td></td>
		            </tr>
	            @foreach ($waitlist_students as $student)
		            <tr>
		            	<td>{{$student->lastname_kanji}} {{$student->firstname_kanji}}</td>
		            	<td>{{$student->title}}</td>
		            	<td>{{$student->date}}</td>
		            	<td>
		            		<form class="delete" id="reservation_form" method="GET">
                                @csrf
                                <input type="hidden" name="yoyaku_id" value="{{$student->id}}">
                                <input type="hidden" name="customer_id" value="{{$student->customer_id}}">
                                <input type="hidden" name="schedule_id" value="{{$student->schedule_id}}">
                                <input type="hidden" name="date" value="{{$student->date}}">
                                <button id="reserve_now" type="button" class="btn btn-primary">{{ __('messages.reservenow') }}</button>
                            </form>
		            	</td>
		            </tr>
	            @endforeach
            </table>
        </div>
    </div>
@endsection
@push('scripts')
<script src="{{ asset('public'.mix('js/page/schedule/waitlisted_students.js')) }}"></script>
@endpush
