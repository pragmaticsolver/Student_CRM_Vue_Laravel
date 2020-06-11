@extends('layouts.app')

@section('content')
	<div class="row justify-content-center">
		<div class="col-12">
            <h1>{{ __('messages.schedule-settings') }}</h1>
			@if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
	        @if($errors->any())
	            <div class="alert alert-danger">
	              <ul>
	                  @foreach($errors->all() as $error)
	                      <li>{{ $error }}</li>
	                  @endforeach
	              </ul>
	            </div><br/>
            @endif
            @include('partials.error')
			<form method="POST" action="{{ route('schedule-settings.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.working-days') }}:</label>
                    <div class="col-lg-10 pt-2">
                        <label for="wd_sun" class="mr-3"><input type="checkbox" name="working_days[]" value="sun" id="wd_sun" {{ in_array('sun',$working_days) ? 'checked' : '' }} >Sunday</label>
                        <label for="wd_mon" class="mr-3"><input type="checkbox" name="working_days[]" value="mon" id="wd_mon" {{ in_array('mon',$working_days) ? 'checked' : '' }} >Monday</label>
                        <label for="wd_tue" class="mr-3"><input type="checkbox" name="working_days[]" value="tue" id="wd_tue" {{ in_array('tue',$working_days) ? 'checked' : '' }} >Tuesday</label>
                        <label for="wd_wed" class="mr-3"><input type="checkbox" name="working_days[]" value="wed" id="wd_wed" {{ in_array('wed',$working_days) ? 'checked' : '' }} >Wednesday</label>
                        <label for="wd_thu" class="mr-3"><input type="checkbox" name="working_days[]" value="thu" id="wd_thu" {{ in_array('thu',$working_days) ? 'checked' : '' }} >Thursday</label>
                        <label for="wd_fri" class="mr-3"><input type="checkbox" name="working_days[]" value="fri" id="wd_fri" {{ in_array('fri',$working_days) ? 'checked' : '' }} >Friday</label>
                        <label for="wd_sat" class="mr-3"><input type="checkbox" name="working_days[]" value="sat" id="wd_sat" {{ in_array('sat',$working_days) ? 'checked' : '' }} >Saturday</label>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.first-day-of-week') }}:</label>
                    <div class="col-lg-10 pt-2">
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="radio_sun"
                                value="sun"
                                name="week_start_day"
                                {{ $week_start_day == 'sun' ? 'checked': '' }}
                            >
                            <label class="form-check-label" for="radio_sun">Sunday</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input
                                class="form-check-input"
                                type="radio"
                                id="radio_mon"
                                value="mon"
                                name="week_start_day"
                                {{ $week_start_day == 'mon' ? 'checked': '' }}
                            >
                            <label class="form-check-label" for="radio_mon">Monday</label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.default-show-calendar') }}:</label>
                    <div class="col-lg-10">
                        <div class="row">
                            <div class="col-sm-6">
                                <select name="default_show_calendar[]" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" required>
                                    <option value="">{{ __('messages.please-select-time') }}</option>
                                    @for($hour = 0; $hour < 24; $hour++)
                                        @foreach([0,30] as $minute)
                                            @php $time = sprintf('%02d', $hour).':'.sprintf('%02d', $minute); @endphp
                                            <option value="{{ $time }}" @if(isset($default_show_calendar[0]) && $time == $default_show_calendar[0]) selected="selected" @endif>{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <select name="default_show_calendar[]" class="form-control{{ $errors->has('role') ? ' is-invalid' : '' }}" required>
                                    <option value="">{{ __('messages.please-select-time') }}</option>
                                    @for($hour = 0; $hour < 24; $hour++)
                                        @foreach([0,30] as $minute)
                                            @php $time = sprintf('%02d', $hour).':'.sprintf('%02d', $minute); @endphp
                                            <option value="{{ $time }}" @if(isset($default_show_calendar[1]) && $time == $default_show_calendar[1]) selected="selected" @endif>{{ $time }}</option>
                                        @endforeach
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.save') }}" class="btn btn-success form-control">
                    </div>
                </div>
            </form>
		</div>
	</div>
@endsection