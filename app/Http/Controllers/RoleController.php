<?php

namespace App\Http\Controllers;

use App\Http\Controllers\General\GeneralsController;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        //$this->middleware('can:role-list')->except('assignPermission', 'assign');
        //$this->middleware('can:assign-permission')->only('assignPermission', 'assign');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('role.index', ['roles' => Role::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('role.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Role::create($request->all());
        return back()->with('message', 'Role Created Successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('role.edit', ['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
       $id = $request->id;

      // Validate the Field
       $this->validate($request,[
        'name'  =>'required',
       ]);

       $inputdata  = Role::find($id);
       $inputdata->name = $request->name;
       $inputdata->save();

        //$role->update($request->all());
        return back()->with('message', 'Role Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
          Role::where('id',$id)->delete();
        }catch (\Exception $e){
          return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

      //  $role->delete();
      //  return back()->with('message', 'Role Deleted Successfully!');
    }

    public function access_index($id)
    {
      $permission_list =  Permission::query()
      ->where('group', '<>', NULL)
      ->orderBy('group','asc')->get();
      $group = Permission::query()->select('group')->orderBy('group','asc')->distinct()->get();

      $generalscontroller = new GeneralsController();
      $role_name = $generalscontroller->RoleName($id);

      $roles = Role::query()
              ->where('id', $id)->first();

      return view ('/role/assign-permission',
          compact('permission_list','id','role_name','group','roles'));
    }

    public function access_store(Request $request)
    {
      $id = $request->id;
      $permission_list =  Permission::query()
      ->where('group', '<>', NULL)
      ->orderBy('group','asc')->get();
      $group = Permission::query()->select('group')->orderBy('group','asc')->distinct()->get();

      $generalscontroller = new GeneralsController();
      $role_name = $generalscontroller->RoleName($id);

      $role = Role::findOrFail($id);
      if($request->has('permissions')){ 
          $role->name = $request->role_name;
          $role->permissions = json_encode($request->permissions);
          //return $role->permissions;
          $role->save();
        //  flash(translate('Role has been Mapped successfully'))->success();
        //  return redirect()->route('role.index');
      }
      //return back();
      return redirect()->back()->with('message','Role has been Mapped successfully')->withInput();
      //return view ('/role/assign-permission', compact('permission_list','id','role_name','group'));
    }


    public function assignPermission(Request $request, Role $role)
    {
        return view('role.assign-permission', [
            'permissions' => Permission::all(),
            'role'  =>  $role->load('permissions'),
        ]);
    }

    public function assign(Request $request, Role $role)
    {
        $role->permissions()->sync($request->permission_id);
        return back()->with('message', 'Permission Assigned');
    }
}
