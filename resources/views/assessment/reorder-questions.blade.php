@csrf
<input type="hidden" name="assessment_id" value="{{ $assessment->id }}">
<ol class="reorder-questions-section" style="max-height:400px;overflow-y:scroll;">
    @foreach($assessment->assessment_questions as $assessment_question)
        <li class="m-1">
            <a class="btn btn-secondary btn-block text-left text-white">
                {{ $assessment_question->name }}
            </a>
            <input type="hidden" name="question_ids[]" value="{{ $assessment_question->id }}">
        </li>
    @endforeach
</ol>
