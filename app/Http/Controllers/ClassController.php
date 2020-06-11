<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Classes;
use App\Settings;
use App\ClassCategories;
use App\Courses;
use App\Helpers\CommonHelper;
use App\Http\Requests\ClassRequest;
use Carbon\Carbon;

class ClassController extends Controller
{
    public function index()
    {
        $default_size = Settings::get_value('limit_number_of_students_per_class');
        $classes = Classes::allClasses();
        return view('class.list', array('classes' => $classes, 'default_size' => $default_size));
    }

    public function create(Request $request)
    {
        $plans = DB::table('payment_plans')->get();
        return view('class.create', array(
            'plans' => $plans,
            'default_size' => Settings::get_value('limit_number_of_students_per_class'),
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels')),
            'categories' => ClassCategories::all(),
            'category_id' => $request->category_id ? $request->category_id : null,
            'default_class_length' => Settings::get_value('default_class_length'),
            'courses' => Courses::all(),
            'use_points' => Settings::get_value('use_points')
        ));
    }

    public function store(ClassRequest $request)
    {
        try {
            Classes::create([
                'title' => $request->get('title'),
                'payment_plan_id'=> $request->get('payment_plan_id'),
                'status'=> 0, 'class_type' => 0,
                'level' => $request->level,
                'size' => $request->size ? $request->size : null,
                'category_id' => $request->category_id,
                'length' => $request->length,
                'default_course_id' => $request->default_course_id ? $request->default_course_id : NULL
            ]);

            return redirect('/class')->with('success', 'Class has been added!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $classes = DB::table('classes')->where('id','=',$id)->get();
        $date = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d');
        return view('class.details', array('class' => $classes[0], 'date' => $date));
    }

    public function edit($id)
    {
        $classes = DB::table('classes')->where('id','=',$id)->get();
        return view('class.edit', array(
            'class' => $classes[0],
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels')),
            'categories' => ClassCategories::all(),
            'courses' => Courses::all(),
        ));
    }

    public function update(ClassRequest $request, $id)
    {
        try {
            Classes::find($id)->update([
                'size' => $request->size ? $request->size : null,
                'title' => $request->title,
                'level' => $request->level,
                'category_id' => $request->category_id,
                'length' => $request->length,
                'default_course_id' => $request->default_course_id ? $request->default_course_id : NULL
            ]);

            return redirect('/class/'.$id.'/edit')->with('success', 'Class has been updated!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $class = Classes::find($id);
        $class->delete();

        return redirect('/class')->with('success', 'Class has been deleted successfully!');
    }
}
