@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-edit-calendar cal_id="{{ $availableSelectionCalendar->id }}"></app-edit-calendar>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/availability_selection_calendars/edit_calendar/index.js')) }}"></script>
@endpush