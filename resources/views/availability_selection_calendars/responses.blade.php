@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2" id="vue-app">
            <app-availbility-selection-responses cal_id="{{ $availabilitySelectionCalendar->id }}"></app-availbility-selection-responses>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('public'.mix('js/page/availability_selection_calendars/responses.js')) }}"></script>
@endpush