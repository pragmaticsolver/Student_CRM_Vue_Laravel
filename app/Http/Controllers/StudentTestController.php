<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Schedules;
use App\StudentTests;
use App\Answers;
use App\Students;
use App\Tests;
use App\CommentTemplates;

class StudentTestController extends Controller
{
    public function store_result(Request $request, $student_test_id)
    {
        try {
            $student_test = StudentTests::find($student_test_id);
            $test = $student_test->test;
            $score = 0;

            foreach($test->questions as $question) {
                $request_name = 'question_'.$question->id;
                $answer_id = $request->$request_name;
                $answer = Answers::find($answer_id);

                if($answer->correct) $score+= $question->score;
            }

            $student_test->update([
                'score' => $score,
                'total_score' => $test->get_total_score(),
                'status' => 1,
                'date' => now()
            ]);

            return redirect('student/online-test/list')->with('success', __('messages.submittestsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $student_test = StudentTests::find($id);
        $schedule_id = $student_test->schedule->id;
        $student_test->delete();

        return redirect(route('schedule.show', $schedule_id))->with('success', __('messages.deletetestsuccessfully'));
    }
}
