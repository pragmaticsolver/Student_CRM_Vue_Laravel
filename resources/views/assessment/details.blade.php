@extends('layouts.app')

@section('content')
    <div class="justify-content-center">
        @include('partials.success')
        @include('partials.error')
        <h1>{{ __('messages.assessmentdetails') }}</h1>
        <table class="table table-hover table-striped">
            <tbody>
                <tr>
                    <th width="16%">{{ __('messages.name') }}</th>
                    <td>{{ $assessment->name }}</td>
                </tr>
                @if($assessment->description)
                    <tr>
                        <th>{{ __('messages.description') }}</th>
                        <td>{{ $assessment->description }}</td>
                    </tr>
                @endif
                <tr>
                    <th>{{ __('messages.type') }}</th>
                    <td>{{ $assessment->type }}</td>
                </tr>
                @if($assessment->type == 'automatic')
                    @php
                        $assessment_lesson = $assessment->assessment_lesson;
                    @endphp
                    <tr>
                        <th>{{ __('messages.course') }}</th>
                        <td><a href="{{ url('/course/details/'.$assessment_lesson->course->id) }}">{{ $assessment_lesson->course->title }}</a></td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.unit') }}</th>
                        <td><a href="{{ url('/unit/details/'.$assessment_lesson->unit->id) }}">{{ $assessment_lesson->unit->name }}</a></td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.lesson') }}</th>
                        <td><a href="{{ url('/lesson/details/'.$assessment_lesson->lesson->id) }}">{{ $assessment_lesson->lesson->title }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('messages.sendto') }}</th>
                        <td>{{ $assessment_lesson->send_to }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="clearfix">
            <h1 class="float-left">{{ __('messages.questions') }}</h1>
            <button class="btn btn-success float-right btn_add_question">{{ __('messages.addquestion') }}</button>
            <button class="btn btn-secondary float-right btn_reorder_questions mr-2" data-id="{{ $assessment->id }}">{{ __('messages.reorder-questions') }}</button>
        </div>

        <div style="position:relative;width:100%;">
            <div id="questions_section_preloader" class="text-center;" style="width: 100%;height: 100%;position: absolute;top:0;
            left: 0;z-index:10;background:#58608852;">
                <div class="fa fa-spinner fa-spin" style="font-size:30px;position: relative;display: inline-block;position: absolute;left: 50%;top: 50%;
                text-align:center;"></div>
            </div>
            <div id="assessment_questions_section">

            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal inmodal" id="AddQuestionModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.addquestion') }}</h4>
                </div>
                <div class="modal-body" style="max-height: 450px;overflow-y: scroll;">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="add_question_form">
                                @csrf
                                <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="add_question_submit_btn">
                                    {{ __('messages.add') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#AddQuestionModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="EditQuestionModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.editquestion') }}</h4>
                </div>
                <div class="modal-body" style="max-height: 450px;overflow-y: scroll;">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="edit_question_form" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="edit_question_sumbit_btn">
                                    {{ __('messages.edit') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#EditQuestionModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal inmodal" id="ReorderQuestionsModal" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <div class="row mr-1">
                        <div class="col-12">
                            <form id="reorder_questions_form" enctype="multipart/form-data">
                            <span class="form-fields"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                            <div class="col">
                                <button type="submit" class="btn btn-success pull-right mx-1" name="add" id="reoder_lessons_sumbit_btn">
                                    {{ __('messages.save') }}
                                    <span class="form_spinner">&nbsp<span class="fa fa-spinner fa-spin"></span></span>
                                </button>
                                <button type="button" class="btn btn-secondary pull-right mx-1" onclick="$('#ReorderQuestionsModal').modal('hide');">{{ __('messages.cancel') }}</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    get_questions_url = "{{ route('assessment.questions', $assessment->id) }}";
    add_question_fields_html = `<?php echo $add_question_fields_html ?>`;
    store_assessment_question_url = "{{ route('assessment-question.store') }}";
    assessment_question_edit_fields_url = "{{ route('assessment-question.edit_fields', '') }}";
    assessment_question_update_url = "{{ route('assessment-question.update', '') }}";
    assessment_reorder_questions_save_url = "{{ route('assessment.reorder_questions.save', '') }}";
    assessment_question_destroy_url = "{{ route('assessment-question.destroy', '') }}";
    assessment_reorder_questions_form_url = "{{ route('assessment.reorder_questions.form', '') }}";
    csrf_token = "{{ csrf_token() }}";
</script>
<script src="{{ asset('public'.mix('js/assessment/details.js')) }}"></script>
{{-- Reordering doesnt work without this script on iphone --}}
<script defer src="{{ asset('public'.mix('js/vendor/jquery.ui.touch-punch.js')) }}"></script>
@endpush
