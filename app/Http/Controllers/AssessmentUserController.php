<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssessmentUserRequest;
use Illuminate\Http\Request;
use App\Assessments;
use App\Schedules;
use App\Students;
use App\AssessmentUsers;
use App\AssessmentUserQuestions;
use App\AssessmentUserTimeslot;
use App\Helpers\NotificationHelper;
use App\Settings;

class AssessmentUserController extends Controller
{
    public function create($schedule_id)
    {
        $schedule = Schedules::find($schedule_id);
        $students = $schedule->get_students();

        return view('assessment.assessment_user.create', [
            'assessments' => Assessments::get_all_manual(),
            'schedule' => $schedule,
            'students' => $students
        ]);
    }

    public function store(AssessmentUserRequest $request, $schedule_id)
    {
        try {
            if($request->send_to == 'student')
            {
                foreach($request->students as $student_id)
                {
                    $student = Students::find($student_id);
                    $user = $student->user;

                    $assessment_user = AssessmentUsers::create([
                        'user_id' => $user->id,
                        'schedule_id' => $schedule_id,
                        'assessment_id' => $request->assessment_id,
                        'complete' => AssessmentUsers::INCOMPLETE_STATUS
                    ]);

                    NotificationHelper::sendManualAssessmentNotification($assessment_user);
                }
            }
            else
            {
                $schedule = Schedules::find($schedule_id);
                $user = $schedule->teacher->user;
                foreach($request->students as $student_id)
                {
                    $assessment_user = AssessmentUsers::create([
                        'user_id' => $user->id,
                        'schedule_id' => $schedule->id,
                        'assessment_id' => $request->assessment_id,
                        'complete' => AssessmentUsers::INCOMPLETE_STATUS,
                        'for_student' => $student_id
                    ]);

                    NotificationHelper::sendManualAssessmentNotification($assessment_user);
                }
            }

            return redirect(route('schedule.show', [$schedule_id, 'nav' => 'assessment']))->with('success', __('messages.add-assessment-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store_result(Request $request, $assessment_user_id)
    {
        try {
            $assessment_user = AssessmentUsers::find($assessment_user_id);
            $assessment = $assessment_user->assessment;
            $questions = $assessment->assessment_questions;

            // Validate Inputs
            $errors = [];
            $inputs = [];
            foreach($questions as $question)
            {
                if($question->type == 'rating')
                {
                    $request_name = 'question_'.$question->id;
                    $answer_value = $request->$request_name;
                    $inputs[$question->id] = $answer_value;
                    if($question->is_required && !$answer_value)
                    {
                        $errors[$question->id] = __('messages.please-select-appropriate-rating');
                    }
                }  
                else if($question->type == 'option')
                {
                    $request_name = 'question_'.$question->id;
                    $answer_value = $request->$request_name;
                    $inputs[$question->id] = $answer_value;
                    if($question->is_required && !$answer_value)
                    {
                        $errors[$question->id] = __('messages.please-select-atleast-one-option');
                    }
                }  
                else if($question->type == 'comment')
                {
                    $request_name = 'question_'.$question->id;
                    $answer_value = $request->$request_name;
                    $inputs[$question->id] = $answer_value;
                    if($question->is_required && !$answer_value)
                    {
                        $errors[$question->id] = __('messages.please-fill-out-this-field');
                    }
                }  
                else if($question->type == 'availability-selection-calender')
                {
                    $request_name = 'timeslots_'.$question->id;
                    $timeslot_ids = (array)$request->$request_name;
                    $inputs[$question->id] = implode(",",$timeslot_ids);
                    if($question->is_required && count($timeslot_ids) == 0)
                    {
                        $errors[$question->id] = __('messages.please-select-atleast-one-timeslot');
                    }
                }  
                else if($question->type == 'textfield')
                {
                    $request_name = 'question_'.$question->id;
                    $answer_value = $request->$request_name;
                    $inputs[$question->id] = $answer_value;
                    if($question->is_required && !$answer_value)
                    {
                        $errors[$question->id] = __('messages.please-fill-out-this-field');
                    }
                }
            }

            $session['errors'] = $errors;
            $session['inputs'] = $inputs;
            if(count($errors) > 0)
            {
                $request->session()->flash('assessment_form_'.$assessment_user->id, $session);
                return redirect()->back();
            }

            // Delete Old Responses. (in case of assessment is being updated by admin)
            $assessment_user->assessment_user_questions()->delete();
            $assessment_user->assessmentUserTimeslots()->delete();

            // Store User Inputs
            foreach($questions as $question) 
            {
                $request_name = 'question_'.$question->id;
                $answer_value = $request->$request_name;               

                if($question->type == 'availability-selection-calender')
                {
                    $request_name = 'timeslots_'.$question->id;
                    $timeslot_ids = (array)$request->$request_name;
                    foreach($timeslot_ids as $timeslot_id)
                    {
                        $assessmentUserTimeslot = new AssessmentUserTimeslot();
                        $assessmentUserTimeslot->assessment_user_id = $assessment_user_id;
                        $assessmentUserTimeslot->assessment_question_id = $question->id;
                        $assessmentUserTimeslot->timeslot_id = $timeslot_id;
                        $assessmentUserTimeslot->save();
                    }
                }
                
                if($answer_value)
                {
                    AssessmentUserQuestions::create([
                        'assessment_user_id' => $assessment_user_id,
                        'assessment_question_id' => $question->id,
                        'value' => $answer_value
                    ]);
                }
            }

            if(!$assessment_user->complete)
            {
                $assessment_user->update(['complete' => AssessmentUsers::COMPLETE_STATUS]);

                // Send email to student who is being assessed.
                if($assessment_user->assessment_for_student)
                {
                    $view_assessment_url = route('student.view_assessment', $assessment_user->id);
                    $student = $assessment_user->assessment_for_student;
                    $class = $assessment_user->schedule->class;
                    NotificationHelper::sendAssessmentResultAvailableNotification($student, $class, $view_assessment_url);
                }

                $message = __('messages.submit-assessment-successfully');
            }
            else
            {
                $message = __('messages.assessment-response-updated-successfully');
            }
            
            if($request->return_url)
            {
                return redirect($request->return_url)->with('success', $message);
            }
            return redirect('user/assessment/list')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($assessment_user_id)
    {
        return view('assessment.assessment_user.details', ['assessment_user' => AssessmentUsers::find($assessment_user_id)]);
    }

    public function destroy($assessment_user_id)
    {
        $assessment_user = AssessmentUsers::findOrFail($assessment_user_id);
        $schedule = $assessment_user->schedule;
        $assessment = $assessment_user->assessment;
        $assessment_user->delete();

        $message = __('messages.assessment-deleted-successfully');
        if(!$schedule)
        {
            return redirect(route('assessment.responses',$assessment->id))->with('success', $message);
        }
        else
        {
            return redirect(route('schedule.show', [$schedule->id, 'nav' => 'assessment']))->with('success', $message);
        }
    }

    public function assignAssessmentData($assessment_id)
    {
        $assessment = Assessments::findOrFail($assessment_id);
        $students =  Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get();
        $final_students = [];
        foreach($students as $student){
            $final_students[] = [
                'id' => $student->id,
                'fullname' => $student->fullname,
                'levels' => $student->levels ? explode(',', $student->levels) : []
            ];
        }

        $out['levels'] = explode(',', Settings::get_value('class_student_levels'));
        $out['students'] = $final_students;
        $out['assessment_name'] = $assessment->name;
        return $out;
    }

    public function assignSubmit(Request $request)
    {
        $students = Students::whereIn('id', $request->selected_students)->select('user_id')->get();
        foreach($students as $student)
        {
            $assessment_user = AssessmentUsers::create([
                'user_id' => $student->user_id,
                'schedule_id' => NULL,
                'assessment_id' => $request->assessment_id,
                'complete' => AssessmentUsers::INCOMPLETE_STATUS
            ]);

            NotificationHelper::sendManualAssessmentNotification($assessment_user);
        }
        $out['status'] = 1;
        return $out;
    }
}
