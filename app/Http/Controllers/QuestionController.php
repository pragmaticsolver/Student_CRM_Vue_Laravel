<?php

namespace App\Http\Controllers;

use App\Questions;
use App\Tests;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index()
    {
        return view('test.online_test.question.list', ['questions' => Questions::all()]);
    }

    public function create(Request $request)
    {
        return view('test.online_test.question.create', [
            'tests' => Tests::all(),
            'test_id' => $request->test_id
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(Questions::get_store_validate_params());

        try {
            Questions::create([
                'question' => $request->question,
                'test_id' => $request->test_id,
                'score' => $request->score
            ]);

            return redirect(route('test.show', $request->test_id))->with('success', __('messages.addquestionsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

    }

    public function show($id)
    {
        return view('test.online_test.question.details', ['question' => Questions::find($id)]);
    }

    public function edit($id)
    {
        return view('test.online_test.question.edit', ['question' => Questions::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Questions::get_update_validate_params());

        try {
            $question = Questions::find($id);

            $question->update([
                'question' => $request->question,
                'score' => $request->score
            ]);

            return redirect(route('test.show', $question->test_id))->with('success', __('messages.addquestionsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $question = Questions::find($id);

            $question->answers()->delete();
            $question->delete();

            return redirect()->back()->with('success', __('messages.deletequestionsuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
