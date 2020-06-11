<?php

namespace App\Http\Controllers;

use App\Helpers\AutomatedTagsHelper;
use App\Settings;
use App\Students;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TagsController extends Controller
{
    public function index()
    {
        return view('tags.index');
    }

    public function saveTag(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:191|unique:tags,name,'. $request->id
        ]);

        $tag = $request->id ? Tag::findOrFail($request->id) : new Tag();
        $tag->name = $request->name;
        $tag->color = $request->color;
        $tag->icon = $request->icon;
        $tag->save();

        $tag = Tag::find($tag->id);
        $out['status'] = 1;
        $out['tag'] = $tag;
        return $out;
    }

    public function getTags()
    {
        return Tag::orderBy('is_automated','DESC')->orderBy('id','ASC')->get();
    }

    public function deleteTag($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();
        return $tag;
    }

    public function saveStudentTags(Request $request)
    {
        $student = Students::findOrFail($request->student_id);
        $automated_tag_ids = $student->tags()->onlyAutomated()->pluck('tags.id')->toArray();
        $tags_to_keep = array_merge((array)$request->tag_ids, $automated_tag_ids);
        $student->tags()->sync($tags_to_keep);

        $out['status'] = 1;
        $out['student_tags'] = $student->getTags();
        return $out;
    }

    public function getSettings()
    {
        $out['new_student_tag_attachment_duration_days'] = Settings::get_value('new_student_tag_attachment_duration_days');
        return $out;
    }

    public function saveSettings(Request $request)
    {
        Validator::make($request->all(), [
            'new_student_tag_attachment_duration_days' => 'required|integer|min:0'
        ])->validate();

        Settings::update_value('new_student_tag_attachment_duration_days',$request->new_student_tag_attachment_duration_days);

        $students = Students::all();
        foreach($students as $student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshNewStudentTag();
        }

        $out['status'] = 1;
        return $out;
    }
}
