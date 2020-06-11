@extends('layouts.app')

@section('content')
    @include('partials.success')
    @include('partials.error')

    <div class="row justify-content-center">
        <div class="col-12 sticky_tabs_container">
            <h1>{{ __('messages.class-details') }}</h1>
            @php
                $nav = Request::query('nav');
                if(!$nav)
                {
                    $nav = "course";
                }
            @endphp
            <ul class="nav nav-tabs">
                    <li class="nav-item"><a class="nav-link {{(isset($nav) && $nav == 'course') ? 'active' : ''}}" data-toggle="tab" href="#course">{{ __('messages.coursedetails')}}</a></li>
            </ul>
        </div>
        <div class="col-lg-12">
            <div class="tab-content">
                <div id="course" class="tab-pane fade {{(isset($nav) && $nav == 'course') ? 'active show' : ''}}">
                    @include('student.course_details_tab')
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
            $('.sticky_tabs_container .nav-link').click(function(){
                nav = $(this).attr('href').replace("#",'');
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
@endpush
