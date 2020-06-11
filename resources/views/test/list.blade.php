@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <div class="col-12 mt-2">
            <div class="row">
                <div class="col-6">
                    <h1>{{ __('messages.testlist') }}</h1>
                </div>
                <div class="col-6 text-right">
                    <a class="btn btn-success" href="{{ route('test.create') }}">{{ __('messages.addtest') }}</a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <table class="table table-hover data-table order-column">
        	@if($tests->count() > 0)
                <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.course') }}</th>
                        <th>{{ __('messages.unit') }}</th>
                        <th>{{ __('messages.lesson') }}</th>
                        <th>{{ __('messages.numberofquestions') }}</th>
                        <th>{{ __('messages.addquestion') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                </thead>
                <tbody>
        		@foreach($tests as $test)
        			<tr>
                        <td><a href="{{ url('/test/details/'.$test->id) }}">{{ $test->name }}</a></td>
                        <td><a href="{{ url('/course/details/'.$test->course->id) }}">{{ $test->course->title }}</a></td>
                        <td><a href="{{ url('/unit/details/'.$test->unit->id) }}">{{ $test->unit->name }}</a></td>
                        <td><a href="{{ url('/lesson/details/'.$test->lesson->id) }}">{{ $test->lesson->title }}</a></td>
                        <td>{{ $test->questions->count() }}</td>
                        <td><a href="{{ url('/question/add?test_id='.$test->id) }}" class="btn btn-success">{{ __('messages.addquestion') }}</a></td>
                        <td><a href="{{ url('/test/edit/'.$test->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('test.destroy', $test->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
        		@endforeach
                </tbody>
        	@endif
            </table>
            <hr>
            <h1>{{ __('messages.papertestlist') }}</h1>
            <table class="table table-hover data-table order-column">
                @if($paper_tests->count() > 0)
                    <thead>
                    <tr>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.course') }}</th>
                        <th>{{ __('messages.unit') }}</th>
                        <th>{{ __('messages.lesson') }}</th>
                        <th>{{ __('messages.totalscore') }}</th>
                        <th>{{ __('messages.edit') }}</th>
                        <th>{{ __('messages.delete') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($paper_tests as $paper_test)
                        <tr>
                            <td>{{ $paper_test->name }}</td>
                            <td><a href="{{ url('/course/details/'.$paper_test->course->id) }}">{{ $paper_test->course->title }}</a></td>
                            <td><a href="{{ url('/unit/details/'.$paper_test->unit->id) }}">{{ $paper_test->unit->name }}</a></td>
                            <td><a href="{{ url('/lesson/details/'.$paper_test->lesson->id) }}">{{ $paper_test->lesson->title }}</a></td>
                            <td>{{ $paper_test->total_score }}</td>
                            <td><a href="{{ url('/paper-test/edit/'.$paper_test->id) }}" class="btn btn-success">{{ __('messages.edit') }}</a></td>
                            <td>
                                <form class="delete" method="POST" action="{{ route('paper_test.destroy', $paper_test->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @endif
            </table>
        </div>
    </div>
@endsection
