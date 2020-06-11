@extends('layouts.app')

@section('content')
    @include('partials.success')
    @include('partials.error')

    <div class="row justify-content-center">
        <div class="col-12 sticky_tabs_container">
            @php
                $nav = Request::query('nav');
                if(!$nav)
                {
                    $nav = "schedule";
                }
            @endphp
            <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'schedule') ? 'active' : ''}}" data-toggle="tab" href="#schedule">{{ __('messages.scheduledetails')}}</a></li>
            </ul>
        </div>
        <div class="col-lg-12">
            <div class="tab-content">
                <div id="schedule" class="tab-pane fade {{(isset($nav) && $nav == 'schedule') ? 'active show' : ''}}">
                    <div class="card card-body">
                        @include('schedule.details.tabs.comments')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('course.unit.lesson.file-name')
@endsection

@push('scripts')
<script src="{{ asset('public'.mix('js/page/filename.js')) }}"></script>
<script src="{{ asset('public'.mix('js/page/schedule/details/tabs/comments.js')) }}"></script>
@endpush
