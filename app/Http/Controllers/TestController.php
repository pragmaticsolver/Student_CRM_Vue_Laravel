<?php

namespace App\Http\Controllers;

use App\Tests;
use App\PaperTests;
use App\Courses;
use App\Units;
use App\Lessons;
use App\Http\Requests\TestRequest;
use App\Http\Requests\OnlineTestRequest;

class TestController extends Controller
{
    public function index()
    {
        return view('test.list', [
            'tests' => Tests::all(),
            'paper_tests' => PaperTests::all()
        ]);
    }

    public function create()
    {
        return view('test.create', [
            'courses' => Courses::all(),
            'units' => Units::all(),
            'lessons' => Lessons::all()
        ]);
    }

    public function store(TestRequest $request)
    {
        try {
            switch($request->test_type) {
                case 'online':
                    Tests::create([
                        'name' => $request->name,
                        'course_id' => $request->course_id,
                        'unit_id' => $request->unit_id,
                        'lesson_id' => $request->lesson_id
                    ]);
                    break;
                case 'paper':
                    PaperTests::create([
                        'name' => $request->name,
                        'course_id' => $request->course_id,
                        'unit_id' => $request->unit_id,
                        'lesson_id' => $request->lesson_id,
                        'total_score' => $request->total_score
                    ]);
            }

            return redirect('/test/list')->with('success', __('messages.addtestsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        return view('test.online_test.details', ['test' => Tests::find($id)]);
    }

    public function edit($id)
    {
        return view('test.online_test.edit', [
            'test' => Tests::find($id),
            'courses' => Courses::all(),
            'units' => Units::all(),
            'lessons' => Lessons::all()
        ]);
    }

    public function update(OnlineTestRequest $request, $id)
    {
        try {
            Tests::find($id)->update([
                'name' => $request->name,
                'course_id' => $request->course_id,
                'unit_id' => $request->unit_id,
                'lesson_id' => $request->lesson_id
            ]);

            return redirect('/test/list')->with('success', __('messages.updatetestsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $test = Tests::find($id);
        $test->delete();

        return redirect()->back()->with('success', __('messages.deletetestsuccessfully'));
    }
}
