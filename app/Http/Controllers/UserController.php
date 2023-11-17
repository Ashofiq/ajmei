<?php

namespace App\Http\Controllers;

use App\Models\CompaniesAssigns;
use App\Models\Warehouse\WarehouseAssigns;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\User;
use App\Models\UsersMappingSps;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Role;
use App\Rules\OldPasswordRule;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        //$this->middleware('can:user-list')->except('passChangeShowForm', 'updatePassword');
        //$this->middleware('can:change-password')->only('passChangeShowForm', 'updatePassword');
    }

    public function index()
    {
        $users = User::orderBy('id', 'desc')->paginate();
        return view('user.index', [
            'users' => $users,
            'roles' => Role::all(),
            'combo' => $users,
        ]);
    }

    public function create()
    {
      $dropdownscontroller = new DropdownsController();
      $companieslist  = $dropdownscontroller->comboCompanyList(0);

        return view('user.create', [
            'users' => User::orderBy('id', 'desc')->paginate(),
            'roles' => Role::all(),
            'companieslist' => $companieslist,
        ]);
    }

    public function edit(User $user)
    {
        return view('user.edit', ['user' => $user]);
    }

    public function update(Request $request, User $user)
    {
        $user_id = $request->id;
        $inputdata  = User::find($user_id);
        $inputdata->name     = $request->name;
        $inputdata->email    = $request->email;
        $inputdata->role_id  = $request->role_id;
        $inputdata->save();

      /*  $user->update(
          ['name' => $request->name,
          'email' => $request->email,
          'role_id' => $request->role_id]
        );*/
        return back()->with('message', 'User Role Updated');
    }

    public function store(Request $request)
    {
        $this->validate($request, ['password' => 'required|min:4|confirmed']);
        $user = User::create(array_merge($request->except(['password']),['password' => Hash::make($request->password)]));
        
        $inputdata  = User::find($user->id);
        $inputdata->role_id  = $request->role_id;
        $inputdata->save();
        
          WarehouseAssigns::create([
          'w_a_comp_id' => $request->company_code,
          'w_user_id'   => $user->id,
          'w_ref_id'    => 1,
          'default'     => 1,
        ]);
  
        $inputdata = new CompaniesAssigns();
        $inputdata->comp_id   = $request->company_code;
        $inputdata->user_id   = $user->id;
        $inputdata->default   = 1;
        $inputdata->save();
        return back()->with('message', 'User Created Successfully!');
    }

    public function passChangeShowForm(User $user)
    {
        return view('password', ['user' => $user,'own' => 0]);
    }

    public function updatePassword(Request $request, User $user)
    {
        $this->validate($request, [
            'password' => 'required|min:4|confirmed',
            //'old_password' => ['required', new OldPasswordRule($user->password)],
        ]);
        $user->update(['password'=> Hash::make($request->password)]);
        return back()->with('message', 'Password Changed!');
    }
    
    public function u_search(Request $request){
      $user_id = $request->input('user_id');
      $combo = User::orderBy('id', 'desc')->paginate();
      if($user_id != '-1'){
        $rows = User::where('id', $user_id)->orderBy('id', 'desc')->paginate(10)->setpath('');
        $rows->appends(array(
          'user_id' => $user_id,
        ));
        $collect = collect($rows);
      }else {
        $rows = User::orderBy('id', 'desc')->paginate(10);
      }
      return view('user.index', [
          'users' => $rows,
          'roles' => Role::all(),
          'combo' => $combo,
      ]);
    }
    
    public function u_sp_index($id)
    {
        $generalscontroller = new GeneralsController();
        $UserName = $generalscontroller->UserName($id);
        $userssp = CustomerSalesPersons::query()
            ->join("sys_infos", "sys_infos.id", "=", "customer_sales_persons.sales_desig")
            ->join("users_mapping_sps", "users_mapping_sps.sp_ref_id", "=", "customer_sales_persons.id")
            ->selectRaw('users_mapping_sps.id as u_spid,customer_sales_persons.id,
            sales_name,sales_desig,vComboName,sales_mobile,sales_email')
            ->where('users_mapping_sps.u_user_id', $id)->get();

        return view('user.user_sp_index', [
            'user_id' => $id,
            'user_name' => $UserName,
            'userssp' => $userssp,
            'salespersons' => CustomerSalesPersons::all(),
        ]);
    }

    public function u_sp_store(Request $request)
    {
      UsersMappingSps::create([
        'u_user_id' => $request->user_id,
        'sp_ref_id' => $request->sp_id,
      ]);
      return back()->with('message', 'SP Added Successful.');
    }

    public function destroy($id)
    {
        try{
            UsersMappingSps::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');
    }
    
}
