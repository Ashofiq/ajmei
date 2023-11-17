<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Models\Companies;
use App\Models\Chartofaccounts;
use App\Models\Customers\Customers;
use App\Models\Customers\CustomerContacts;
use App\Models\Salespersons\CustomerSalesPersons;
use App\Models\Customers\CustomerDeliveryInfs;

use App\Models\Settings\SysInfos;
use App\Models\Settings\DropdownTypes;
use App\Models\Employees\Employees;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Response;
use Validator;

class EmployeesController extends Controller
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
        $rows = Employees::query()
          ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
          ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
          ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
          ->selectRaw('employees.id,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,
          emp_joining_dt,emp_joining_salary,emp_present_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,
          dept.vComboName as department,sec.vComboName as section')
         ->where('employees.emp_com_id', $company_code)->orderBy('employees.id', 'desc')->paginate(10);

        $employees = Employees::query()->where('employees.emp_com_id', $company_code)->orderBy('emp_name','asc')->get();
        return view ('/employees/index', compact('rows','employees'));
    }

    public function search(Request $request)
    {
      $employee_id = $request->get('employee_id');
      $employees = Employees::query()->orderBy('emp_name','asc')->get();  
      $rows = Employees::query()
        ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id")
        ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->selectRaw('employees.id,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,emp_joining_dt,emp_joining_salary,emp_present_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section')
        ->where('employees.id', $employee_id)
        ->orderBy('employees.id', 'desc')->paginate(10)->setpath('');

        $rows->appends(array(
          'employee_id' => $employee_id,
        ));
        $collect = collect($rows);
        return view ('/employees/index', compact('rows','employees'));
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
        
        $dropdowntypes = DropdownTypes::query()->orderBy('vDropDownName','asc')->get();
        $sysinfos   = SysInfos::query()->whereNotIn('vComboName', ['Department', 'Designation', 'Shift', 'Section', 'Unit']) 
        ->orderBy('vComboName','asc')->get();

        return view('/employees/create',compact('companies','company_code','sysinfos','dropdowntypes'));
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
        // $validator  =  Validator::make($request->all(),[
        //     'company_code' =>'required',
        //     'id_no' =>'required',
        //     'employee_name'    =>'required',
        //     'designation'    =>'required',
        //     'section' =>'required',
        //     'department'    =>'required',
        //   //  'section_category'    =>'required',
        //     'shift' =>'required',
        //     'joining_date' =>'required', 
        //     'birth_date'    =>'required',
        //     'joining_salary' =>'required',
        //     'salary_grade' =>'required',
        //     'pay_type' =>'required',
        // ]);

        // if ($validator->fails()) {
        //   return (object) array('message' => $validator, 'success' => false);
        // }



        $inputdata = new Employees();
        $inputdata->emp_com_id      = $request->company_code; 
        $inputdata->emp_id_no       = $request->id_no; 
        $inputdata->emp_uid_no_old  = $request->uid_no_old; 
        $inputdata->emp_old_id_no   = $request->old_id_no; 
        $inputdata->emp_secret_id   = $request->secret_id; 
        $inputdata->emp_name        = $request->employee_name; 
        $inputdata->emp_bank_acc_no = $request->bank_account_no; 
        $inputdata->emp_national_id = $request->national_id; 
        $inputdata->emp_skill_grade = $request->skill_grade; 
        $inputdata->emp_desig_ref_id = $request->designation; 
        $inputdata->emp_sec_ref_id = $request->section; 
        $inputdata->emp_dept_ref_id = $request->department; 
        $inputdata->emp_sec_cat_ref_id = $request->section_category; 
        $inputdata->emp_type_ref_id = $request->type; 
        $inputdata->emp_shift_ref_id = $request->shift; 
        $inputdata->emp_paytype = $request->pay_type; 
        $inputdata->	emp_unit_ref_id = $request->unit; 
        // $inputdata->emp_cat_ref_id = $request->category1;
        $inputdata->emp_outmark_ref_id = $request->out_mark; 
        $inputdata->emp_ottype_ref_id = $request->ot_type; 
        $inputdata->emp_resign_ref_id = $request->resign_status; 
        $inputdata->emp_birth_dt = date('Y-m-d',strtotime($request->birth_date));  
        $inputdata->emp_joining_dt = date('Y-m-d',strtotime($request->joining_date));  
        $inputdata->emp_joining_salary = $request->joining_salary; 
        $inputdata->emp_present_salary = $request->present_salary; 
        // $inputdata->emp_actual_salary = $request->actual_salary;
        // $inputdata->emp_others_salary = $request->others_salary;
        $inputdata->emp_sal_grade = $request->salary_grade;
        $inputdata->emp_promo_date = $request->promotion_date !=''?date('Y-m-d',strtotime($request->promotion_date)):NULL;  
        $inputdata->emp_out_date = $request->out_date !=''?date('Y-m-d',strtotime($request->out_date)):NULL;
        $inputdata->remark = $request->remark;
        $inputdata->save();

        // if($inputdata->save()){
        //   return  array('message' => '', 'success' => true);
        // }else{
        //   return array('message' => '', 'success' => false);
        // }

        
        return redirect()->back()->with('message','Employee Created Successfull !')->withInput();
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */ 
    public function employee_m_view($id)
    {   

        $rows_m = Employees::query()
        ->leftJoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftJoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
        ->leftJoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->leftJoin("sys_infos as seccat", "seccat.id", "=", "emp_sec_cat_ref_id")
        ->leftJoin("sys_infos as type", "type.id", "=", "emp_type_ref_id")
        ->leftJoin("sys_infos as shift", "shift.id", "=", "emp_shift_ref_id")
        ->leftJoin("sys_infos as paytype", "paytype.id", "=", "emp_paytype")
        ->leftJoin("sys_infos as outmark", "outmark.id", "=", "emp_outmark_ref_id")
        ->leftJoin("sys_infos as ottype", "ottype.id", "=", "emp_ottype_ref_id")
        ->leftJoin("sys_infos as resign", "resign.id", "=", "emp_resign_ref_id")
        ->where('employees.id', $id)
        ->selectRaw('employees.id,emp_id_no,emp_uid_no_old,emp_old_id_no,emp_secret_id,emp_name,emp_bank_acc_no,emp_national_id,emp_skill_grade,emp_birth_dt,emp_joining_dt,emp_joining_salary,emp_present_salary,emp_sal_grade,emp_promo_date,emp_out_date,desig.vComboName as designation,dept.vComboName as department,
        sec.vComboName as section,seccat.vComboName as sec_category,type.vComboName as type,shift.vComboName as shift,paytype.vComboName as paytype,
        outmark.vComboName as outmark,ottype.vComboName as ottype,resign.vComboName as resign')->first(); 
        return view('employees.employee_viewmodal',compact('rows_m'));
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
        
      $dropdowntypes = DropdownTypes::query()->orderBy('vDropDownName','asc')->get();
      $sysinfos   = SysInfos::query()->orderBy('vComboName','asc')->get();

      $row = Employees::query()->selectRaw('employees.*')
      ->where('employees.id', $id)->first(); 

      return view('/employees/edit',compact('company_code','row','companies','dropdowntypes','sysinfos'));
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
      
      $id = $request->employee_edit_id;

    //  // Validate the Field
    //    $this->validate($request,[
    //       'company_code'  =>'required',
    //       'id_no'         =>'required',
    //       'employee_name' =>'required',
    //       'designation'   =>'required',
    //       'section'       =>'required',
    //       'department'    =>'required',
    //       'section_category' =>'required',
    //       'shift'         =>'required',
    //       'joining_date'  =>'required', 
    //       'birth_date'    =>'required',
    //       'joining_salary' =>'required',
    //       'salary_grade'  =>'required',
    //      // 'pay_type'      =>'required',
    //    ]);

       $inputdata  = Employees::find($id);
       $inputdata->emp_com_id      = $request->company_code; 
       $inputdata->emp_id_no       = $request->id_no; 
       $inputdata->emp_uid_no_old  = $request->uid_no_old; 
       $inputdata->emp_old_id_no   = $request->old_id_no; 
       $inputdata->emp_secret_id   = $request->secret_id; 
       $inputdata->emp_name        = $request->employee_name; 
       $inputdata->emp_bank_acc_no = $request->bank_account_no; 
       $inputdata->emp_national_id = $request->national_id; 
       $inputdata->emp_skill_grade = $request->skill_grade; 
       $inputdata->emp_desig_ref_id = $request->designation; 
       $inputdata->emp_sec_ref_id = $request->section; 
       $inputdata->emp_dept_ref_id = $request->department; 
       $inputdata->emp_sec_cat_ref_id = $request->section_category; 
       $inputdata->emp_type_ref_id = $request->type; 
       $inputdata->emp_shift_ref_id = $request->shift; 
     //  $inputdata->emp_paytype_ref_id = $request->pay_type; 
     //  $inputdata->emp_cat_ref_id = $request->category1;
     //  $inputdata->emp_outmark_ref_id = $request->out_mark; 
      $inputdata->emp_ottype_ref_id = $request->ot_type; 
      // $inputdata->emp_resign_ref_id = $request->resign_status; 
       $inputdata->emp_birth_dt = date('Y-m-d',strtotime($request->birth_date));  
       $inputdata->emp_joining_dt = date('Y-m-d',strtotime($request->joining_date));  
       $inputdata->emp_joining_salary = $request->joining_salary; 
       $inputdata->emp_present_salary = $request->present_salary; 
       $inputdata->remark = $request->remark; 
      //  $inputdata->emp_actual_salary = $request->actual_salary;
      //  $inputdata->emp_others_salary = $request->others_salary;
       $inputdata->emp_sal_grade = $request->salary_grade;
     //  $inputdata->emp_promo_date = $request->promotion_date !=''?date('Y-m-d',strtotime($request->promotion_date)):NULL; 
      // $inputdata->emp_out_date = $request->out_date !=''?date('Y-m-d',strtotime($request->out_date)):NULL;
       $inputdata->save();

      return redirect()->back()->with('message','Update Successfull');
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
          Employees::where('id',$id)->delete();
        }catch (\Exception $e){
            return redirect()->back()->with('error',$e->getMessage());
        }
        return redirect()->back()->with('message','Deleted Successfull');

    }
    
    public function get_employee_inf($compid,$empid){
      //return('ss'. $custid);
       $generalsController = new GeneralsController(); 
       $getinformation = $generalsController->getEmployeeDetailsInf($compid,$empid);
       return response()->json($getinformation);
    }

}
