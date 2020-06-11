<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AssessmentQuestionRequest;
use App\Assessments;
use App\AssessmentQuestions;
use App\AvailabilitySelectionCalendar;

class AssessmentQuestionController extends Controller
{
    public function create(Request $request)
    {
        return view('assessment.question.create', [
            'assessments' => Assessments::all(),
            'assessment_id' => $request->assessment_id,
            'availability_selection_calendars' => AvailabilitySelectionCalendar::all(),
        ]);
    }

    public function store(AssessmentQuestionRequest $request)
    {
        try {

            $position = NULL;
            $rec = AssessmentQuestions::where('assessment_id',$request->assessment_id)->orderBy('position','DESC')->first();
            if($rec && $rec->position != NULL)
            {
                $position = $rec->position + 1;
            }

            AssessmentQuestions::create([
                'name' => $request->name,
                'type' => $request->assessment_question_type,
                'assessment_id' => $request->assessment_id,
                'option_values' => $request->assessment_question_type == 'option' ? json_encode($request->options) : '',
                'position' => $position,
                'availability_selection_calendar_id' => $request->assessment_question_type == 'availability-selection-calender' ? $request->availability_selection_calendar_id: NULL,
                'is_required' => $request->is_required ? 1 : 0
            ]);

            $message = __('messages.add-assessment-question-successfully');
            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = $message;
                return $out;
            }

            return redirect(route('assessment.show', $request->assessment_id))->with('success', $message);
        } catch (\Exception $e) {

            $message = $e->getMessage();
            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = $message;
                return $out;
            }

            return redirect()->back()->with('error', $message);
        }
    }

    public function edit($id)
    {
        $assessment_question = AssessmentQuestions::find($id);
        return view('assessment.question.edit', [
            'assessment_question' => $assessment_question,
            'assessment_id' => $assessment_question->assessment_id,
            'assessments' => Assessments::all(),
            'availability_selection_calendars' => AvailabilitySelectionCalendar::all(),
        ]);
    }

    public function edit_fields($id)
    {
        $assessment_question = AssessmentQuestions::find($id);
        return view('assessment.question.fields', [
            'assessment_question' => $assessment_question,
            'assessment_id' => $assessment_question->assessment_id,
            'assessments' => Assessments::all(),
            'availability_selection_calendars' => AvailabilitySelectionCalendar::all()
        ]);
    }

    public function update(AssessmentQuestionRequest $request, $id)
    {
        try {
            $type = $request->assessment_question_type;
            AssessmentQuestions::find($id)->update([
                'name' => $request->name,
                'type' => $type,
                'assessment_id' => $request->assessment_id,
                'option_values' => $type == 'option' ? json_encode($request->options) : '',
                'availability_selection_calendar_id' => $type == 'availability-selection-calender' ? $request->availability_selection_calendar_id: NULL,
                'is_required' => $request->is_required ? 1 : 0
            ]);


            $message = __('messages.update-assessment-question-successfully');
            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = $message;
                return $out;
            }

            return redirect(route('assessment.show', $request->assessment_id))->with('success', $message);
        } catch (\Exception $e) {

            $message = $e->getMessage();
            if($request->ajax())
            {
                $out['status'] = 0;
                $out['message'] = $message;
                return $out;
            }

            return redirect()->back()->with('error', $message);
        }
    }

    public function destroy($id, Request $request)
    {
        $assessment_question = AssessmentQuestions::find($id);
        $assessment = $assessment_question->assessment;
        $assessment_question->delete();

        $message = __('messages.delete-assessment-question-successfully');
        if($request->ajax())
        {
            $out['status'] = 1;
            $out['message'] = $message;
            return $out;
        }

        return redirect(route('assessment.show', $assessment->id))->with('success', $message);
    }
}
