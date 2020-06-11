<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    public function index(Request $request)
    {
        $default['sort_field'] = 'fullname';
        $default['sort_dir'] = 'asc';

        if($request->sort_field && $request->sort_dir)
        {
            $children_filter['sort_field'] = $request->sort_field;
            $children_filter['sort_dir'] = $request->sort_dir;
            session(['children_filter' => $children_filter]);
        }

        $session_filter = session('children_filter');
        if($session_filter['sort_field'] && $session_filter['sort_dir'])
        {
            $filter['sort_field'] = $session_filter['sort_field'];
            $filter['sort_dir'] = $session_filter['sort_dir'];
        }
        else
        {
            $filter['sort_field'] = $default['sort_field'];
            $filter['sort_dir'] = $default['sort_dir'];
        }

        $childrenQuery = \Auth::user()->children();
        $childrenQuery->selectRaw("students.*,CONCAT(students.firstname,' ',students.lastname) as fullname");
        $childrenQuery->orderBy($filter['sort_field'],$filter['sort_dir']);

        return view('children.index', [
            'children' => $childrenQuery->get(),
            'filter' => $filter,
        ]);
    }
}
