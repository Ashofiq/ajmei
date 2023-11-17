<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\General\DropdownsController;
use App\Http\Controllers\General\GeneralsController;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use Carbon\Carbon;

use App\Models\Employees\Employees;
use App\Models\Leave\EmpLeaves;

use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getEmpLeaveData(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      if($request->filled('company_code')){
        $company_code = $request->get('company_code');
      }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
      } 
      $generalscontroller  = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($company_code);
      
      $q = EmpLeaves::query() 
      ->join("emp_leave_types", "emp_leave_types.id", "=", "leave_type_id")
      ->join("employees as emp", "emp.id", "=", "leave_emp_id")
      ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
      ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
      ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
      ->where('leave_comp_id', $company_code) 
      ->selectRaw('leave_emp_id,emp_id_no,emp_name,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section,emp_joining_dt,leave_type_id,leave_type,leave_from_dt,leave_to_dt,leave_days');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
    
    if($request->filled('fromdate')){
      $fromdate  = date('Y-m-d',strtotime($request->get('fromdate')));
      $q->where('leave_to_dt','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
    }
    
    if($request->filled('todate')){
      $todate = date('Y-m-d',strtotime($request->get('todate')));
      $q->where('leave_from_dt','<=', date('Y-m-d',strtotime($request->get('todate'))));
    }
    

    $q->orderBy('dept.vComboName', 'asc'); 
    $q->orderBy('sec.vComboName', 'asc');
    $rows = $q->orderBy('leave_to_dt', 'asc')->get();

     if ($request->input('submit') == "pdf"){
      $fileName = 'leave_rpt';
      $pdf = PDF::loadView('/leave/reports/rpt_emp_leave_entry_data_pdf',
      compact('rows','companies','company_code','comp_name',), [], [
           'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
    }

    $collect = collect($rows);
    // get requested action
    return view('/leave/reports/rpt_emp_leave_entry_data', compact('rows','companies','company_code','comp_name','fromdate','todate'));

  }

   public function getEmpLeaveSummaryData(Request $request)
   {
      $dropdownscontroller = new DropdownsController();
      $companies    = $dropdownscontroller->comboCompanyAssignList();
      if($request->filled('company_code')){
        $company_code = $request->get('company_code');
      }else{
        $company_code = $dropdownscontroller->defaultCompanyCode();
      } 
      $generalscontroller  = new GeneralsController();
      $comp_name = $generalscontroller->CompanyName($company_code);
      
      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
            
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate'))); 
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate'))); 
      }

      $sql = "SELECT leave_emp_id,emp_id_no,emp_name,desig.vComboName as designation,dept.vComboName as department,sec.vComboName as section,emp_joining_dt,
       SUM(CASE WHEN leave_type ='CL' THEN leave_days END) AS CL_DAYS,  
       SUM(CASE WHEN leave_type ='EL' THEN leave_days END) AS EL_DAYS,
       SUM(CASE WHEN leave_type ='SL' THEN leave_days END) AS SL_DAYS,
       SUM(CASE WHEN leave_type ='ML' THEN leave_days END) AS ML_DAYS,
       SUM(leave_days) AS TOTAL
      FROM emp_leaves  
      inner join emp_leave_types on emp_leave_types.id = leave_type_id
      inner join employees on employees.id = leave_emp_id
      inner join sys_infos desig on desig.id = emp_desig_ref_id
      inner join sys_infos dept on dept.id = emp_dept_ref_id
      inner join sys_infos sec on sec.id = emp_sec_ref_id  
      Where leave_comp_id = $company_code AND leave_to_dt >= '$fromdate' and leave_from_dt <= '$todate'
      GROUP BY leave_emp_id,emp_id_no,emp_name,desig.vComboName,dept.vComboName,sec.vComboName,emp_joining_dt
      Order BY dept.vComboName, sec.vComboName asc";
      $rows = DB::select($sql);
      
     if ($request->input('submit') == "pdf"){
      $fileName = 'leave_rpt';
      $pdf = PDF::loadView('/leave/reports/rpt_emp_leave_summary_pdf',
      compact('rows','companies','company_code','comp_name',), [], [
           'title' => $fileName,
     ]);
     return $pdf->stream($fileName,'.pdf');
    }

    $collect = collect($rows);
    // get requested action
    return view('/leave/reports/rpt_emp_leave_summary', compact('rows','companies','company_code','comp_name','fromdate','todate'));

  }
   

}
