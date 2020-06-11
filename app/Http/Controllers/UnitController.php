<?php

namespace App\Http\Controllers;

use App\Units;
use App\Courses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnitController extends Controller
{
    public function index()
    {
        return view('course.unit.list', ['units' => Units::all()]);
    }

    public function create(Request $request)
    {
        return view('course.unit.create', ['courses' => Courses::all(), 'course_id' => $request->course_id]);
    }

    public function store(Request $request)
    {
        $request->validate(Units::get_validate_params());

        try {

            $position = NULL;
            $rec = Units::where('course_id',$request->course_id)->orderBy('position','DESC')->first();
            if($rec && $rec->position != NULL)
            {
                $position = $rec->position + 1;
            }

            Units::create([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'objectives' => $request->objectives,
                'position' => $position
            ]);

            $message = __('messages.addunitsuccessfully');
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
        return view('course.unit.details', ['unit' => Units::find($id)]);
    }

    public function edit($id)
    {
        return view('course.unit.edit', [
            'unit' => Units::find($id),
            'courses' => Courses::all()
        ]);
    }

    public function edit_modal($id)
    {
        return view('course.unit.edit-modal', [
            'unit' => Units::find($id)
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Units::get_validate_params());

        try {
            Units::find($id)->update([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'objectives' => $request->objectives
            ]);

            $message = __('messages.updateunitsuccessfully');
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

    public function destroy($id)
    {
        $unit = Units::find($id);

        // Delete Lesson Files
        foreach($unit->lessons as $lesson)
        {
            foreach($lesson->files as $lessonFile)
            {
                if ($lessonFile->section != 5)
                    @Storage::disk('public')->delete($lessonFile->file_path);
                $lessonFile->delete();
            }
        }

        $unit->delete();

        return redirect('/unit/list')->with('success', __('messages.deleteunitsuccessfully'));
    }

    public function reorderLessonsForm($unit_id)
    {
        $unit = Units::find($unit_id);
        return view('course.unit.lessons-reorder', compact('unit'));
    }

    public function reorderLessonsSave($unit_id, Request $request)
    {
        $unit = Units::find($unit_id);

        $lesson_ids = (array)$request->lesson_ids;
        foreach($lesson_ids as $key => $lesson_id)
        {
            $unit->lessons()->where('id',$lesson_id)->update(['position' => $key]);
        }

        $out = array();
        $out['status'] = 1;
        $out['message'] = __('messages.lessons-reordered-successfully');
        return $out;
    }
}
