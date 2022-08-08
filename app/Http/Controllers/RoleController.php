<?php

namespace App\Http\Controllers;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    protected $role;


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addRole(Request $request)
    {

        if($request->input('id'))
        $role = Role::find($request->input('id'));
        else
        $role = new Role();
    
        $validator = Validator::make(
            $request->all(),
            [
                'role_name' => 'required|string|unique:role,role_name,'.$role->id.',id',
                'parent_id'=>'required'

            ]
        );
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $role->role_name = $request->input("role_name");
        $role->assign = $request->input("assign");
        $role->parent_id = $request->input("parent_id");
        $role->save();     
        return response()->json(['message' => 'Role created successfully', 'role' => $role]);
    }

    public function roleList(Request $request)
    {
        $query = Role::select();
        return $this->_getPaginatedResult($query,$request);    
    }

    public function allRole()
    {
        if(auth()->user()->role_id ==1)
        {
            $role = Role::all();
        }
        else 
        {
            $role = Role::where('parent_id',auth()->user()->role_id)->get();
        }
        
        return response()->json($role);
    }

    public function getRole($id)
    {
        $role = Role::find($id);
        return response()->json($role);
    }
}
