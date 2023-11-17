<?php

namespace App\Http\Controllers\Attendance;

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

use App\Models\Attendance\EmpRawAttendances;
use App\Models\Attendance\EmpAttendhistories;
use App\Models\Employees\Employees;

use PDF;
use Response;

class ReportController extends Controller
{

   public $presentDate;

   public function __construct()
   {

   }

   public function getRawAttendanceData(Request $request)
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
    
      $employees = Employees::query()
      //->where('emp_com_id','=',$company_code)
      ->orderBy('emp_com_id','asc')->get();
      
      $q = EmpRawAttendances::query()
        ->leftjoin("employees as emp", "emp_secret_id", "=", "vFingerId")
        ->leftjoin("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
        ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
        ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
        ->selectRaw('vFingerId,dtCheckInTime,emp_id_no,emp_name,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
      $employee_id = '';

      if($request->filled('employee_id')){ 
        $employee_id = $request->get('employee_id');
        $q->where('emp_id_no','=',$employee_id);
      }
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $q->whereDate('dtCheckInTime','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->whereDate('dtCheckInTime','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
  
    $rows = $q->orderBy('dtCheckInTime', 'desc')->distinct()->get();
 
    $collect = collect($rows);
    // get requested action
    return view('/attendance/reports/rpt_raw_attendance_data', compact('rows','companies','company_code','comp_name','fromdate','todate','employee_id','employees'));

  }
  
  public function getAttendErrorData(Request $request)
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
     
      $q = EmpAttendhistories::query()
          ->join("employees as emp", "emp.id", "=", "vEmpid")
          ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
          ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
          ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
          ->where('att_his_comp_id', $company_code) 
          ->where('tInTime','<>', '00:00:00')
          ->whereRaw('tInTime = tOutTime')
          ->selectRaw('emp_attendhistories.*,emp_id_no,emp_name,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

      $fromdate = date('Y-m-d');
      $todate  = date('Y-m-d');
            
      if($request->filled('fromdate')){
        $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
        $q->where('attDate','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
      }
      if($request->filled('todate')){
        $todate  = date('Y-m-d',strtotime($request->get('todate')));
        $q->where('attDate','<=', date('Y-m-d',strtotime($request->get('todate'))));
      }
 
    $rows = $q->orderBy('attDate', 'asc')->get();
 
    $collect = collect($rows);
    // get requested action
    return view('/attendance/reports/rpt_error_attendance_data', compact('rows','companies','company_code','comp_name','fromdate','todate'));

  }
  
  public function getAttendPresentData(Request $request)
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
    
     $q = EmpAttendhistories::query()
      ->join("employees as emp", "emp.id", "=", "vEmpid")
       ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
       ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
       ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
       ->where('emp_com_id', $company_code)
       ->where('tInTime','<>', '00:00:00')
       ->selectRaw('emp_attendhistories.*,emp_id_no,emp_name,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
           
     if($request->filled('fromdate')){
       $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
       $q->where('attDate','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
     }
     if($request->filled('todate')){
       $todate  = date('Y-m-d',strtotime($request->get('todate')));
       $q->where('attDate','<=', date('Y-m-d',strtotime($request->get('todate'))));
     }
    
    $q->orderBy('dept.vComboName', 'asc'); 
    $q->orderBy('sec.vComboName', 'asc');
    $rows = $q->orderBy('attDate', 'asc')->get();


    if ($request->input('submit') == "pdf"){
     $fileName = 'customer_list';
     $pdf = PDF::loadView('/attendance/reports/rpt_dept_sec_wise_list_pdf',
     compact('rows','companies','company_code','comp_name',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }

   $collect = collect($rows);
   // get requested action
   return view('/attendance/reports/rpt_att_present_list', compact('rows','companies','company_code','comp_name','fromdate','todate'));

 }
 
 public function getAttendAbsentData(Request $request)
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
    
     $q = EmpAttendhistories::query()
      ->join("employees as emp", "emp.id", "=", "vEmpid")
       ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
       ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
       ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
       ->where('emp_com_id', $company_code)
       ->where('tInTime','=', '00:00:00')
       ->where('iLeave','=', '0')
       ->selectRaw('emp_attendhistories.*,emp_id_no,emp_name,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

     $fromdate = date('Y-m-d');
     $todate  = date('Y-m-d');
           
     if($request->filled('fromdate')){
       $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
       $q->where('attDate','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
     }
     if($request->filled('todate')){
       $todate  = date('Y-m-d',strtotime($request->get('todate')));
       $q->where('attDate','<=', date('Y-m-d',strtotime($request->get('todate'))));
     }
 
    $q->orderBy('dept.vComboName', 'asc'); 
    $q->orderBy('sec.vComboName', 'asc');
    $rows = $q->orderBy('attDate', 'asc')->get();


    if ($request->input('submit') == "pdf"){
     $fileName = 'customer_list';
     $pdf = PDF::loadView('/attendance/reports/rpt_dept_sec_wise_list_pdf',
     compact('rows','companies','company_code','comp_name',), [], [
          'title' => $fileName,
    ]);
    return $pdf->stream($fileName,'.pdf');
   }

   $collect = collect($rows);
   // get requested action
   return view('/attendance/reports/rpt_att_absent_list', compact('rows','companies','company_code','comp_name','fromdate','todate'));

 }
 
 public function getJobCardReport(Request $request)
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
    
    $employees = Employees::query()
      //->where('emp_com_id','=',$company_code)
      ->orderBy('emp_com_id','asc')->get();

    $q = EmpAttendhistories::query()
      ->join("employees as emp", "emp.id", "=", "vEmpid")
      ->join("sys_infos as desig", "desig.id", "=", "emp_desig_ref_id")
      ->leftjoin("sys_infos as sec", "sec.id", "=", "emp_sec_ref_id")
      ->leftjoin("sys_infos as dept", "dept.id", "=", "emp_dept_ref_id") 
      ->where('emp_com_id', $company_code)
      ->selectRaw('emp_attendhistories.*,DAY(LAST_DAY(attDate)) as no_days,emp_id_no,emp_name,emp_joining_dt,desig.vComboName as designation,sec.vComboName as section,dept.vComboName as department');

    $fromdate = date('Y-m-d');
    $todate  = date('Y-m-d');
    $employee_id = '';

    if($request->filled('employee_id')){ 
      $employee_id = $request->get('employee_id');
      $q->where('emp_id_no','=',$employee_id);
    }

    if($request->filled('fromdate')){
      $fromdate = date('Y-m-d',strtotime($request->get('fromdate')));
      $q->where('attDate','>=', date('Y-m-d',strtotime($request->get('fromdate'))));
    }
    if($request->filled('todate')){
      $todate  = date('Y-m-d',strtotime($request->get('todate')));
      $q->where('attDate','<=', date('Y-m-d',strtotime($request->get('todate'))));
    }

    $q->orderBy('dept.vComboName', 'asc'); 
    $q->orderBy('sec.vComboName', 'asc');
    $q->orderBy('vEmpid', 'asc');
    $rows = $q->orderBy('attDate', 'asc')->get();


   if ($request->input('submit') == "pdf"){
      $fileName = 'job_card';
      $pdf = PDF::loadView('/attendance/reports/rpt_emp_job_card_pdf',
      compact('rows','companies','company_code','comp_name','fromdate','todate','employee_id','employees',), [], [
        'title' => $fileName,
      ]);
      return $pdf->stream($fileName,'.pdf');
    }


  $collect = collect($rows);
  // get requested action
  return view('/attendance/reports/rpt_emp_job_card', compact('rows','companies','company_code','comp_name','fromdate','todate','employee_id','employees'));
  }

   

}
