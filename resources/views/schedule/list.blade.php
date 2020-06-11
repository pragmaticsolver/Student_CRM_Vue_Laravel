@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <h1>{{ __('messages.reservationlist') }}</h1>
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}  
                </div><br/>
            @endif
            <table class="table table-hover">
            		<tr>
		            	<td>{{ __('messages.classteacher')}}</td>
		            	<td>{{ __('messages.classname')}}</td>
		            	<td>{{ __('messages.date')}}</td>
		            	<td></td>
		            </tr>
	            @foreach ($yoyakus as $yoyaku)
		            <tr>            	
		            	<td>{{$yoyaku->schedule->teacher->nickname }}</td>
		            	<td>{{$yoyaku->schedule->class->title }}</td>
		            	<td>{{ $yoyaku->date.' '.$yoyaku->schedule->start_time.' - '.$yoyaku->schedule->end_time }}</td>
		            </tr>
	            @endforeach
            </table>
        </div>
    </div>
@endsection
