@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-availbility-selection-calendar-list :permissions="{{ $permissions }}"></app-availbility-selection-calendar-list>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/availability_selection_calendars/index.js')) }}"></script>
@endpush