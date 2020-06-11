@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.papertestlist') }}</h1>
            <table class="table table-hover data-table order-column">
                @if($paper_tests->count() > 0)
                    <thead>
                    <tr>
                        <th>{{ __('messages.test') }}</th>
                        <th>{{ __('messages.class') }}</th>
                        <th>{{ __('messages.classdate') }}</th>
                        <th>{{ __('messages.testdate') }}</th>
                        <th>{{ __('messages.score') }}</th>
                        <th>{{ __('messages.comment') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($paper_tests as $paper_test)
                        @php
                            $schedule = $paper_test->schedule;
                            $lang = $paper_test->student->user->lang ? $paper_test->student->user->lang : 'en';
                            $comment_field = 'comment_'.$lang;
                        @endphp
                        <tr>
                            <td>{{ $paper_test->paper_test->name }}</td>
                            <td>{{ $schedule->class->title }}</td>
                            <td>{{ $schedule->get_date() }}</td>
                            <td>{{ $paper_test->date }}</td>
                            <th>{{ $paper_test->get_score() }}</th>
                            <td><pre>{!! $paper_test->$comment_field !!}</pre></td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
@endsection
