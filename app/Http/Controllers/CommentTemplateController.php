<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CommentTemplates;

class CommentTemplateController extends Controller
{
    public function index()
    {
        return view('test.paper_test.comment_template.list', ['comment_templates' => CommentTemplates::all()]);
    }

    public function create()
    {
        return view('test.paper_test.comment_template.create');
    }

    public function store(Request $request)
    {
        $request->validate(CommentTemplates::get_validate_params());

        try {
            CommentTemplates::create([
                'name' => $request->name,
                'content_en' => $request->content_en,
                'content_ja' => $request->content_ja
            ]);

            return redirect('comment-template/list')->with('success', __('messages.add-commenttemplate-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        return view('test.paper_test.comment_template.edit', ['comment_template' => CommentTemplates::find($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(CommentTemplates::get_validate_params());

        try {
            CommentTemplates::find($id)->update([
                'name' => $request->name,
                'content_en' => $request->content_en,
                'content_ja' => $request->content_ja
            ]);

            return redirect('comment-template/list')->with('success', __('messages.update-commenttemplate-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $comment_template = CommentTemplates::find($id);
        $comment_template->delete();

        return redirect('comment-template/list')->with('success', __('messages.delete-commenttemplate-successfully'));
    }
}
