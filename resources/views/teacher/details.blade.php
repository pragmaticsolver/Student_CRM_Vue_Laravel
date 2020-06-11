@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
		<div class="col-lg-12">
            <h2>{{$teacher->name}}</h2>
            <div class="col-lg-12">
                <div class="row">
                    <label class="col-lg-4">{{ __('messages.name') }}</label>
                    <div class="col-lg-8">{{$teacher->name}}({{$teacher->furigana}})</div>
                </div>
                <div class="row">
                    <label class="col-lg-4">{{ __('messages.nickname') }}</label>
                    <div class="col-lg-8">{{$teacher->nickname}}</div>
                </div>
                <div class="row">
                    <label class="col-lg-4">{{ __('messages.hometown') }}</label>
                    <div class="col-lg-8">{{$teacher->birthplace}}</div>
                </div>
                <div class="row">
                    <label class="col-lg-4">{{ __('messages.birthday') }}</label>
                    <div class="col-lg-8">{{$teacher->birthday}}</div>
                </div>
                <div class="row">
                    <label class="col-lg-4">{{ __('messages.profile') }}</label>
                    <div class="col-lg-8">{{$teacher->profile}}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
