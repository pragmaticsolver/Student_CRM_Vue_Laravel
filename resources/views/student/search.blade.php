@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.search-results') }}</h1>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('partials.success')
            @include('partials.error')
            <table class="table table-hover">
                <tbody>
                    @if(!$students->isEmpty())
                        <tr>
                            <th>{{ __('messages.student-name') }}</th>
                            <th>{{ __('messages.homephone') }}</th>
                            <th>{{ __('messages.cellphone') }}</th>
                            <th>{{ __('messages.email') }}</th>
                        </tr>
                        @foreach($students as $student)
                        <tr>
                            <td><a href="{{ url('/student/'.$student->id) }}">{{$student->fullname}}</a></td>
                            <td>{{$student->home_phone}}</td>
                            <td>{{$student->mobile_phone}}</td>
                            <td>{{ $student->getEmailAddress() }}</td>
                        </tr>
                        @endforeach
                    @else
                    <p>{{ __('messages.no-matching-records-found') }}</p>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection
