<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Courses;
use App\Units;
use App\Lessons;
use App\File;
use App\LessonExercise;
use App\LessonExerciseStatus;
use App\LessonFile;
use App\LessonHomework;
use App\LessonHomeworkStatus;
use Illuminate\Support\Facades\Storage;
use App\Settings;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lessons::all();

        return view('course.unit.lesson.list', ['lessons' => $lessons]);
    }

    public function create(Request $request)
    {
        return view('course.unit.lesson.create', [
            'show_course_selection' => 1,
            'courses' => Courses::all(),
            'units' => Units::orderBy('course_id','ASC')->orderBy('position','ASC')->get(),
            'course_id' => $request->course_id,
            'unit_id' => $request->unit_id
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(Lessons::get_store_validate_params());

        try {
        	$image_name = null;

            if($request->image) {
                $feature_image = (new File())->setFile($request->image)->setPath('lesson/thumbnail/')->setName();
                $image_name = $feature_image->store() ? $feature_image->getName() : null;
            }

            $position = NULL;
            $rec = Lessons::where('unit_id',$request->unit_id)->orderBy('position','DESC')->first();
            if($rec && $rec->position != NULL)
            {
                $position = $rec->position + 1;
            }

        	$lesson = Lessons::create([
	            'title' => $request->title,
	            'course_id' => $request->course_id,
	            'unit_id' => $request->unit_id,
                'description'=> $request->description,
                'objectives'=> $request->objectives,
	            'full_text'=> $request->full_text,
                'thumbnail' => $image_name,
                'position' => $position,
                'student_lesson_prep' => $request->student_lesson_prep,
                'vocab_list' => $request->vocab_list,
                'extra_materials_text' => $request->extra_materials_text,
                'teachers_notes' => $request->teachers_notes,
                'teachers_prep' => $request->teachers_prep
            ]);

            $this->setCommonData($lesson, $request);

            $message = __('messages.addlessonsuccessfully');
            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = $message;
                return $out;
            }

	        return redirect(route('course.show', $request->course_id))->with('success', $message);
        } catch(\Exception $e) {

            if($request->ajax())
            {
                $out['status'] = 0;
                $out['message'] = $e->getMessage();
                return $out;
            }

        	return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $lesson = Lessons::find($id);

        return view('course.unit.lesson.details', ['lesson' => $lesson]);
    }

    public function edit($id)
    {
        $lesson = Lessons::find($id);
        return view('course.unit.lesson.edit', ['lesson' => $lesson]);
    }

    public function edit_fields($id)
    {
        $lesson = Lessons::find($id);
        return view('course.unit.lesson.edit-fields', ['lesson' => $lesson]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Lessons::get_update_validate_params());
        
        try {
            $lesson = Lessons::find($id);
            if(Settings::get_value('downloadable_files_required') && $lesson->downloadableFiles->count() == 0) {
                return redirect()->back()->with('error', __('messages.downloadable_files_is_required')); 
            }
            if(Settings::get_value('pdf_files_required') && $lesson->pdfFiles->count() == 0) {
                return redirect()->back()->with('error', __('messages.pdf_files_is_required')); 
            }
            if(Settings::get_value('audio_files_required') && $lesson->audioFiles->count() == 0) {
                return redirect()->back()->with('error', __('messages.audio_files_is_required')); 
            }
           
            $image_name = null;
            if($request->update_image == 'true' && $request->image) {
                $feature_image = (new File())->setFile($request->image)
                                             ->setPath('lesson/thumbnail/')
                                             ->setName();
                $image_name = $feature_image->store() ? $feature_image->getName() : null;
            }

        	$lesson->title = $request->title;
            $lesson->description = $request->description;
            $lesson->objectives = $request->objectives;
	        $lesson->full_text = $request->full_text;
            $lesson->student_lesson_prep = $request->student_lesson_prep;
            $lesson->vocab_list = $request->vocab_list;
            $lesson->extra_materials_text = $request->extra_materials_text;
            $lesson->teachers_notes = $request->teachers_notes;
            $lesson->teachers_prep = $request->teachers_prep;

	        if($image_name) $lesson->thumbnail = $image_name;

            $lesson->save();

            $this->setCommonData($lesson, $request);

            $message = __('messages.updatelessonsuccessfully');
            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = $message;
                return $out;
            }

	        return redirect(route('course.show', $lesson->course_id))->with('success', $message);
        } catch(\Exception $e) {
            if($request->ajax())
            {
                $out['status'] = 0;
                $out['message'] = $e->getMessage();
                return $out;
            }
        	return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function setCommonData($lesson, $request)
    {
        // sync Lesson exercises
        // remove exercise handling
        $exercies_to_keep = array_filter((array)$request->exercise_ids);
        $lesson->lessonExercises()->whereNotIn('id',$exercies_to_keep)->delete();
        // add or update exercise handling
        if($request->exercise_ids)
        {
            foreach($request->exercise_ids as $key => $exercise_id)
            {
                $lessonExercise = $exercise_id ? LessonExercise::find($exercise_id) : new LessonExercise();
                $lessonExercise->lesson_id = $lesson->id;
                $lessonExercise->title = $request->exercise_titles[$key];
                $lessonExercise->save();
            }
        }

        // Sync Lesson Homework
        // remove homework handling
        $homeworks_to_keep = array_filter((array)$request->homework_ids);
        $lesson->lessonHomeworks()->whereNotIn('id',$homeworks_to_keep)->delete();
        // add or update homework handling
        if($request->homework_ids)
        {
            foreach($request->homework_ids as $key => $homework_id)
            {
                $lessonHomework = $homework_id ? LessonHomework::find($homework_id) : new LessonHomework();
                $lessonHomework->lesson_id = $lesson->id;
                $lessonHomework->title = $request->homework_titles[$key];
                $lessonHomework->save();
            }
        }

        // Sync Lesson Videos
        // remove video handling
        $videos_to_keep = array_filter((array)$request->video_ids);
        $lesson->videoFiles()->whereNotIn('id',$videos_to_keep)->delete();
        // add or update video handling
        if($request->video_ids)
        {
            foreach($request->video_ids as $key => $video_id)
            {
                $lessonVideo = $video_id ? LessonFile::find($video_id) : new LessonFile();
                $lessonVideo->lesson_id = $lesson->id;
                $lessonVideo->file_path = $request->video_links[$key];
                $lessonVideo->file_name = $request->video_names[$key];
                $lessonVideo->section = 5;
                $lessonVideo->save();
            }
        }
    }

    public function destroy($id, Request $request)
    {
        $lesson = Lessons::find($id);

        // Delete Lesson Files
        foreach($lesson->files as $lessonFile)
        {
            if ($lessonFile->section != 5)
                @Storage::disk('public')->delete($lessonFile->file_path);
            $lessonFile->delete();
        }
        $lesson->delete();

        $message = __('messages.deletelessonsuccessfully');
        if($request->ajax())
        {
            $out['status'] = 1;
            $out['message'] = $message;
            return $out;
        }

        return redirect('/lesson/list')->with('success', $message);
    }

    public function update_exercise_status(Request $request)
    {
        $out['status'] = 1;
        $out['message'] = '';
        $out['status_line'] = '';

        $lessonExerciseStatus = LessonExerciseStatus::where('lesson_exercise_id', $request->lesson_exercise_id)
                                    ->where('schedule_id', $request->schedule_id)
                                    ->first();

        if(!$lessonExerciseStatus)
        {
            $lessonExerciseStatus = New LessonExerciseStatus();
            $lessonExerciseStatus->lesson_exercise_id = $request->lesson_exercise_id;
            $lessonExerciseStatus->schedule_id = $request->schedule_id;
        }

        if($request->is_complete == 1)
        {
            if($lessonExerciseStatus->is_complete == 1)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.exercise-is-already-marked-as-complete');
            }
            else
            {
                $lessonExerciseStatus->is_complete = 1;
                $lessonExerciseStatus->updated_by = \Auth::user()->id;
                $lessonExerciseStatus->save();
            }
        }
        else
        {
            if($lessonExerciseStatus->is_complete == 0)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.exercise-is-already-marked-as-incomplete');
            }
            else
            {
                $lessonExerciseStatus->is_complete = 0;
                $lessonExerciseStatus->updated_by = \Auth::user()->id;
                $lessonExerciseStatus->save();
            }
        }

        if($out['status'] == 1)
        {
            $out['status_line'] = $lessonExerciseStatus->getStatusLine();
        }
        return $out;
    }

    public function update_homework_status(Request $request)
    {
        $out['status'] = 1;
        $out['message'] = '';
        $out['status_line'] = '';

        $lessonHomeworkStatus = LessonHomeworkStatus::where('lesson_homework_id', $request->lesson_homework_id)
                                    ->where('schedule_id', $request->schedule_id)
                                    ->first();

        if(!$lessonHomeworkStatus)
        {
            $lessonHomeworkStatus = New LessonHomeworkStatus();
            $lessonHomeworkStatus->lesson_homework_id = $request->lesson_homework_id;
            $lessonHomeworkStatus->schedule_id = $request->schedule_id;
        }

        if($request->is_complete == 1)
        {
            if($lessonHomeworkStatus->is_complete == 1)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.homework-is-already-marked-as-complete');
            }
            else
            {
                $lessonHomeworkStatus->is_complete = 1;
                $lessonHomeworkStatus->updated_by = \Auth::user()->id;
                $lessonHomeworkStatus->save();
            }
        }
        else
        {
            if($lessonHomeworkStatus->is_complete == 0)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.homework-is-already-marked-as-incomplete');
            }
            else
            {
                $lessonHomeworkStatus->is_complete = 0;
                $lessonHomeworkStatus->updated_by = \Auth::user()->id;
                $lessonHomeworkStatus->save();
            }
        }

        if($out['status'] == 1)
        {
            $out['status_line'] = $lessonHomeworkStatus->getStatusLine();
        }
        return $out;
    }

}
