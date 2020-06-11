@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
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
            <form method="POST" action="{{ route('event.update', $event->id) }}">
                @method('PATCH')
                @csrf
                <h1>{{ __('messages.editevent') }}</h1>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.eventname') }}</label>
                    <div class="col-lg-10">
                        <input name="title" type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ empty(old('title')) ? $event->title : old('title') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.description') }}</label>
                    <div class="col-lg-10">
                        <textarea name="description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" required="">{{ empty(old('description')) ? $event->description : old('description') }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.date') }}</label>
                    <div class="col-lg-10">
                        <input name="date" type="date" class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" value="{{ empty(old('date')) ? $schedule->date : old('date') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.cost') }}</label>
                    <div class="col-lg-10">
                        <input name="cost" type="number" class="form-control{{ $errors->has('cost') ? ' is-invalid' : '' }}" value="{{ empty(old('cost')) ? $event->cost : old('cost') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.size') }}</label>
                    <div class="col-lg-10">
                        <input name="size" type="number" class="form-control{{ $errors->has('size') ? ' is-invalid' : '' }}" value="{{ empty(old('size')) ? $event->size : old('size') }}" required="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-lg-2 col-form-label">{{ __('messages.isallday') }}</label>
                    <div class="col-lg-10">
                        <input name="allday" type="checkbox" id="all_day" class="form-control{{ $errors->has('all_day') ? ' is-invalid' : '' }}" value="allday" {{ $schedule->type == "2" ? 'checked' : '' }}>
                    </div>
                </div>
                <div class="form-group row" style="{{ $schedule->type == 2 ? 'display: none' : '' }}">
                    <label class="col-lg-2 col-form-label">{{ __('messages.starttime') }}</label>
                    <div class="col-lg-10">
                        <input name="start_time" type="time" class="form-control{{ $errors->has('start_time') ? ' is-invalid' : '' }}" value="{{ empty(old('start_time')) ? $schedule->start_time : old('start_time') }}" required="">
                    </div>
                </div>
                <div class="form-group row" style="{{ $schedule->type == 2 ? 'display: none' : '' }}">
                    <label class="col-lg-2 col-form-label">{{ __('messages.endtime') }}</label>
                    <div class="col-lg-10">
                        <input name="end_time" type="time" class="form-control{{ $errors->has('end_time') ? ' is-invalid' : '' }}" value="{{ empty(old('end_time')) ? $schedule->end_time : old('end_time') }}" required="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-10">
                        <input name="edit" type="submit" value="{{ __('messages.edit') }}" class="btn btn-success form-control">
                    </div>
                </div>
        </div>
        </form>
    </div>
    </div>
@endsection
