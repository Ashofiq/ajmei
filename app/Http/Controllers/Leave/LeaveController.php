<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;
use App\Models\Leave\EmpLeaveTypes;
use App\Models\Leave\EmpLeaves;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $rows = EmpLeaves::query()
          ->Join("emp_leave_types as ltypes", "ltypes.id", "=", "leave_type_id")
          ->Join("employees as employees", "employees.id", "=", "leave_emp_id")
          ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
          ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
          ->leftJoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
          ->selectRaw('emp_leaves.id,emp_id_no,leave_type,leave_desc,leave_from_dt,leave_to_dt,leave_days,leave_reasons,emp_name,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section')
          ->where('leave_comp_id', $company_code)->orderBy('employees.id', 'desc')->paginate(10);
        $employees = Employees::query()->where('emp_com_id', $company_code)->orderBy('emp_name','asc')
          ->where('emp_status', 1)->get();
        return view ('/leave/index', compact('rows','employees'));
    }

    public function search(Request $request)
    {
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $employee_id = $request->get('employee_id');
        $employees = Employees::query()->where('emp_com_id', $company_code)->orderBy('emp_name','asc')
        ->where('emp_status', 1)->get(); 
        $rows = EmpLeaves::query()
            ->Join("employees as employees", "employees.id", "=", "vEmpId")
            ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
            ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
            ->selectRaw('emp_manual_attendances.id,vEmpId,dtAttInTime,dtAttOutTime,vAttType,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,emp_joining_dt,emp_joining_salary,emp_present_salary,emp_actual_salary,emp_others_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,dept.vComboName as department') 
            ->where('employees.id', $employee_id)
            ->orderBy('employees.id', 'desc')->paginate(10)->setpath('');

            $rows->appends(array(
            'employee_id' => $employee_id,
            ));
        $collect = collect($rows);
        return view ('/leave/index', compact('rows','employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $dropdownscontroller = new DropdownsController();
        $company_code = $dropdownscontroller->defaultCompanyCode();
        $companies  = $dropdownscontroller->comboCompanyAssignList(); 
        $employees = Employees::query()->orderBy('emp_name','asc')
        ->where('emp_com_id', $company_code)
        ->where('emp_status', 1)->get();
        $leavetypes = EmpLeaveTypes::query()->orderBy('leave_type','asc')
        ->where('ltype_comp_id', $company_code)->get();
        return view('/leave/create_leave',compact('companies','company_code','employees','leavetypes'));
    }
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      // Validate the Field
        $this->validate($request,[
            'company_code'  =>'required',
            'employee_id'   =>'required', 
            'from_date'     =>'required', 
            'to_date'       =>'required', 
            'leave_days'    =>'required',
        ]);

        $inputdata = new EmpLeaves(); 
        $inputdata->leave_comp_id  = $request->company_code;
        $inputdata->leave_emp_id   = $request->employee_id;   
        $inputdata->leave_type_id  = $request->leave_type_id; 
        $inputdata->leave_from_dt  = date('Y-m-d',strtotime($request->from_date));
        $inputdata->leave_to_dt   = date('Y-m-d',strtotime($request->to_date));  
        $inputdata->leave_days    = $request->leave_days; 
        $inputdata->leave_reasons = $request->reasons; 
        $inputdata->save();

        return redirect()->back()->with('message','Leave Created Successfull !')->withInput();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

      $dropdownscontroller = new DropdownsController();
      $generalscontroller = new GeneralsController();

      $company_code = $dropdownscontroller->defaultCompanyCode();
      $companies  = $dropdownscontroller->comboCompanyAssignList();
        
    //   $employees = Employees::query()->orderBy('emp_name','asc')
    //   ->where('emp_status', 1)->get();
    //   $leavetypes = EmpLeaveTypes::query()->orderBy('leave_type','asc')->get();
      
      $employees = Employees::query()->orderBy('emp_name','asc')
        ->where('emp_com_id', $company_code)
        ->where('emp_status', 1)->get();
      $leavetypes = EmpLeaveTypes::query()->orderBy('leave_type','asc')
        ->where('ltype_comp_id', $company_code)->get();
        
      $row = EmpLeaves::query()
        ->Join("emp_leave_types as ltypes", "ltypes.id", "=", "leave_type_id")
        ->Join("employees as employees", "employees.id", "=", "leave_emp_id")
        ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
        ->leftJoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->selectRaw('emp_leaves.*,emp_id_no,leave_type,leave_desc,emp_name,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section')
        ->where('emp_leaves.id', $id)->first();  
      return view('/leave/edit_leave',compact('company_code','row','companies','employees','leavetypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
      $id = $request->leave_edit_id;

      // Validate the Field
      $this->validate($request,[
        'company_code'  =>'required',
        'employee_id'   =>'required', 
        'from_date'     =>'required', 
        'to_date'       =>'required', 
        'leave_days'    =>'required',
      ]);

      $inputdata = EmpLeaves::find($id); 
      $inputdata->leave_comp_id  = $request->company_code;
      $inputdata->leave_emp_id   = $request->employee_id;   
      $inputdata->leave_type_id  = $request->leave_type_id; 
      $inputdata->leave_from_dt  = date('Y-m-d',strtotime($request->from_date));
      $inputdata->leave_to_dt   = date('Y-m-d',strtotime($request->to_date));  
      $inputdata->leave_days    = $request->leave_days; 
      $inputdata->leave_reasons = $request->reasons;  
      $inputdata->save(); 
      return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id)
    {
        //return  $id;
        try{
          EmpLeaves::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }

    public function get_emp_leave_bal_inf($compid,$empid,$leavetypeid){
      //return('ss'. $custid);
       $generalsController = new GeneralsController(); 
       $get_stock = $generalsController->get_leave_stock($compid,$leavetypeid);
       $get_availed = $generalsController->get_leave_availed($compid,$empid,$leavetypeid);
       $balance = $get_stock->days - $get_availed->availed;
       return response()->json($balance);
    }

}
