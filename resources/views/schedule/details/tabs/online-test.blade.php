<h3 class="float-left">{{ __('messages.onlinetestresults') }}</h3>

<div class="table-responsive">
    <table class="table table-bordered table-hover ">
        <thead>
            <tr>
                <th>{{ __('messages.student') }}</th>
                <th>{{ __('messages.test') }}</th>
                <th>{{ __('messages.complete') }}</th>
                <th>{{ __('messages.score') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @if($schedule->student_tests->count() > 0)
                @foreach($schedule->student_tests as $student_test)
                    @php
                        $student = $student_test->student;
                        $test = $student_test->test;
                    @endphp
                    <tr>
                        <td><a href="{{ route('student.show', $student->id) }}">{{ $student->get_kanji_name() }}</a></td>
                        <td><a href="{{ route('test.show', $test->id) }}">{{ $test->name }}</a></td>
                        <td>{!! $student_test->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                        <td>
                            @if($student_test->is_complete())
                                {{ $student_test->score.'/'.$student_test->total_score }}
                            @endif
                        </td>
                        <td>
                            <form class="delete" method="POST" action="{{ route('student-test.destroy', $student_test->id) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit">{{ __('messages.delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
