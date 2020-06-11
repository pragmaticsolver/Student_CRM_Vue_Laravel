<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ClassCategoryRequest;
use App\ClassCategories;
use App\Role;

class ClassCategoryController extends Controller
{
    public function index()
    {
        return view('class.category.list', ['categories' => ClassCategories::all()]);
    }

    public function create()
    {
        return view('class.category.create', ['roles' => Role::all()]);
    }

    public function store(ClassCategoryRequest $request)
    {
        try {
            ClassCategories::create([
                'name' => $request->name,
                'visible_user_roles' => json_encode($request->visible_user_roles)
            ]);

            return redirect('/class-category')->with('success', __('messages.create-class-category-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        return view('class.category.edit', ['roles' => Role::all(), 'category' => ClassCategories::find($id)]);
    }

    public function update(ClassCategoryRequest $request, $id)
    {
        try {
            ClassCategories::find($id)->update([
                'name' => $request->name,
                'visible_user_roles' => json_encode($request->visible_user_roles)
            ]);

            return redirect('/class-category')->with('success', __('messages.edit-class-category-successfully'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $category = ClassCategories::find($id);

        return view('class.category.details', [
            'category' => $category,
            'classes' => $category->get_classes(),
            'events' => $category->get_events()
        ]);
    }

    public function destroy($id)
    {
        $class_category = ClassCategories::find($id);
        $class_category->delete();

        return redirect('/class-category')->with('success', __('messages.delete-class-category-successfully'));
    }
}
