<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Role;
use App\Permission;
use App\Settings;
use App\User;
use App\Students;
use App\Category;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();

        $rolecount=array();

        foreach($roles as $key=>$role){
            $rolecount[$role->id] =   User::role($role->name)->count();
        }

        $undeleteable_roles = Role::UNDELETEABLE_ROLES;

        return view('role.list', compact('roles','rolecount', 'undeleteable_roles'));
    }

    public function create()
    {
        return view('role.create',[
            'permissions' => Permission::all(),
            'default_lang' => Settings::get_value('default_lang')
        ]);
    }

    public function store(RoleRequest $request)
    {
        try {
            $role = Role::create([
                'name' => $request->name,
                'login_redirect_path' => $request->login_redirect_path,
                'is_student' => $request->is_student ? 1 : 0,
                'can_login' => $request->can_login ? 1 : 0,
                'send_login_details' => $request->send_login_details ? 1 : 0,
                'default_lang' => $request->default_lang ? $request->default_lang : NULL
            ]);

            if($request->permissions)
            {
                foreach($request->permissions as $permission) {
                    $role->givePermissionTo(Permission::find($permission));
                }
            }

            return redirect()->route('roles.index')->with('success', 'Role '.$role->name.' added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        return redirect('roles');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        $categories = Category::all();
        $roleper = $role->permissions()->pluck('id')->toArray();

        return view('role.edit', compact('role', 'permissions', 'roleper', 'categories'));
    }

    public function getCategories() {
        
        if(isset($_POST['category'])){
            
            $category = $_POST['category'];
            
            $cate = Category::selectRaw('id')->where('name','=',$category)->get();
            $categoryId = json_decode($cate)[0]->id ;
            $permissions = Permission::where('categoryId','=',$categoryId)->get();
            return $permissions;
        }


    }
    public function update(RoleRequest $request, $id)
    {

        try {
            $role = Role::findOrFail($id);

            User::role($role->name)->get()->each(function($user) use ($role, $request) {
                // Delete student records if is_student is changed from checked to unchecked
                if($role->is_student && !$request->is_student) {
                    if($user->student) $user->student->delete();
                // Create student records if is_student is changed from unchecked to checked
                } elseif(!$role->is_student && $request->is_student) {
                    if(!$user->student) Students::createByUser($user);
                }
            });

            $role->update([
                'name' => $request->name,
                'login_redirect_path' => $request->login_redirect_path,
                'is_student' => $request->is_student ? 1 : 0,
                'can_login' => $request->can_login ? 1 : 0,
                'send_login_details' => $request->send_login_details ? 1 : 0,
                'default_lang' => $request->default_lang ? $request->default_lang : NULL
            ]);

            $p_all = Permission::all();

            foreach ($p_all as $p) {
                $role->revokePermissionTo($p);
            }

            if($request->permissions)
            {
                foreach($request->permissions as $permission) {
                    $role->givePermissionTo(Permission::find($permission));
                }
            }

            return redirect()->route('roles.index')->with('success', 'Role '.$role->name.' updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success',
             'Role deleted!');
    }
}
