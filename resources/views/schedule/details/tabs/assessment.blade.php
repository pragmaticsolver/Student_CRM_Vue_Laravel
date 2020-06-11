<div class="clearfix mb-2 mt-2">
    <h3 class="float-left">{{ __('messages.assessmentresults') }}</h3>
    <a href="{{ route('assessment_user.create', $schedule->id) }}" class="btn btn-success float-right">{{ __('messages.addassessment') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover ">
        <thead>
        <tr>
            <th>{{ __('messages.assessment-by') }}</th>
            <th>{{ __('messages.assessment-for') }}</th>
            <th>{{ __('messages.assessment') }}</th>
            <th>{{ __('messages.complete') }}</th>
            <th>{{ __('messages.type') }}</th>
            <th>{{ __('messages.actions') }}</th>
        </tr>
        </thead>
        <tbody>
        @if($schedule->assessment_users->count() > 0)
            @foreach($schedule->assessment_users as $assessment_user)
                @php
                    $assessment = $assessment_user->assessment;
                    $user = $assessment_user->user;
                @endphp
                <tr>
                    <td>
                        @if($user->student)
                            <a href="{{ route('student.show', $user->student->id) }}">{{ $user->student->get_kanji_name() }}</a>
                            <div>{{ __('messages.student') }}</div>
                        @elseif($user->teacher)
                            <a href="{{ route('teacher.show', $user->teacher->id) }}">{{ $user->teacher->nickname }}</a>
                            <div>{{ __('messages.teacher') }}</div>
                        @endif
                    </td>
                    <td>
                        @if($assessment_user->assessment_for_student)
                            <a href="{{ route('student.show', $assessment_user->assessment_for_student->id) }}">{{ $assessment_user->assessment_for_student->get_kanji_name() }}</a>
                        @endif
                    </td>
                    <td><a href="{{ route('assessment.show', $assessment->id) }}">{{ $assessment->name }}</a></td>
                    <td>{!! $assessment_user->is_complete() ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>' !!}</td>
                    <td>{{ $assessment->type }}</td>
                    <td>
                        @if($assessment_user->is_complete())
                            <a class="btn btn-success" href="{{ route('assessment_user.show', $assessment_user->id ) }}">
                                {{ __('messages.seedetails') }}
                            </a>
                        @endif
                        @can('edit-assessment-response')
                            <a class="btn btn-warning" href="{{ route('user.assessment.take', [$assessment_user->id, 'return_url' => route('schedule.show', [ $schedule->id , 'nav' => 'assessment']) ] ) }}">
                                {{ __('messages.edit') }}
                            </a>
                        @endcan
                        <form class="delete" method="POST" action="{{ route('assessment_user.destroy', $assessment_user->id) }}">
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
