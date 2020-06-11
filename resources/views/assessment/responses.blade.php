@extends('layouts.app')


@section('content')
    @include('partials.success')
    @include('partials.error')

    <div class="row justify-content-center">
        <div class="table-responsive">
            <h2>{{ __('messages.assessment-responses') }}: {{ $assessment->name }}</h2>
            <table class="table table-bordered table-hover ">
                <thead>
                <tr>
                    <th>{{ __('messages.assessment-by') }}</th>
                    <th>{{ __('messages.complete') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($assessment_users as $assessment_user)
                        @php
                            $assessment = $assessment_user->assessment;
                            $user = $assessment_user->user;
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('student.show', $user->student->id) }}">{{ $user->student->fullname }}</a>
                            </td>
                            <td>{!! $assessment_user->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                            <td>
                                @if($assessment_user->is_complete())
                                    <a class="btn btn-success" href="{{ route('assessment_user.show', $assessment_user->id ) }}">
                                        {{ __('messages.seedetails') }}
                                    </a>
                                @endif
                                @can('edit-assessment-response')
                                    <a class="btn btn-warning" href="{{ route('user.assessment.take', [$assessment_user->id, 'return_url' => route('assessment.responses', $assessment->id) ] ) }}">
                                        {{ __('messages.edit') }}
                                    </a>
                                @endcan
                                <form class="delete mb-0" method="POST" action="{{ route('assessment_user.destroy', $assessment_user->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection