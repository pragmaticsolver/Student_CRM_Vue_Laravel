@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12">
            <h1>{{ __('messages.assessmentlist') }}</h1>
            <table class="table table-hover data-table order-column">
                @if($assessment_users->count() > 0)
                    <thead>
                    <tr>
                        <th>{{ __('messages.assessment') }}</th>
                        <th>{{ __('messages.class') }}</th>
                        <th>{{ __('messages.classdate') }}</th>
                        <th>{{ __('messages.complete') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($assessment_users as $assessment_user)
                        <tr>
                            <td>{{ $assessment_user->assessment->name }}</td>
                            <td>{{ $assessment_user->schedule->class->title }}</td>
                            <td>{{ $assessment_user->schedule->get_date() }}</td>
                            <td>{!! $assessment_user->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                            <td>
                                @if(!$assessment_user->is_complete())
                                    <a href="{{ route('user.assessment.take', $assessment_user->id) }}" class="btn btn-success">{{ __('messages.takeassessment') }}</a>
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
