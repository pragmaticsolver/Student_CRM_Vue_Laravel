@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.assessments') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif
            @include('partials.error')
            <table class="table table-hover">
                <tbody>

                    @if(!$assessmentUsers->isEmpty())
                        <tr>
                            <th>{{ __('messages.class') }}</th>
                            <th>{{ __('messages.assessment') }}</th>
                            <th>{{ __('messages.type') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                        @foreach($assessmentUsers as $assessment_user)
                            @php
                                $assessment = $assessment_user->assessment;
                                $user = $assessment_user->user;
                            @endphp
                            <tr>
                                <td>{{ $assessment_user->schedule->class->title }}</td>
                                <td>{{ $assessment->name }}</td>
                                <td>{{ $assessment->type }}</td>
                                <td>
                                    <a class="btn btn-primary" href="{{ route('student.view_assessment', $assessment_user->id ) }}">
                                        {{ __('messages.view-details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <p class="text-center">{{ __('messages.no-records-found') }}</p>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
