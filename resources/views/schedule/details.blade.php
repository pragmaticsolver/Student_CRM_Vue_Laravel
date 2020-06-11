@extends('layouts.app')

@section('content')
@include('partials.success')
@include('partials.error')

<div class="row justify-content-center">
    <div class="col-12 sticky_tabs_container">
        <h1>{{ __('messages.scheduledetails') }}</h1>
        @php
        $nav = Request::query('nav');
        if(!$nav)
        {
        $nav = 'schedule';
        }
        @endphp
        <ul class="nav nav-tabs">
            <li class="nav-item"><a class="nav-link {{ isset($nav) && $nav == 'schedule' ? 'active' : ''}}" data-toggle="tab" href="#schedule">{{ __('messages.scheduledetails')}}</a></li>
            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'class') ? 'active' : ''}}" data-toggle="tab" href="#class">{{ __('messages.classdetails')}}</a></li>
            @if($schedule->course_schedule)
            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'courseprogress') ? 'active' : ''}}" data-toggle="tab" href="#courseprogress">{{ __('messages.courseprogress')}}</a></li>
            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'onlinetest') ? 'active' : ''}}" data-toggle="tab" href="#onlinetest">{{ __('messages.onlinetestresults')}}</a></li>
            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'assessment') ? 'active' : ''}}" data-toggle="tab" href="#assessment">{{ __('messages.assessmentresults')}}</a></li>
            <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'papertest') ? 'active' : ''}}" data-toggle="tab" href="#papertest">{{ __('messages.papertestresults')}}</a></li>
            @endif
        </ul>
    </div>
    <div class="col-lg-12">
        <div class="tab-content">
            <div id="schedule" class="tab-pane fade {{ isset($nav) && $nav == 'schedule' ? 'active show' : ''}}">
                @include('schedule.details.tabs.schedule')
            </div>
            <div id="class" class="tab-pane fade {{(isset($nav)  && $nav == 'class')? 'active show' : ''}}">
                @include('schedule.details.tabs.class')
            </div>
            <div id="courseprogress" class="tab-pane fade {{(isset($nav) && $nav == 'courseprogress') ? 'active show' : ''}}">
                @include('schedule.details.tabs.course-progress')
            </div>
            <div id="onlinetest" class="tab-pane fade {{(isset($nav) && $nav == 'onlinetest') ? 'active show' : ''}}">
                @include('schedule.details.tabs.online-test')
            </div>
            <div id="papertest" class="tab-pane fade {{(isset($nav) && $nav == 'papertest') ? 'active show' : ''}}">
                @include('schedule.details.tabs.paper-test')
            </div>
            <div id="assessment" class="tab-pane fade {{(isset($nav) && $nav == 'assessment') ? 'active show' : ''}}">
                @include('schedule.details.tabs.assessment')
            </div>
        </div>
    </div>
</div>
@include('course.unit.lesson.file-name')

@endsection

@push('scripts')
<script src="{{ asset('public'.mix('js/page/filename.js')) }}"></script>

<script>
    
    window.addEventListener('DOMContentLoaded', function() {
        (function($) {
            // Update nav paramter in url to display same tab that was last opened.
            $('.sticky_tabs_container .nav-link').click(function() {
                nav = $(this).attr('href').replace("#", '');
                url = new URL(window.location.href);
                var query_string = url.search;
                var search_params = new URLSearchParams(query_string);
                search_params.delete('nav');
                search_params.append('nav', nav);
                url.search = search_params.toString();
                var new_url = url.toString();
                history.replaceState(null, null, new_url);
            });
        })(jQuery);
    });
</script>
<script src="{{ asset('public'.mix('js/page/schedule/details/tabs/comments.js')) }}"></script>

@endpush