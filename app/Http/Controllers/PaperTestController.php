<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaperTestRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StudentPaperTestRequest;
use App\Students;
use App\Schedules;
use App\Courses;
use App\Units;
use App\Lessons;
use App\CommentTemplates;
use App\StudentPaperTests;
use App\PaperTests;
use Illuminate\Support\Facades\Storage;

class PaperTestController extends Controller
{
    public function show($id)
    {
        return view('test.paper_test.details', ['paper_test' => PaperTests::find($id)]);
    }

    public function edit($id)
    {
        return view('test.paper_test.edit', [
            'courses' => Courses::all(),
            'units' => Units::all(),
            'lessons' => Lessons::all(),
            'paper_test' => PaperTests::find($id)
        ]);
    }

    public function update(PaperTestRequest $request, $id)
    {
        try {
            PaperTests::find($id)->update([
                'name' => $request->name, 'total_score' => $request->total_score, 'course_id' => $request->course_id,
                'unit_id' => $request->unit_id, 'lesson_id' => $request->lesson_id
            ]);

            return redirect('test/list')->with('success', __('messages.update-papertest-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('success', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        PaperTests::find($id)->delete();

        return redirect('test/list')->with('success', __('messages.delete-papertest-successfully'));
    }

    public function schedule_create($schedule_id)
    {
        $schedule = Schedules::find($schedule_id);
        $students = $schedule->get_students();
        $paper_tests = $schedule->get_paper_tests();

        return view('schedule.details.tabs.paper_test.create', [
            'schedule' => $schedule,
            'students' => $students,
            'paper_tests' => $paper_tests,
            'comment_templates' => CommentTemplates::all()
        ]);
    }

    public function schedule_edit($student_paper_test_id)
    {
        $studentPaperTest = StudentPaperTests::findOrFail($student_paper_test_id);
        $schedule = $studentPaperTest->schedule;
        $students = $schedule->get_students();
        $paper_tests = $schedule->get_paper_tests();

        return view('schedule.details.tabs.paper_test.edit', [
            'schedule' => $schedule,
            'students' => $students,
            'paper_tests' => $paper_tests,
            'comment_templates' => CommentTemplates::all(),
            'studentPaperTest' => $studentPaperTest
        ]);
    }

    public function schedule_store(StudentPaperTestRequest $request, $schedule_id, $student_paper_test_id = NULL)
    {
        if($student_paper_test_id)
        {
            $studentPaperTest = StudentPaperTests::findOrFail($student_paper_test_id);
        }
        else
        {
            $studentPaperTest = new StudentPaperTests();
        }

        try {
            $student = Students::find($request->student_id);
            $paperTest = PaperTests::find($request->paper_test_id);
            $schedule = Schedules::find($schedule_id);
            $comment_en = '';
            $comment_ja = '';

            $file_path = "DONT_UPDATE";
            if($request->remove_old_file == 1 && $studentPaperTest->file_path)
            {
                $file_path = NULL;
                if(Storage::disk('public')->has($studentPaperTest->file_path))
                {
                    Storage::disk('public')->delete($studentPaperTest->file_path);
                }
            }
            $uploaded_file_path = $request->temp_file_path;
            if($uploaded_file_path && Storage::disk('local')->has($uploaded_file_path))
            {
                // Move file from general storage folder to specific folder and remove old file.
                $file_path = 'student_paper_test_files/'.basename($uploaded_file_path);
                Storage::disk('public')->put($file_path, Storage::disk('local')->get($uploaded_file_path));
                Storage::delete($uploaded_file_path);
            }


            if($request->comment_template_id)
            {
                $comment_template = CommentTemplates::find($request->comment_template_id);

                $comment_template->set_class_name($schedule->class->title)
                                ->set_date($request->date)
                                ->set_score($request->score.'/'.$request->total_score)
                                ->set_test($paperTest->name)
                                ->set_comment($request->comment)
                                ->set_student_name($student->get_kanji_name());
                $comment_en = $comment_template->get_format('content_en');
                $comment_ja = $comment_template->get_format('content_ja');
            }
            else
            {
                $comment_en = $request->comment;
                $comment_ja = $request->comment;
            }

            $studentPaperTest->student_id = $request->student_id;
            $studentPaperTest->schedule_id = $schedule_id;
            $studentPaperTest->paper_test_id = $request->paper_test_id;
            $studentPaperTest->date = $request->date;
            $studentPaperTest->score = $request->score;
            $studentPaperTest->total_score = $request->total_score;
            $studentPaperTest->comment_en = $comment_en;
            $studentPaperTest->comment_ja = $comment_ja;
            $studentPaperTest->comment = $request->comment;
            $studentPaperTest->comment_template_id = $request->comment_template_id;

            if($file_path != 'DONT_UPDATE')
            {
                $studentPaperTest->file_path = $file_path;
            }

            $studentPaperTest->save();

            $message = $student_paper_test_id ? __('messages.papertest-updated-successfully') : __('messages.add-papertest-successfully');

            return redirect(route('schedule.show', [$schedule_id, 'nav' => 'papertest']))->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function student_destroy($schedule_id, $student_paper_test_id)
    {
        try {
            StudentPaperTests::deleteWithFiles([$student_paper_test_id]);
            return redirect(route('schedule.show',  [$schedule_id, 'nav' => 'papertest']))->with('success', __('messages.delete-papertest-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function get_total_score(Request $request)
    {
        try {
            $paper_test = PaperTests::find($request->paper_test_id);

            return response()->json(['total_score' => $paper_test->total_score], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
