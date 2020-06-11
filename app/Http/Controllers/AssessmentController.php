<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentRequest;
use App\Assessments;
use App\AssessmentLessons;
use App\AssessmentUsers;
use App\AvailabilitySelectionCalendar;
use App\Courses;
use App\Units;
use App\Lessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class AssessmentController extends Controller
{
    public function index()
    {
        return view('assessment.list', ['assessments' => Assessments::all()]);
    }

    public function create()
    {
        return view('assessment.create', [
            'courses' => Courses::all(),
            'units' => Units::all(),
            'lessons' => Lessons::all()
        ]);
    }

    public function store(AssessmentRequest $request)
    {
        try {
            $assessment = Assessments::create([
                'name' => $request->name,
                'type' => $request->assessment_type,
                'description' => $request->description
            ]);

            if($request->assessment_type == Assessments::AUTOMATIC_TYPE) {
                AssessmentLessons::create([
                    'course_id' => $request->course_id,
                    'unit_id' => $request->unit_id,
                    'lesson_id' => $request->lesson_id,
                    'assessment_id' => $assessment->id,
                    'send_to' => $request->send_to
                ]);
            }

            return redirect('/assessment/list')->with('success', __('messages.add-assessment-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $assessment = Assessments::find($id);
        $availability_selection_calendars = AvailabilitySelectionCalendar::all();
        $add_question_fields_html = View::make('assessment.question.fields', 
            [ 'assessments' => Assessments::all(), 
            'assessment_id' => $assessment->id, 
            'availability_selection_calendars' => $availability_selection_calendars
            ]);

        return view('assessment.details', [
            'assessment' => $assessment,
            'add_question_fields_html' => $add_question_fields_html
        ]);
    }

    public function edit($id)
    {
        return view('assessment.edit', [
            'assessment' => Assessments::find($id),
            'courses' => Courses::all(),
            'units' => Units::all(),
            'lessons' => Lessons::all()
        ]);
    }

    public function update(AssessmentRequest $request, $id)
    {
        try {
            Assessments::find($id)->update(['name' => $request->name, 'description' => $request->description ]);

            return redirect('/assessment/list')->with('success', __('messages.update-assessment-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $assessment = Assessments::find($id);
        $assessment->delete();

        return redirect()->back()->with('success', __('messages.delete-assessment-successfully'));
    }

    public function questions($id, Request $request)
    {
        $assessment = Assessments::find($id);
        return view('assessment.questions', array('assessment' => $assessment));
    }

    public function reorderQuestionsForm($id)
    {
        $assessment = Assessments::find($id);
        return view('assessment.reorder-questions', compact('assessment'));
    }

    public function reorderQuestionsSave($id, Request $request)
    {
        $assessment = Assessments::find($id);

        $question_ids = (array)$request->question_ids;
        foreach($question_ids as $key => $question_id)
        {
            $assessment->assessment_questions()->where('id',$question_id)->update(['position' => $key]);
        }

        $out = array();
        $out['status'] = 1;
        $out['message'] = __('messages.questions-reordered-successfully');
        return $out;
    }

    public function responses($assessment_id)
    {
        $assessment = Assessments::findOrFail($assessment_id);
        $assessment_users = AssessmentUsers::where('assessment_id', $assessment_id)->where('schedule_id', NULL)->get();
        return view('assessment.responses', ['assessment_users' => $assessment_users, 'assessment' => $assessment]);
    }

    public function preview($assessment_id)
    {
        $assessment = Assessments::findOrFail($assessment_id);
        return view('assessment.preview', ['assessment' => $assessment]);
    }
}
