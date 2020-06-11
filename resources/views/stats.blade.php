@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.statistics') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <div id="vue-app">
                <app-stats :timezone="timezone"></app-stats>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.app_timezone = '{{ $timezone }}';
    </script>
    <script src="{{ asset('public'.mix('js/page/stats.js')) }}"></script>
@endpush
