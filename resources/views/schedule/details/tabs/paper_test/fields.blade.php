<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.class') }}</label>
    <div class="col-lg-4">
        <input type="text" value="{{ $schedule->class->title }}" class="form-control" readonly>
    </div>

    <label class="col-lg-2 col-form-label">{{ __('messages.classdate') }}</label>
    <div class="col-lg-4">
        <input type="text" value="{{ $schedule->get_date() }}" class="form-control" readonly>
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.test') }}</label>
    <div class="col-lg-4">
        <select id="paper_test_id" name="paper_test_id" class="form-control{{ $errors->has('paper_test_id') ? ' is-invalid' : '' }}" required>
            <option value="">{{ __('messages.selecttest') }}</option>
            @php $_val = isset($studentPaperTest) ? old('paper_test_id',$studentPaperTest->paper_test_id) : old('paper_test_id'); @endphp
            @if($paper_tests->count() > 0)
                @foreach($paper_tests as $paper_test)
                    <option value="{{$paper_test->id}}"
                        data-total_score="{{ $paper_test->total_score }}"
                        @if($paper_test->id == $_val) selected @endif
                    >{{ $paper_test->name }}</option>
                @endforeach
            @endif
        </select>
    </div>

    <label class="col-lg-2 col-form-label">{{ __('messages.student') }}</label>
    <div class="col-lg-4">
        <select name="student_id" class="form-control{{ $errors->has('student_id') ? ' is-invalid' : '' }}" required>
            <option value="">{{ __('messages.selectstudent') }}</option>
            @php $_val = isset($studentPaperTest) ? old('student_id',$studentPaperTest->student_id) : old('student_id'); @endphp
            @if($students->count() > 0)
                @foreach($students as $student)
                    <option value="{{$student->id}}" @if($student->id == $_val) selected @endif>{{ $student->getFullNameAttribute() }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>
<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.totalscore') }}</label>
    <div class="col-lg-4">
        @php $_val = isset($studentPaperTest) ? old('total_score',$studentPaperTest->total_score) : old('total_score'); @endphp
        <input
            type="number" id="total_score" name="total_score" min="0" step="0.01" value="{{ $_val }}"
            class="form-control{{ $errors->has('total_score') ? ' is-invalid' : '' }}" required
        >
    </div>

    <label class="col-lg-2 col-form-label">{{ __('messages.score') }}</label>
    <div class="col-lg-4">
        @php $_val = isset($studentPaperTest) ? old('score',$studentPaperTest->score) : old('score'); @endphp
        <input
            type="number" name="score" min="0" step="0.01" value="{{ $_val }}"
            class="form-control{{ $errors->has('score') ? ' is-invalid' : '' }}" required
        >
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.testdate') }}</label>
    <div class="col-lg-4">
        @php $_val = isset($studentPaperTest) ? old('date',$studentPaperTest->date) : old('date', \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d')); @endphp
        <input
            type="date" value="{{ $_val }}" name="date"
            class="form-control{{ $errors->has('date') ? ' is-invalid' : '' }}" required
        >
    </div>

    <label class="col-lg-2 col-form-label">{{ __('messages.commenttemplate') }}</label>
    <div class="col-lg-4">
        <select name="comment_template_id" class="form-control">
            <option value="">{{ __('messages.selectcommenttemplate') }}</option>
            @php $_val = isset($studentPaperTest) ? old('comment_template_id',$studentPaperTest->comment_template_id) : old('comment_template_id'); @endphp
            @if($comment_templates->count() > 0)
                @foreach($comment_templates as $comment_template)
                    <option value="{{$comment_template->id}}" @if($comment_template->id == $_val) selected @endif>{{ $comment_template->name }}</option>
                @endforeach
            @endif
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-lg-2 col-form-label">{{ __('messages.comment') }}</label>
    <div class="col-lg-10">
        @php $_val = isset($studentPaperTest) ? old('comment',$studentPaperTest->comment) : old('comment'); @endphp
        <textarea name="comment" class="form-control" rows="7">{{ $_val }}</textarea>
    </div>
</div>

<div class="form-group row">
<label class="col-lg-2 col-form-label">{{ __('messages.pdf-file') }}</label>
    <div class="col-lg-10">
        <div class="dropzone" id="pdf_file"></div>
    </div>
    <input type="hidden" id="temp_file_path" name="temp_file_path" value="">
    <input type="hidden" id="remove_old_file" name="remove_old_file" value="0">

    @php $_val = isset($studentPaperTest) && $studentPaperTest->file_path ? $studentPaperTest->dropzonePDFFile() : ''; @endphp
    <input type="hidden" id="uploaded_pdf_file" value="{{ $_val }}">
</div>

@push('scripts')
<script>
    upload_temp_file_url = "{{ route('upload_temp_file') }}";
</script>
<script src="{{ asset('public'.mix('js/page/schedule/details/tabs/paper_test/fields.js')) }}"></script>
@endpush
