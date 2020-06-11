<?php

namespace App\Http\Controllers;

use App\Answers;
use App\Tests;
use App\Questions;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    public function create(Request $request)
    {
        return view('test.online_test.question.answer.create', [
            'tests' => Tests::all(),
            'questions' => Questions::all(),
            'test_id' => $request->test_id,
            'question_id' => $request->question_id
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(Answers::get_store_validate_params());

        try {
            Answers::create([
                'answer' => $request->answer,
                'question_id' => $request->question_id,
                'test_id' => $request->test_id,
                'order' => $request->order,
                'correct' => $request->correct ? 1 : 0
            ]);

            return redirect(route('question.show', $request->question_id))->with('success', __('messages.addanswersuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        return view('test.online_test.question.answer.edit', ['answer' => Answers::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Answers::get_update_validate_params());

        try {
            $answer = Answers::find($id);

            $answer->update([
                'answer' => $request->answer,
                'order' => $request->order,
                'correct' => $request->correct ? 1 : 0
            ]);

            return redirect(route('question.show', $answer->question_id))->with('success', __('messages.updateanswersuccessfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $answer = Answers::find($id);
        $answer->delete();

        return redirect()->back()->with('success', __('messages.updateanswersuccessfully'));
    }
}
