<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ScheduleLessons;
use App\StudentTests;
use App\AssessmentLessons;
use App\AssessmentUsers;
use App\Helpers\NotificationHelper;
use Illuminate\Support\Carbon;

class ScheduleLessonController extends Controller
{
    public function complete(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        try {
            $schedule_id = $request->schedule_id;
            $lesson_id = $request->lesson_id;

            $schedule_lesson = ScheduleLessons::firstOrNew([
                'schedule_id' => $schedule_id,
                'lesson_id' => $lesson_id,
            ]);           
            
            if($schedule_lesson->complete != 1) // First time
            {
                $schedule_lesson->complete = 1;
                $schedule_lesson->date = $request->date;
                $schedule_lesson->save();

                $lesson = $schedule_lesson->lesson;
                $schedule = $schedule_lesson->schedule;

                $valid_yoyakus = $schedule_lesson->get_valid_yoyakus();

                // Assessment Actions
                if($lesson->assessment_lessons->count() > 0) {
                    foreach($lesson->assessment_lessons as $assessment_lesson) {
                        // Create assessment record and send notification to all registered students
                        if($assessment_lesson->send_to == AssessmentLessons::SEND_TO_STUDENT) {
                            foreach($valid_yoyakus as $yoyaku) {
                                $user = $yoyaku->student->user;

                                $assessment_user = AssessmentUsers::create([
                                    'user_id' => $user->id, 'schedule_id' => $schedule->id,
                                    'assessment_id' => $assessment_lesson->assessment->id, 'complete' => AssessmentUsers::INCOMPLETE_STATUS
                                ]);
                                NotificationHelper::sendAutomaticAssessmentNotification($assessment_user,$schedule_lesson);
                            }
                        // Create assessment record and send notification to teacher
                        } else {
                            $user = $schedule->teacher->user;

                            $assessment_user = AssessmentUsers::create([
                                'user_id' => $user->id, 'schedule_id' => $schedule->id,
                                'assessment_id' => $assessment_lesson->assessment->id, 'complete' => AssessmentUsers::INCOMPLETE_STATUS
                            ]);

                            NotificationHelper::sendAutomaticAssessmentNotification($assessment_user,$schedule_lesson);
                        }
                    }
                }

                // Test Actions
                if($lesson->tests->count() > 0) {
                    // Create test record and send notification to all registered students
                    foreach($lesson->tests as $test) {
                        foreach($valid_yoyakus as $yoyaku) {
                            $student = $yoyaku->student;

                            $student_test = StudentTests::create([
                                'test_id' => $test->id, 'schedule_id' => $schedule->id,
                                'student_id' => $student->id, 'status' => StudentTests::INCOMPLETE_STATUS
                            ]);

                            NotificationHelper::sendTestNotification($student_test, $schedule_lesson);
                        }
                    }
                }

                // Paper Test Actions
                if($lesson->paper_tests->count() > 0) {
                    foreach($lesson->paper_tests as $paper_test) {
                        NotificationHelper::sendPaperTestNotification($paper_test, $schedule_lesson);
                    }
                }
            }
            else
            {
                $schedule_lesson->date = $request->date;
                $schedule_lesson->save();
            }

            return redirect()->back()->with('success', __('messages.updatelessonsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $schedule_lesson = ScheduleLessons::find($id);
        $schedule_lesson->delete();

        return redirect()->back()->with('success', __('messages.deletelessonsuccessfully'));
    }

    public function saveComments(Request $request)
    {
        $schedule_id = $request->schedule_id;
        $lesson_id = $request->lesson_id;

        $scheduledLesson = ScheduleLessons::firstOrNew([
            'schedule_id' => $schedule_id,
            'lesson_id' => $lesson_id,
        ]);

        $scheduledLesson->comments = $request->comments;
        $scheduledLesson->comment_updated_at = Carbon::now()->format('Y-m-d H:i:s');
        $scheduledLesson->comment_updated_by = \Auth::user()->id;
        $scheduledLesson->save();

        $out['status'] = 1;
        $out['comments_status_line'] = $scheduledLesson->getCommentStatusLine();
        return $out;
    }
}
