<div class="clearfix mb-2 mt-2">
    <h3 class="float-left">{{ __('messages.papertestresults') }}</h3>
    <a href="{{ route('student.paper_test.create', $schedule->id) }}" class="btn btn-success float-right">{{ __('messages.addpapertest') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover ">
        <thead>
        <tr>
            <th>{{ __('messages.student') }}</th>
            <th>{{ __('messages.test') }}</th>
            <th>{{ __('messages.score') }}</th>
            <th>{{ __('messages.comment') }}</th>
            <th style="width:185px;">{{ __('messages.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @if($schedule->student_paper_tests->count() > 0)
            @foreach($schedule->student_paper_tests as $paper_test)
                <tr>
                    <td><a href="{{ route('student.show', $paper_test->student->id) }}">{{ $paper_test->student->getFullNameAttribute() }}</a></td>
                    <td>{{ $paper_test->paper_test->name }}</td>
                    <td>{{ $paper_test->get_score() }}</td>
                    <td><pre>{!! @$paper_test['comment_'.$comment_lang] !!}</pre></td>
                    <td>
                        @if($paper_test->file_path)
                            <a class="btn btn-primary mb-1" target="_blank" href="{{ \Storage::disk('public')->url($paper_test->file_path) }}">{{ __('messages.view-pdf-file') }}</a>
                        @endif
                        <a class="btn btn-warning" href="{{ route('student.paper_test.edit', $paper_test->id) }}">{{ __('messages.edit') }}</a>
                        <form class="delete" method="POST"
                            action="{{ route('student.paper_test.destroy', ['schedule_id' => $schedule->id, 'student_paper_test_id' => $paper_test->id])}}" >
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
