@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.onlinetestlist') }}</h1>
            <table class="table table-hover data-table order-column">
                @if($student_tests->count() > 0)
                    <thead>
                    <tr>
                        <th>{{ __('messages.test') }}</th>
                        <th>{{ __('messages.numberofquestions') }}</th>
                        <th>{{ __('messages.class') }}</th>
                        <th>{{ __('messages.classdate') }}</th>
                        <th>{{ __('messages.complete') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($student_tests as $student_test)
                        <tr>
                            <td>{{ $student_test->test->name }}</td>
                            <td>{{ $student_test->test->questions->count() }}</td>
                            <td>{{ $student_test->schedule->class->title }}</td>
                            <td>{{ $student_test->schedule->get_date() }}</td>
                            <td>{!! $student_test->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                            <td>
                                @if(!$student_test->status)
                                    <a href="{{ route('student.online_test.take', $student_test->id) }}" class="btn btn-success">{{ __('messages.test') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
@endsection
